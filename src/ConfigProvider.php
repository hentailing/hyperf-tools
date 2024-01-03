<?php

declare(strict_types=1);

namespace Madoka;

/**
 * Class ConfigProvider
 * @package Madoka
 */
class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for inahime.',
                    'source' => __DIR__ . '/../publish/inahime.php',
                    'destination' => BASE_PATH . '/config/autoload/inahime.php',
                ],
            ],
        ];
    }
}