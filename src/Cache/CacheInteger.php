<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Madoka\Cache\Integer\IntegerInterface;

/**
 * Class CacheInteger
 * @package Madoka\Cache
 * @method int inc(string $key, int $step = 1, int $ttl = null)
 * @method int dec(string $key, int $step = 1, int $ttl = null)
 * @method int get(string $key, callable $callable)
 * @method bool set(string $key, int $value, int $ttl = null)
 * @method bool delete(string $key)
 * @method bool has(string $key)
 */
class CacheInteger
{
    use NamespaceTrait;

    /**
     * @var IntegerInterface
     */
    protected $driver;

    public function __call($name, $arguments)
    {
        $result = 0;
        switch ($name){
            case 'inc':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->inc(...$arguments);
                break;
            case 'dec':
                $arguments[0] = $this->name($arguments[0]);
                $result = $this->driver->dec(...$arguments);
                break;
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
        }
        return $result;
    }
}