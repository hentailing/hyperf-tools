<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Psr\Container\ContainerInterface;
use Hyperf\Redis\Redis;

class RedisDriver extends Driver
{
    /**
     * @var Redis
     */
    protected $redis;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);
        if(isset($config['pool_class'])){
            $this->redis = $container->get($config['pool_class']);
        }else{
            $this->redis = $container->get(Redis::class);
        }
    }

    /**
     * @inheritDoc
     */
    public function inc(string $key, int $step = 1)
    {
        return $this->redis->incrBy($this->getCacheKey($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function dec(string $key, int $step = 1)
    {
        return $this->redis->decrBy($this->getCacheKey($key), $step);
    }

    /**
     * @inheritDoc
     */
    public function get($key, $default = 0)
    {
        $key = $this->getCacheKey($key);
        $result = $this->redis->get($key);
        if($result === false){
            $this->set($key, $default);
            return $default;
        }
        return (int)$result;
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value, $ttl = null)
    {
        return $this->redis->set($this->getCacheKey($key), $value);
    }

    /**
     * @inheritDoc
     */
    public function delete($key)
    {
        return (bool)$this->redis->del($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function has($key)
    {
        return $this->redis->exists($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function expire($key, $ttl)
    {
        $this->redis->expire($this->getCacheKey($key), $ttl);
    }
}