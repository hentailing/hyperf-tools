<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Hyperf\Cache\Exception\CacheException;
use Hyperf\Memory\TableManager;
use Madoka\Cache\PrefixCacheInterface;
use Psr\Container\ContainerInterface;
use Swoole\Table;

class SwooleTableDriver extends AbstractDriver
{
    public const COLUMN_NAME = 'value';

    /**
     * @var Table
     */
    protected Table $table;

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
    public function inc(string $key, int $step = 1): int
    {
        return $this->table->incr($this->getCacheKey($key), static::COLUMN_NAME, $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1): int
    {
        return $this->table->decr($this->getCacheKey($key), static::COLUMN_NAME, $step);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, callable $callable): int
    {
        $cacheKey = $this->getCacheKey($key);
        if ($this->table->exist($cacheKey)) {
            return $this->table->get($cacheKey, static::COLUMN_NAME);
        }else {
            $int = (int)$callable();
            $this->set($key, $int);
            return $int;
        }
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, int $value, int $ttl = null): bool
    {
        return $this->table->set($this->getCacheKey($key), [static::COLUMN_NAME => $value]);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        return $this->table->del($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->table->exist($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl): void
    {
        // 不需要操作，swoole table不支持过期时间设置
    }

    /**
     * @inheritDoc
     */
    public function getMultiplePrefix(string $prefix): iterable
    {
        $cacheKey = $this->getCacheKey($prefix) . PrefixCacheInterface::SEPARATOR;
        $prefixLength = strlen($cacheKey);
        foreach ($this->table as $key => $item) {
            if (str_contains($key, $cacheKey)) {
                yield substr($key, $prefixLength) => $item[static::COLUMN_NAME];
            }
        }
    }
}