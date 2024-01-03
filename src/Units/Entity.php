<?php

declare(strict_types=1);

namespace Madoka\Units;

use Hyperf\Utils\Contracts\Arrayable;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use stdClass;

/**
 * 简便实体内用于数组对象处理
 * Class Entity
 * @package Madoka\Units
 */
class Entity implements IteratorAggregate, JsonSerializable, ArrayAccess, Arrayable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * Entity constructor.
     * @param array $data
     */
    public function __construct($data = [])
    {
        $this->data($data);
    }

    public function __get($name)
    {
        return isset($this->data[$name]) ? $this->data[$name] : null;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return array_key_exists($name, $this->data);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /**
     * @param array $data
     */
    public function data($data)
    {
        $this->data = (array)$data;
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return empty($this->data) ? new stdClass() : $this->data;
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
    public function isEmpty(){
        return empty($this->data);
    }
}