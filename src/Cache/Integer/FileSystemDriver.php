<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Hyperf\Cache\Exception\CacheException;
use Hyperf\Utils\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;

class FileSystemDriver extends Driver
{

    /**
     * @var string
     */
    protected $storePath = BASE_PATH . '/runtime/integers';

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);
        if (! file_exists($this->storePath)) {
            $results = mkdir($this->storePath, 0755, true);
            if (! $results) {
                throw new CacheException('Has no permission to create integer directory!');
            }
        }
        $this->filesystem = $container->get(Filesystem::class);
    }


    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1)
    {
        $file = $this->getCacheKey($key);
        $integer = 0;
        $ttl = 0;
        if(is_file($file)){
            $reader = new FileIntegerReader($file);
            if(!$reader->isExpired()){
                $ttl = $reader->getExpiredTime();
                $integer = $reader->getContent();
            }
        }
        $integer += $step;
        $this->putContent($file, $integer, $ttl);
        return $integer;
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1)
    {
        return $this->inc($key, -$step);
    }

    /**
     * @inheritDoc
     */
    public function get($key, callable $callable)
    {
        $file = $this->getCacheKey($key);
        if(is_file($file)){
            $reader = new FileIntegerReader($file);
            if(!$reader->isExpired()){
                return $reader->getContent();
            }
        }
        $integer = (int)call_user_func($callable);
        $this->set($key, $integer);
        return $integer;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $file = $this->getCacheKey($key);
        return $this->putContent($file, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        $file = $this->getCacheKey($key);
        if (file_exists($file)) {
            if (!is_writable($file)) {
                return false;
            }
            unlink($file);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        $file = $this->getCacheKey($key);
        if( !is_file($file) ){
            return false;
        }
        $reader = new FileIntegerReader($file);
        return $reader->isExpired();
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl)
    {
        $file = $this->getCacheKey($key);
        if(is_file($file)){
            $reader = new FileIntegerReader($file);
            if(!$reader->isExpired()){
                $this->putContent($file, $reader->getContent(), $ttl);
            }
        }
    }

    /**
     * @param string $file
     * @param int $value
     * @param int $ttl
     * @return bool
     */
    protected function putContent($file, $value, $ttl){
        if($ttl > 0){
            $seconds = time() + $ttl;
        }else{
            $seconds = 0;
        }
        return (bool)file_put_contents($file, sprintf('%d.%d', $seconds, $value));
    }

    /**
     * 获取缓存文件名
     * @param string $key
     * @return string
     */
    public function getCacheKey(string $key)
    {
        return $this->getPrefix() . $key . '.integer';
    }

    /**
     * 获取前缀
     * @return string
     */
    protected function getPrefix()
    {
        return $this->storePath . DIRECTORY_SEPARATOR . $this->prefix;
    }
}