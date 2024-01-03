<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use Generator;
use Hyperf\Cache\Driver\Driver;
use APCUIterator;

/**
 * Class ApcuDriver
 * @package Madoka\Cache
 */
class ApcuDriver extends Driver implements DriverInterface
{
    /**
     * @inheritDoc
     */
    public function get($key, $default = null)
    {
        $key = $this->getCacheKey($key);
        $data = apcu_fetch( $key, $success);
        if( $success ){
            return $data;
        }else{
            return $default;
        }
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        $key = $this->getCacheKey($key);
        $ttl = $this->secondsUntil($ttl);
        return (bool)apcu_store($key, $value, $ttl);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        $key = $this->getCacheKey($key);
        return (bool) apcu_delete($key);
    }

    /**
     * @return bool
     */
    public function clear()
    {
        return $this->clearPrefix('');
    }

    /**
     * @inheritDoc
     */
    public function getMultiple($keys, $default = null)
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $val) {
            $result = $this->set($key, $val, $ttl);

            if (false === $result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $result = $this->delete($key);

            if (false === $result) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        $key = $this->getCacheKey($key);
        return (bool)apcu_exists($key);
    }

    /**
     * @inheritDoc
     */
    public function fetch(string $key, $default = null): array
    {
        $key = $this->getCacheKey($key);
        $data = apcu_fetch($key, $success);
        if( $success ){
            return [true, $data];
        }else{
            return [false, $default];
        }
    }

    /**
     * @inheritDoc
     */
    public function clearPrefix(string $prefix): bool
    {
        $prefix = empty($prefix) ? '' : $prefix . DriverInterface::SEPARATOR;
        $key = $this->getCacheKey($prefix);
        if(empty($key)){
            return apcu_clear_cache();
        }
        $iterator = new APCUIterator("/^$key/", APC_ITER_ALL);
        return (bool) apcu_delete($iterator);
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix) : Generator
    {
        $key = $this->getCacheKey($prefix . DriverInterface::SEPARATOR);
        $length = strlen($key);
        $iterator = new APCUIterator("/^$key/", APC_ITER_ALL);
        foreach ($iterator as $item){
            yield substr($item['key'], $length) => $item['value'];
        }
    }

    /**
     * @inheritDoc
     */
    public function getPrefixKeys(string $prefix): Generator
    {
        $key = $this->getCacheKey($prefix . DriverInterface::SEPARATOR);
        $length = strlen($key);
        $iterator = new APCUIterator("/^$key/", APC_ITER_ALL);
        foreach ($iterator as $item){
            yield substr($item['key'], $length);
        }
    }

    /**
     * @inheritDoc
     */
    public function countPrefix(string $prefix): int
    {
        $key = $this->getCacheKey($prefix . DriverInterface::SEPARATOR);
        $APCUIterator = new APCUIterator("/^$key/", APC_ITER_ALL);
        return $APCUIterator->getTotalCount();
    }
}