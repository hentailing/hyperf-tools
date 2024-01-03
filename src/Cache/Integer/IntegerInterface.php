<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

interface IntegerInterface
{
    /**
     * 自增数字
     * @param string $key
     * @param int $step
     * @return int
     */
    public function inc(string $key, int $step = 1);

    /**
     * 自减数字
     * @param string $key
     * @param int $step
     * @return int
     */
    public function dec(string $key, int $step = 1);

    /**
     * Fetches a value from the cache.
     * @param string $key     The unique key of this item in the cache.
     * @param callable  $callable Default value to return if the key does not exist.
     * @return int The value of the item from the cache, or $default in case of cache miss.
     */
    public function get($key, callable $callable);

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     * @param string $key
     * @param int $value
     * @param int|null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null);

    /**
     * Delete an item from the cache by its unique key.
     * @param string $key The unique cache key of the item to delete.
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function delete($key);

    /**
     * Determines whether an item is present in the cache.
     * @param string $key The cache item key.
     * @return bool
     */
    public function has($key);

    /**
     * reset The cache item expiration TTL time
     * @param string $key The cache item key.
     * @param int $ttl expiration TTL time
     * @return void
     */
    public function expire($key, $ttl);
}