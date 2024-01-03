<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Madoka\Cache\Driver\DriverInterface;
use Hyperf\Cache\Driver\DriverInterface as HyperfDriverInterface;
use Generator;

/**
 * 缓存执行器
 * Class CacheActuator
 * @package Madoka\Cache
 * @method mixed get(string $key, mixed $default = null) 获取缓存
 * @method bool set(string $key, mixed $value, int $ttl = null) 设置缓存
 * @method bool delete(string $key) 删除缓存
 * @method bool has(string $key) 缓存是否存在
 * @method bool clear() 清空缓存
 * @method Generator getKeys() 获取当前所有缓存key迭代器
 * @method Generator getValues() 获取当前所有缓存内容迭代器
 * @method int count() 获取当前所有缓存的计数
 * @method array getMultiple(array $keys, mixed $default = null) 批量获取缓存
 * @method bool setMultiple(array $values, int $ttl = null) 批量设置缓存
 * @method bool deletedMultiple(array $keys) 批量删除缓存
 * @method array fetch(string $key, mixed $default) 获取缓存存在状态和数据
 */
class CacheActuator
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
            case 'get':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->get(...$arguments);
                break;
            case 'set':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->set(...$arguments);
                break;
            case 'delete':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->delete(...$arguments);
                break;
            case 'has':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->has(...$arguments);
                break;
            case 'clear':
                $result = $this->driver->clearPrefix($this->join());
                break;
            case 'count':
                $result = $this->driver->countPrefix($this->join());
                break;
            case 'getKeys':
                $result = $this->driver->getPrefixKeys($this->join());
                break;
            case 'getValues':
                $result = $this->driver->getMultiplePrefix($this->join());
                break;
            case 'getMultiple':
                $keys = $arguments[0];
                $arguments[0] = array_map(function ($key) {
                    return $this->name($key);
                }, $keys);
                $result = [];
                $values = $this->driver->getMultiple(...$arguments);
                $index = 0;
                foreach ($values as $value){
                    $result[$keys[$index]] = $value;
                    $index++;
                }
                break;
            case 'setMultiple':
                $cacheKeys = [];
                foreach ($arguments[0] as $key => $value) {
                    $cacheKeys[$this->name($key)] = $value;
                }
                $arguments[0] = $cacheKeys;
                $result = $this->driver->setMultiple(...$arguments);
                break;
            case 'deleteMultiple':
                $arguments[0] = array_map(function ($key) {
                    return $this->name($key);
                }, $arguments[0]);
                $result = $this->driver->deleteMultiple(...$arguments);
                break;
            case 'fetch':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->fetch(...$arguments);
                break;
        }
        return $result;
    }
}