<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Hyperf\Redis\RedisFactory;
use Madoka\Cache\PrefixCacheInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Redis\Redis;

class RedisDriver extends Driver
{
    /**
     * @var Redis
     */
    protected Redis $redis;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);

        $this->redis = $container->get(RedisFactory::class)->get($config['options']['pool'] ?? 'default');
    }

    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1): int
    {
        return $this->redis->incrBy($this->getCacheKey($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1): int
    {
        return $this->redis->decrBy($this->getCacheKey($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, callable $callable): int
    {
        $key = $this->getCacheKey($key);
        $result = $this->redis->get($key);
        if($result === false){
            $int = $callable();
            $this->set($key, $int);
            return $int;
        }
        return (int)$result;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, int $value, int $ttl = null): bool
    {
        return $this->redis->set($this->getCacheKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return (bool)$this->redis->del($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->redis->exists($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function expire(string $key, int $ttl): void
    {
        $this->redis->expire($this->getCacheKey($key), $ttl);
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): iterable
    {
        $cacheKey = sprintf('%s%s', $this->getCacheKey($prefix), PrefixCacheInterface::SEPARATOR);
        $match = sprintf('%s*', $cacheKey);
        $iterator = null;
        $prefixLength = strlen($cacheKey);
        while (false !== ($keys = $this->redis->scan($iterator, $match, PrefixCacheInterface::MATCH_CHUNK_SIZE))) {
            $values = $this->redis->mget($keys);
            foreach ($keys as $i => $key) {
                if ($values[$i] !== false) {
                    yield substr($key, $prefixLength) => $values[$i];
                }
            }
        }
    }
}