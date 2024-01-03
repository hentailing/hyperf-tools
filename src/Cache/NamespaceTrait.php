<?php

declare(strict_types=1);

namespace Madoka\Cache;

trait NamespaceTrait
{
    /**
     * @var string[]
     */
    protected $default;

    /**
     * @var object
     */
    protected $driver;

    /**
     * @var string[]
     */
    protected $keys = [];

    /**
     * NamespaceTrait constructor.
     * @param object $driver
     * @param string[] $default
     */
    public function __construct($driver, $default = [])
    {
        $this->driver = $driver;
        $this->default = $default;
    }

    /**
     * 获取一个新实例
     * @return static
     */
    public function newInstance(){
        return new static($this->driver, $this->default);
    }

    /**
     * @param string $prefix
     * @return $this
     */
    public function prefix($prefix){
        array_push($this->keys, $prefix);
        return $this;
    }

    /**
     * @return $this
     */
    public function perv(){
        if(!empty($this->keys)){
            array_pop($this->keys);
        }
        return $this;
    }

    /**
     * @param string $separator
     * @return string
     */
    public function join($separator = '.'){
        return implode($separator, array_merge($this->default, $this->keys));
    }

    /**
     * @param string $name
     * @param string $separator
     * @return string
     */
    public function name($name, $separator = '.'){
        return implode($separator, array_merge($this->default, $this->keys, [$name]));
    }
}