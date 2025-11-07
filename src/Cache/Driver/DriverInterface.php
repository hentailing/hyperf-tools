<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use Hyperf\Cache\Driver\DriverInterface as HyperfDriverInterface;
use Generator;
use Madoka\Cache\PrefixCacheInterface;

interface DriverInterface extends HyperfDriverInterface, PrefixCacheInterface
{
    /**
     * 获取前缀Key
     * @param string $prefix
     * @return iterable<string>
     */
    public function getPrefixKeys(string $prefix): iterable;

    /**
     * 获取前缀的计数
     * @param string $prefix
     * @return int
     */
    public function countPrefix(string $prefix): int;
}