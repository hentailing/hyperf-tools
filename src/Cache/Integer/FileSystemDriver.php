<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use GlobIterator;
use Hyperf\Cache\Exception\CacheException;
use Hyperf\Support\Filesystem\Filesystem;
use Madoka\Cache\PrefixCacheInterface;
use Psr\Container\ContainerInterface;

class FileSystemDriver extends AbstractDriver
{
    /**
     * @var string
     */
    protected string $storePath = BASE_PATH . '/runtime/integers';

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    private string $suffix;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);
        if (! file_exists($this->storePath)) {
            $results = mkdir($this->storePath, 0755, true);
            if (! $results) {
                throw new CacheException('Has no permission to create integer directory!');
            }
        }
        $this->suffix = $config['suffix'] ?? '.integer';
        $this->filesystem = $container->get(Filesystem::class);
    }


    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1): int
    {
        $file = $this->getCacheKey($key);
        $integer = 0;
        $seconds = 0;
        if(is_file($file)){
            $reader = new FileIntegerReader($file);
            if(!$reader->isExpired()){
                $seconds = $reader->getExpiredTime();
                $integer = $reader->getContent();
            }
        }
        $integer += $step;
        $this->filesystem->put($file, FileIntegerReader::pack($seconds, $integer));
        return $integer;
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1): int
    {
        return $this->inc($key, -$step);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, callable $callable): int
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
    public function set(string $key, int $value, int $ttl = null): bool
    {
        $file = $this->getCacheKey($key);
        return $this->putContent($file, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        $file = $this->getCacheKey($key);
        if (file_exists($file)) {
            if (!is_writable($file)) {
                return false;
            }
            return $this->filesystem->delete($file);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
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
    public function expire(string $key, int $ttl): void
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
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): iterable
    {
        $cacheKey = $this->getPrefix() . $prefix . PrefixCacheInterface::SEPARATOR;
        $prefixLength = strlen($this->prefix . $prefix . PrefixCacheInterface::SEPARATOR);
        $iterator = new GlobIterator($cacheKey . '*' . $this->suffix);
        foreach ($iterator as $file) {
            $reader = new FileIntegerReader($file);
            if (!$reader->isExpired()) {
                yield substr($file->getFilename(), $prefixLength, -6) => $reader->getContent();
            }
        }
    }

    /**
     * @param string $file
     * @param int $value
     * @param int $ttl
     * @return bool
     */
    protected function putContent(string $file, int $value, int $ttl): bool
    {
        if($ttl > 0){
            $seconds = time() + $ttl;
        }else{
            $seconds = 0;
        }

        return (bool)$this->filesystem->put($file, FileIntegerReader::pack($seconds, $value));
    }

    /**
     * 获取缓存文件名
     * @param string $key
     * @return string
     */
    public function getCacheKey(string $key): string
    {
        return $this->getPrefix() . $key . $this->suffix;
    }

    /**
     * 获取前缀
     * @return string
     */
    protected function getPrefix(): string
    {
        return $this->storePath . DIRECTORY_SEPARATOR . $this->prefix;
    }
}