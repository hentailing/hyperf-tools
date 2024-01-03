<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use Hyperf\Cache\Driver\Driver;
use Hyperf\Cache\Exception\CacheException;
use Hyperf\Cache\Exception\InvalidArgumentException;
use Hyperf\Utils\Filesystem\Filesystem;
use Psr\Container\ContainerInterface;
use Generator;
use GlobIterator;

class FileSystemDriver extends Driver implements DriverInterface
{

    /**
     * @var string
     */
    protected $storePath = BASE_PATH . '/runtime/caches';

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
                throw new CacheException('Has no permission to create cache directory!');
            }
        }
        $this->filesystem = $container->get(Filesystem::class);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $file = $this->getCacheKey($key);
        if( !is_file($file) ){
            return $default;
        }
        $reader = new FileCacheReader($file);
        if( $reader->isExpired() ){
            return $default;
        }
        return $this->packer->unpack($reader->getContent());
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $file = $this->getCacheKey($key);
        $content = $this->packer->pack($value);
        if($ttl > 0){
            $seconds = time() + $ttl;
        }else{
            $seconds = 0;
        }
        $result = $this->filesystem->put($file, sprintf("%010d\n%s", $seconds, $content));

        return (bool) $result;
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
    public function clear()
    {
        return $this->clearPrefix('');
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys)) {
            throw new InvalidArgumentException('The keys is invalid!');
        }

        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values)) {
            throw new InvalidArgumentException('The values is invalid!');
        }
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys)) {
            throw new InvalidArgumentException('The keys is invalid!');
        }

        foreach ($keys as $index => $key) {
            $this->delete($key);
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
        $reader = new FileCacheReader($file);
        return $reader->isExpired();
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $key, $default = null): array
    {
        $file = $this->getCacheKey($key);
        if (!is_file($file)) {
            return [false, $default];
        }
        $reader = new FileCacheReader($file);
        if ($reader->isExpired()) {
            return [false, $default];
        }

        return [true, $this->packer->unpack($reader->getContent())];
    }

    /**
     * @inheritDoc
     */
    public function clearPrefix(string $prefix): bool
    {
        $globIterator = new GlobIterator($this->getPrefix() . $prefix . '*.cache');
        foreach ($globIterator as $splFileInfo) {
            if ($splFileInfo->isDir()) {
                continue;
            }
            unlink($splFileInfo->getPathname());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getPrefixKeys(string $prefix): Generator
    {
        $cacheKey = sprintf('%s%s%s', $this->getPrefix(), $prefix, DriverInterface::SEPARATOR);
        $prefixLength = strlen(sprintf('%s%s%s', $this->prefix, $prefix, DriverInterface::SEPARATOR));
        $iterator = new GlobIterator($cacheKey.'*.cache');
        foreach ($iterator as $file){
            $reader = new FileCacheReader($file);
            if(!$reader->isExpired() ){
                yield substr($file->getFilename(), $prefixLength, -6);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): Generator
    {
        $cacheKey = sprintf('%s%s%s', $this->getPrefix(), $prefix, DriverInterface::SEPARATOR);
        $prefixLength = strlen(sprintf('%s%s%s', $this->prefix, $prefix, DriverInterface::SEPARATOR));
        $iterator = new GlobIterator($cacheKey.'*.cache');
        foreach ($iterator as $file){
            $reader = new FileCacheReader($file);
            if(! $reader->isExpired() ){
                yield substr($file->getFilename(), $prefixLength, -6) => $this->packer->unpack($reader->getContent());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function countPrefix(string $prefix): int
    {
        $count = 0;
        $cacheKey = sprintf('%s%s%s', $this->getPrefix(), $prefix, DriverInterface::SEPARATOR);
        $iterator = new GlobIterator($cacheKey . '*.cache');
        foreach ($iterator as $file){
            $reader = new FileCacheReader($file);
            if(! $reader->isExpired()){
                $count++;
            }
        }
        return $count;
    }

    public function getCacheKey(string $key)
    {
        return $this->getPrefix() . $key . '.cache';
    }

    protected function getPrefix()
    {
        return $this->storePath . DIRECTORY_SEPARATOR . $this->prefix;
    }
}