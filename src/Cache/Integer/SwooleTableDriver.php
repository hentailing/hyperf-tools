<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Hyperf\Cache\Exception\CacheException;
use Hyperf\Memory\TableManager;
use Psr\Container\ContainerInterface;
use Swoole\Table;

class SwooleTableDriver extends Driver
{
    public const COLUMN_NAME = 'value';

    /**
     * @var Table
     */
    protected $table;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);
        if(!isset($config['table_name']) || !TableManager::has($config['table_name'])){
            throw new CacheException('Swoole Table not exists!');
        }
        $this->table = TableManager::get($config['table_name']);
    }

    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1)
    {
        return $this->table->incr($key, static::COLUMN_NAME, $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1)
    {
        return $this->table->decr($key, static::COLUMN_NAME, $step);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = 0)
    {
        return $this->table->exist($key) ? $this->table->get($key, static::COLUMN_NAME) : (int)$default;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        return (bool)$this->table->set($key, [static::COLUMN_NAME => $value]);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return (bool) $this->table->del($key);
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return (bool)$this->table->exist($key);
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl)
    {
        // 不需要操作，swoole table不支持过期时间设置
    }
}