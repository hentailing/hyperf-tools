<?php

declare(strict_types=1);

namespace Madoka\Memory;

use RuntimeException;
use Swoole\Thread\ArrayList;

class ThreadArrayListManager
{
    /**
     * A container that use to store ArrayList.
     *
     * @var array
     */
    private static array $container = [];

    /**
     * You should initialize a Thread ArrayList with the identifier before use it.
     * @param string $identifier
     * @param array|null $array
     */
    public static function initialize(string $identifier, ?array $array = null): void
    {
        static::$container[$identifier] = new ArrayList($array);
    }

    /**
     * determine if the initialized Thread ArrayList is existed in container by the identifier
     */
    public static function has(string $identifier): bool
    {
        return isset(static::$container[$identifier]);
    }

    /**
     * Get an initialized Thread ArrayList from container by the identifier.
     *
     * @param string $identifier
     * @return ArrayList
     * @throws RuntimeException when the Thread ArrayList with the identifier has not initialization
     */
    public static function get(string $identifier): ArrayList
    {
        if (!isset(static::$container[$identifier])) {
            throw new RuntimeException('The Thread ArrayList has not initialization yet.');
        }

        return static::$container[$identifier];
    }

    /**
     * Remove the Thread ArrayList by the identifier from container after used,
     * otherwise will occur memory leaks.
     *
     * @param string $identifier
     */
    public static function clear(string $identifier): void
    {
        unset(static::$container[$identifier]);
    }
}