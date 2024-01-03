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
    private static $container = [];

    /**
     * You should initialize a Atomic Long with the identifier before use it.
     * @param string $identifier
     * @param int $value
     */
    public static function initialize(string $identifier, $value = 0): void
    {
        static::$container[$identifier] = new Long($value);
    }

    /**
     * Get a initialized Atomic Long from container by the identifier.
     *
     * @param string $identifier
     * @return Long
     * @throws RuntimeException when the Atomic Long with the identifier has not initialization
     */
    public static function get(string $identifier): Long
    {
        if (! isset(static::$container[$identifier])) {
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