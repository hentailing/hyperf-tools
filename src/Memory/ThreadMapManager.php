<?php

declare(strict_types=1);

namespace Madoka\Memory;

use RuntimeException;
use Swoole\Thread\Map;

class ThreadMapManager
{
    /**
     * A container that use to store atomic.
     *
     * @var array
     */
    private static array $container = [];

    /**
     * You should initialize a Thread Map with the identifier before use it.
     * @param string $identifier
     * @param array|null $array
     */
    public static function initialize(string $identifier, ?array $array = null): void
    {
        static::$container[$identifier] = new Map($array);
    }

    /**
     * determine if the initialized Thread Map is existed in container by the identifier
     */
    public static function has(string $identifier): bool
    {
        return isset(static::$container[$identifier]);
    }

    /**
     * Get an initialized Thread Map from container by the identifier.
     *
     * @param string $identifier
     * @return Map
     * @throws RuntimeException when the Thread Map with the identifier has not initialization
     */
    public static function get(string $identifier): Map
    {
        if (!isset(static::$container[$identifier])) {
            throw new RuntimeException('The Thread Map has not initialization yet.');
        }

        return static::$container[$identifier];
    }

    /**
     * Remove the Thread Map by the identifier from container after used,
     * otherwise will occur memory leaks.
     *
     * @param string $identifier
     */
    public static function clear(string $identifier): void
    {
        unset(static::$container[$identifier]);
    }
}