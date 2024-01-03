<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use Hyperf\Cache\Driver\DriverInterface as HyperfDriverInterface;
use Generator;

interface DriverInterface extends HyperfDriverInterface
{
    /**
     * 缓存分隔符
     */
    public const SEPARATOR = '.';

    /**
     * 匹配前缀时的分段长度
     */
    public const MATCH_CHUNK_SIZE = 2500;

    /**
     * 获取前缀Key
     * @param string $prefix
     * @return Generator
     */
    public function getPrefixKeys(string $prefix): Generator;

    /**
     * 获取前缀的每个元素
     * @param string $prefix
     * @return Generator
     */
    public function getMultiplePrefix(string $prefix): Generator;

    /**
     * 获取前缀的计数
     * @param string $prefix
     * @return int
     */
    public function countPrefix(string $prefix): int;
}