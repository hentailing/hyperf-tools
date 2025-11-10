<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use Hyperf\Cache\Driver\Driver;
use Hyperf\Cache\Exception\CacheException;
use Hyperf\Support\Filesystem\Filesystem;
use Madoka\Cache\PrefixCacheInterface;
use Psr\Container\ContainerInterface;
use GlobIterator;

class FileSystemDriver extends Driver implements DriverInterface
{

    /**
     * @var string
     */
    protected string $storePath = BASE_PATH . '/runtime/caches';

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    private string $suffix;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);
        if (!file_exists($this->storePath)) {
            $results = mkdir($this->storePath, 0755, true);
            if (!$results) {
                throw new CacheException('Has no permission to create cache directory!');
            }
        }
        $this->suffix = $config['suffix'] ?? '.cache';
        $this->filesystem = $container->get(Filesystem::class);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $file = $this->getCacheKey($key);
        if (!is_file($file)) {
            return $default;
        }
        $reader = new FileCacheReader($file);
        if ($reader->isExpired()) {
            return $default;
        }
        return $this->packer->unpack($reader->getContent());
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $file = $this->getCacheKey($key);
        $content = $this->packer->pack($value);
        $ttl = $this->secondsUntil($ttl);
        if ($ttl > 0) {
            $seconds = time() + $ttl;
        } else {
            $seconds = 0;
        }
        $result = $this->filesystem->put($file, FileCacheReader::pack($seconds, $content));

        return (bool)$result;
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
    public function clear(): bool
    {
        return $this->clearPrefix('');
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $file = $this->getCacheKey($key);
        if (!is_file($file)) {
            return false;
        }
        $reader = new FileCacheReader($file);
        return $reader->isExpired();
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $key, mixed $default = null): array
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
        $globIterator = new GlobIterator($this->getPrefix() . $prefix . '*' . $this->suffix);
        foreach ($globIterator as $splFileInfo) {
            if ($splFileInfo->isDir()) {
                continue;
            }
            $this->filesystem->delete($splFileInfo->getPathname());
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function getPrefixKeys(string $prefix): iterable
    {
        $cacheKey = sprintf('%s%s%s', $this->getPrefix(), $prefix, PrefixCacheInterface::SEPARATOR);
        $prefixLength = strlen(sprintf('%s%s%s', $this->prefix, $prefix, PrefixCacheInterface::SEPARATOR));
        $iterator = new GlobIterator($cacheKey . '*' . $this->suffix);
        foreach ($iterator as $file) {
            $reader = new FileCacheExpireReader($file);
            if (!$reader->isExpired()) {
                yield substr($file->getFilename(), $prefixLength, -6);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): iterable
    {
        $cacheKey = sprintf('%s%s%s', $this->getPrefix(), $prefix, PrefixCacheInterface::SEPARATOR);
        $prefixLength = strlen(sprintf('%s%s%s', $this->prefix, $prefix, PrefixCacheInterface::SEPARATOR));
        $iterator = new GlobIterator($cacheKey . '*' . $this->suffix);
        foreach ($iterator as $file) {
            $reader = new FileCacheReader($file);
            if (!$reader->isExpired()) {
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
        $cacheKey = sprintf('%s%s%s', $this->getPrefix(), $prefix, PrefixCacheInterface::SEPARATOR);
        $iterator = new GlobIterator($cacheKey . '*' . $this->suffix);
        foreach ($iterator as $file) {
            $reader = new FileCacheExpireReader($file);
            if (!$reader->isExpired()) {
                $count++;
            }
        }
        return $count;
    }

    public function getCacheKey(string $key): string
    {
        return $this->getPrefix() . $key . $this->suffix;
    }

    protected function getPrefix(): string
    {
        return $this->storePath . DIRECTORY_SEPARATOR . $this->prefix;
    }
}