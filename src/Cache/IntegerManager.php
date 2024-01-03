<?php

declare(strict_types=1);

namespace Madoka\Cache;

use Hyperf\Cache\Exception\InvalidArgumentException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Madoka\Cache\Integer\IntegerInterface;
use Madoka\Cache\Integer\RedisDriver;

class IntegerManager
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    protected $drivers = [];

    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    public function __construct(ConfigInterface $config, StdoutLoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getDriver($name = 'default'): IntegerInterface
    {
        if (isset($this->drivers[$name]) && $this->drivers[$name] instanceof IntegerInterface) {
            return $this->drivers[$name];
        }

        $config = $this->config->get("inahime.integer.{$name}");
        if (empty($config)) {
            throw new InvalidArgumentException(sprintf('The integer config %s is invalid.', $name));
        }

        $driverClass = isset($config['driver']) ? $config['driver'] : RedisDriver::class;

        $driver = make($driverClass, ['config' => $config]);

        return $this->drivers[$name] = $driver;
    }
}