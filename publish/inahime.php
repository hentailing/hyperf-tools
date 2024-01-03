<?php

declare(strict_types=1);

use Swoole\Table;

return [
    // 应用设置
    'normal' => [
        'service_local_ip' => env('SERVICE_LOCAL_IP', null),
    ],
    // 自增器配置
    'integer' => [
        'default' => [
            'driver'     => Hyperf\Cache\Driver\RedisDriver::class,
            'pool_class' => Hyperf\Redis\Redis::class,
        ],
    ],
    // table配置
    'tables'  => [
        'default'   =>  [
            'size'  =>  1024,
            'callback'  =>  function(Table $table){
                $table->column('name', Table::TYPE_INT, 8);
            },
            'conflictProportion' => 0.2,
            // 可通过环境变量更改是否需要该table
            'enable'    =>  true
        ],
    ],
];