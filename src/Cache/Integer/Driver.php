<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Hyperf\Utils\InteractsWithTime;
use Psr\Container\ContainerInterface;

abstract class Driver implements IntegerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix;

    public function __construct(ContainerInterface $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
        $this->prefix = isset($config['prefix']) ? $config['prefix'] : 'integer:';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getCacheKey(string $key)
    {
        return $this->prefix . $key;
    }
}