<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Madoka\Cache\Driver\DriverInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 缓存执行器
 * Class CacheActuator
 * @package Madoka\Cache
 */
class CacheActuator implements CacheInterface
{
    use NamespaceTrait;

    /**
     * @var DriverInterface
     */
    protected DriverInterface $driver;

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver->get($this->name($key), $default);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        return $this->driver->set($this->name($key), $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return $this->driver->delete($this->name($key));
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        return $this->driver->clearPrefix($this->join());
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $cacheKeys = [];
        foreach ($keys as $key){
            $cacheKeys[] = $this->name($key);
        }
        $result = [];
        $values = $this->driver->getMultiple($cacheKeys, $default);
        foreach ($cacheKeys as $index => $cacheKey){
            $result[$keys[$index]] = $values[$cacheKey];
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(iterable $values, null|int|\DateInterval $ttl = null): bool
    {
        $newValues = [];
        foreach ($values as $key => $value){
            $newValues[$this->name($key)] = $value;
        }
        return $this->driver->setMultiple($newValues, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $cacheKeys = [];
        foreach ($keys as $key){
            $cacheKeys[] = $this->name($key);
        }
        return $this->driver->deleteMultiple($cacheKeys);
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->driver->has($this->name($key));
    }

    /**
     * 获取当前所有缓存的计数
     * @return int
     */
    public function count(): int
    {
        return $this->driver->countPrefix($this->join());
    }

    /**
     * 获取当前所有缓存key迭代器
     * @return iterable<string>
     */
    public function getKeys(): iterable
    {
        yield from $this->driver->getPrefixKeys($this->join());
    }

    /**
     * 获取当前所有缓存内容迭代器
     * @return iterable<string, mixed>
     */
    public function getValues(): iterable
    {
        yield from $this->driver->getPrefixKeys($this->join());
    }

    /**
     * Return state of existence and data at the same time.
     */
    public function fetch(string $key, mixed $default = null): array
    {
        return $this->driver->fetch($this->name($key), $default);
    }

    /**
     * Clean up data of the same prefix.
     */
    public function clearPrefix(string $prefix): bool
    {
        return $this->driver->clearPrefix($this->name($prefix));
    }
}