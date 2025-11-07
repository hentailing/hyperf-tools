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

    private string $separator;

    private string $prefix;

    /**
     * NamespaceTrait constructor.
     * @param IntegerInterface|DriverInterface $driver
     * @param string[] $default
     * @param string $separator
     */
    public function __construct(IntegerInterface|DriverInterface $driver, array $default = [], string $separator = PrefixCacheInterface::SEPARATOR)
    {
        $this->driver = $driver;
        $this->default = $default;
        $this->separator = $separator;
        $this->compilePrefix();
    }

    /**
     * @return void
     */
    private function compilePrefix(): void
    {
        $this->prefix = implode($this->separator, array_merge($this->default, $this->keys));
    }

    /**
     * 获取一个新实例
     * @param string $separator
     * @return static
     */
    public function newInstance(string $separator = PrefixCacheInterface::SEPARATOR): static
    {
        return new static($this->driver, $this->default, $separator);
    }

    public function prefix(string $prefix): self
    {
        $this->keys[] = $prefix;
        $this->compilePrefix();
        return $this;
    }

    public function perv(): self
    {
        if(!empty($this->keys)){
            array_pop($this->keys);
        }
        $this->compilePrefix();
        return $this;
    }

    /**
     * @return string
     */
    public function join(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $name
     * @return string
     */
    public function name(string $name): string
    {
        return $this->prefix . $this->separator . $name;
    }
}