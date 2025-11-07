<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Generator;
use Hyperf\Cache\Exception\CacheException;
use Madoka\Cache\PrefixCacheInterface;
use Madoka\Memory\ThreadMapManager;
use Psr\Container\ContainerInterface;
use Swoole\Thread\Map;

class SwooleThreadMapDriver extends Driver
{
    protected Map $map;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);
        if(!isset($config['map_name']) || !ThreadMapManager::has($config['map_name'])){
            throw new CacheException('Swoole Thread Map not exists!');
        }
        $this->map = ThreadMapManager::get($config['map_name']);
    }

    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1): int
    {
        return $this->map->incr($this->getCacheKey($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1): int
    {
        return $this->map->decr($this->getCacheKey($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, callable $callable): int
    {
        $cacheKey = $this->getCacheKey($key);
        if ($this->map->offsetExists($cacheKey)) {
            return $this->map->offsetGet($cacheKey);
        }else {
            $int = (int)$callable();
            $this->map->add($cacheKey, $int);
            return $int;
        }
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, int $value, int $ttl = null): bool
    {
        $cacheKey = $this->getCacheKey($key);
        if ($this->map->offsetExists($cacheKey)) {
            return $this->map->update($cacheKey, $value);
        }else {
            return $this->map->add($cacheKey, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        $this->map->offsetUnset($this->getCacheKey($key));
        return true;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->map->offsetExists($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl): void
    {
        // 不需要操作，swoole thread map不支持过期时间设置
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): Generator
    {
        $cacheKey = $this->getCacheKey($prefix) . PrefixCacheInterface::SEPARATOR;
        $prefixLength = strlen($cacheKey);
        foreach ($this->map->keys() as $key) {
            if (str_contains($key, $cacheKey)) {
                yield substr($key, $prefixLength) => $this->map->offsetGet($key);
            }
        }
    }
}