<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Madoka\Cache\Driver\DriverInterface;
use Madoka\Cache\Integer\IntegerInterface;

trait NamespaceTrait
{
    /**
     * @var string[]
     */
    protected array $default;

    /**
     * @var string[]
     */
    protected array $keys = [];

    /**
     * NamespaceTrait constructor.
     * @param IntegerInterface|DriverInterface $driver
     * @param string[] $default
     */
    public function __construct(IntegerInterface|DriverInterface $driver, array $default = [])
    {
        $this->driver = $driver;
        $this->default = $default;
    }

    /**
     * 获取一个新实例
     * @return static
     */
    public function newInstance(): static
    {
        return new static($this->driver, $this->default);
    }

    public function prefix(string $prefix): self
    {
        $this->keys[] = $prefix;
        return $this;
    }

    public function perv(): self
    {
        if(!empty($this->keys)){
            array_pop($this->keys);
        }
        return $this;
    }

    /**
     * @param string $separator
     * @return string
     */
    public function join(string $separator = PrefixCacheInterface::SEPARATOR): string
    {
        return implode($separator, array_merge($this->default, $this->keys));
    }

    /**
     * @param string $name
     * @param string $separator
     * @return string
     */
    public function name(string $name, string $separator = PrefixCacheInterface::SEPARATOR): string
    {
        return implode($separator, array_merge($this->default, $this->keys, [$name]));
    }
}