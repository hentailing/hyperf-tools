<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use Psr\Container\ContainerInterface;

abstract class Driver implements IntegerInterface
{
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var array
     */
    protected array $config;

    /**
     * @var string
     */
    protected string $prefix;

    public function __construct(ContainerInterface $container, array $config)
    {
        $this->container = $container;
        $this->config = $config;
        $this->prefix = $config['prefix'] ?? 'integer:';
    }

    /**
     * @param string $key
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return $this->prefix . $key;
    }
}