<?php

declare(strict_types=1);

namespace Madoka\Units;

use Hyperf\Pool\Connection;

/**
 * 虚拟连接池的连接 用于并行数量限制
 * Class DummyConnection
 * @package App\Common
 */
class DummyConnection extends Connection
{

    /**
     * @return $this
     */
    public function getActiveConnection()
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function reconnect(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        return true;
    }
}