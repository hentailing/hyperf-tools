<?php

namespace Madoka\Event;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Memory\TableManager;
use Hyperf\Framework\Bootstrap\ServerStartCallback;
use Psr\Container\ContainerInterface;

/**
 * 服务启动前加载
 * Class BeforeStartCallback
 * @package Madoka\Event
 */
class BeforeStartCallback extends ServerStartCallback
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function beforeStart()
    {
        $config = $this->container->get(ConfigInterface::class);
        $tableConfigs = $config->get('inahime.tables', []);
        foreach ($tableConfigs as $key => $item){
            if($item['enable'] && is_callable($item['callback'])){
                $table = TableManager::initialize($key, $item['size'], isset($item['conflictProportion']) ? $item['conflictProportion'] : 0.2);
                call_user_func($item['callback'], $table);
                $table->create();
            }
        }
    }
}