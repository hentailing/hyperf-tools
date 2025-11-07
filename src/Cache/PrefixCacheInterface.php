<?php

declare(strict_types=1);

namespace Madoka\Cache;

interface PrefixCacheInterface
{
    /**
     * cache key namespace separator
     */
    public const SEPARATOR = '.';

    /**
     * prefix chunk size when match
     */
    public const MATCH_CHUNK_SIZE = 2500;

    /**
     * Fetches prefix value from the cache.
     * @param string $prefix
     * @return iterable<string, mixed>
     */
    public function getMultiplePrefix(string $prefix): iterable;
}