<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use APCUIterator;
use Madoka\Cache\PrefixCacheInterface;

class ApcuDriver extends AbstractDriver
{
    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1): int
    {
        return apcu_inc($this->getCacheKey($key), $step, $success);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1): int
    {
        return apcu_inc($this->getCacheKey($key), $step, $success);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, callable $callable): int
    {
        return apcu_entry($this->getCacheKey($key), function () use ($callable) {
            return (int)call_user_func($callable);
        });
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null): bool
    {
        return (bool)apcu_store($this->getCacheKey($key), $value, (int)$ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key): bool
    {
        return (bool)apcu_delete($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function has($key): bool
    {
        return (bool)apcu_exists($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function expire(string $key, int $ttl): void
    {
        $key = $this->getCacheKey($key);
        $data = apcu_fetch($key, $success);
        if ($success) {
            apcu_store($key, $data, $ttl);
        }
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): iterable
    {
        $key = $this->getCacheKey($prefix . PrefixCacheInterface::SEPARATOR);
        $length = strlen($key);
        $iterator = new APCUIterator("/^$key/", APC_ITER_ALL);
        foreach ($iterator as $item) {
            yield substr($item['key'], $length) => $item['value'];
        }
    }
}