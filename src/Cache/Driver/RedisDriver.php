<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use Hyperf\Cache\Driver\RedisDriver as BaseRedisDriver;
use Madoka\Cache\PrefixCacheInterface;

class RedisDriver extends BaseRedisDriver implements DriverInterface
{

    /**
     * @inheritDoc
     */
    public function getPrefixKeys(string $prefix): iterable
    {
        $cacheKey = sprintf('%s%s', $this->getCacheKey($prefix), PrefixCacheInterface::SEPARATOR);
        $match = sprintf('%s*', $cacheKey);
        $iterator = null;
        $prefixLength = strlen($cacheKey);
        while (false !== ($keys = $this->redis->scan($iterator, $match, PrefixCacheInterface::MATCH_CHUNK_SIZE))) {
            foreach ($keys as $key) {
                yield substr($key, $prefixLength);
            }
        }
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
                    yield substr($key, $prefixLength) => $this->packer->unpack($values[$i]);
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function countPrefix(string $prefix): int
    {
        $match = sprintf('%s%s*', $this->getCacheKey($prefix), PrefixCacheInterface::SEPARATOR);
        $iterator = null;
        $count = 0;
        while (false !== ($keys = $this->redis->scan($iterator, $match, PrefixCacheInterface::MATCH_CHUNK_SIZE))) {
            $count += count($keys);
        }
        return $count;
    }
}