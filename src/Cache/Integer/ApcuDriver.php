<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

class ApcuDriver extends Driver
{
    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1)
    {
        return apcu_inc($this->getCacheKey($key), $step, $success);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1)
    {
        return apcu_inc($this->getCacheKey($key), $step, $success);
    }

    /**
     * @inheritDoc
     */
    public function get($key, callable $callable)
    {
        return apcu_entry($this->getCacheKey($key), function () use ($callable){
            return (int)call_user_func($callable);
        });
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        return (bool)apcu_store($this->getCacheKey($key), $value, (int)$ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return (bool)apcu_delete($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return (bool)apcu_exists($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl)
    {
        $key = $this->getCacheKey($key);
        $data = apcu_fetch($key, $success);
        if($success){
            apcu_store($key, $data, $ttl);
        }
    }
}