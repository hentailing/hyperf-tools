<?php

declare(strict_types=1);

namespace Madoka\Memory;

use Swoole\Atomic\Long;
use RuntimeException;

class AtomicLongManager
{
    /**
     * A container that use to store atomic.
     *
     * @var array
     */
    private static array $container = [];

    /**
     * You should initialize an Atomic Long with the identifier before use it.
     * @param string $identifier
     * @param int $value
     */
    public static function initialize(string $identifier, int $value = 0): void
    {
        static::$container[$identifier] = new Long($value);
    }

    /**
     * determine if the initialized Atomic Long is existed in container by the identifier
     */
    public static function has(string $identifier): bool
    {
        return isset(static::$container[$identifier]);
    }

    /**
     * Get an initialized Atomic Long from container by the identifier.
     *
     * @param string $identifier
     * @return Long
     * @throws RuntimeException when the Atomic Long with the identifier has not initialization
     */
    public static function get(string $identifier): Long
    {
        if (!isset(static::$container[$identifier])) {
            throw new RuntimeException('The Atomic Long has not initialization yet.');
        }

        return static::$container[$identifier];
    }

    /**
     * Remove the Atomic by the identifier from container after used,
     * otherwise will occur memory leaks.
     *
     * @param string $identifier
     */
    public static function clear(string $identifier): void
    {
        unset(static::$container[$identifier]);
    }
}