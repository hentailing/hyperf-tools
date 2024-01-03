<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Madoka\Cache\Driver\DriverInterface;
use Hyperf\Cache\Driver\DriverInterface as HyperfDriverInterface;
use Generator;
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
     * @var HyperfDriverInterface|DriverInterface
     */
    protected $driver;

    public function __call($name, $arguments)
    {
        $result = null;
        switch ($name){
            case 'getKeys':
                $result = $this->driver->getPrefixKeys($this->join());
                break;
            case 'getValues':
                $result = $this->driver->getMultiplePrefix($this->join());
                break;
            case 'fetch':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->fetch(...$arguments);
                break;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        return $this->driver->get($this->name($key), $default);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->driver->set($this->name($key), $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return $this->driver->delete($this->name($key));
    }

    /**
     * @inheritDoc
     */
    public function clear()
    {
        return $this->driver->clearPrefix($this->join());
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
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
    public function setMultiple($values, $ttl = null)
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
    public function deleteMultiple($keys)
    {
        $cacheKeys = [];
        foreach ($keys as $key){
            $cacheKeys[] = $this->name($key);
        }
        return $this->deleteMultiple($cacheKeys);
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return $this->driver->has($this->name($key));
    }

    /**
     * 获取当前所有缓存的计数
     * @return int
     */
    public function count()
    {
        return $this->driver->countPrefix($this->join());
    }

    /**
     * 获取当前所有缓存key迭代器
     * @return Generator
     */
    public function getKeys()
    {
        yield from $this->driver->getPrefixKeys($this->join());
    }

    /**
     * 获取当前所有缓存内容迭代器
     * @return Generator
     */
    public function getValues()
    {
        yield from $this->driver->getPrefixKeys($this->join());
    }

    /**
     * 获取缓存存在状态和数据
     * @param string $key
     * @param mixed $default
     * @return array
     */
    public function fetch(string $key, $default = null)
    {
        return $this->driver->fetch($this->name($key), $default);
    }
}