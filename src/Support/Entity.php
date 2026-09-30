<?php

declare(strict_types=1);

namespace Madoka\Support;

use Hyperf\Contract\Arrayable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;

/**
 * 简便实体内用于数组对象处理
 * Class Entity
 * @package Madoka\Support
 */
class Entity implements IteratorAggregate, JsonSerializable, ArrayAccess, Arrayable
{
    /**
     * @var array
     */
    protected array $data;

    /**
     * Entity constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data($data);
    }

    public function __get($name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __set($name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function __isset($name): bool
    {
        return array_key_exists($name, $this->data);
    }

    public function __unset($name): void
    {
        unset($this->data[$name]);
    }

    /**
     * @param array $data
     */
    public function data(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->__unset($offset);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): null|array
    {
        return empty($this->data) ? null : $this->data;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->data);
    }
}