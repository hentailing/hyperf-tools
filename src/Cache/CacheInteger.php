<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Madoka\Cache\Integer\IntegerInterface;

/**
 * Class CacheInteger
 * @package Madoka\Cache
 */
class CacheInteger implements IntegerInterface
{
    use NamespaceTrait;

    /**
     * @var IntegerInterface
     */
    protected $driver;

    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1)
    {
        return $this->driver->inc($this->name($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1)
    {
        return $this->driver->dec($this->name($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function get($key, callable $callable)
    {
        return $this->driver->get($this->name($key), $callable);
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
    public function has($key)
    {
        return $this->driver->has($this->name($key));
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl)
    {
        $this->driver->expire($this->name($key), $ttl);
    }
}