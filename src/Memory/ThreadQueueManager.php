<?php

declare(strict_types=1);

namespace Madoka\Memory;

use RuntimeException;
use Swoole\Thread\Queue;

class ThreadQueueManager
{
    /**
     * A container that use to store Queue.
     *
     * @var array
     */
    private static array $container = [];

    /**
     * You should initialize a Thread Queue with the identifier before use it.
     * @param string $identifier
     */
    public static function initialize(string $identifier): void
    {
        static::$container[$identifier] = new Queue();
    }

    /**
     * determine if the initialized Thread Queue is existed in container by the identifier
     */
    public static function has(string $identifier): bool
    {
        return isset(static::$container[$identifier]);
    }

    /**
     * Get an initialized Thread Queue from container by the identifier.
     *
     * @param string $identifier
     * @return Queue
     * @throws RuntimeException when the Thread Queue with the identifier has not initialization
     */
    public static function get(string $identifier): Queue
    {
        if (!isset(static::$container[$identifier])) {
            throw new RuntimeException('The Thread Queue has not initialization yet.');
        }

        return static::$container[$identifier];
    }

    /**
     * Remove the Thread Queue by the identifier from container after used,
     * otherwise will occur memory leaks.
     *
     * @param string $identifier
     */
    public static function clear(string $identifier): void
    {
        unset(static::$container[$identifier]);
    }
}