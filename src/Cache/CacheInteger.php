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
    protected IntegerInterface $driver;

    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1): int
    {
        return $this->driver->inc($this->name($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1): int
    {
        return $this->driver->dec($this->name($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, callable $callable): int
    {
        return $this->driver->get($this->name($key), $callable);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, int $value, int $ttl = null): bool
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
    public function has(string $key): bool
    {
        return $this->driver->has($this->name($key));
    }

    /**
     * @inheritDoc
     */
    public function expire(string $key, int $ttl): void
    {
        $this->driver->expire($this->name($key), $ttl);
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): iterable
    {
        yield from $this->driver->getMultiplePrefix(empty($prefix) ? $this->join() : $this->name($prefix));
    }
}