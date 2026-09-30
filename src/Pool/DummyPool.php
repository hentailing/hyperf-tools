<?php
declare(strict_types=1);

namespace Madoka\Pool;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Pool;

/**
 * 虚拟连接池 用于并行数量限制
 * Class DummyPool
 * @package Madoka\Pool
 */
class DummyPool extends Pool
{
    protected function createConnection(): ConnectionInterface
    {
        return new DummyConnection($this->container, $this);
    }
}