<?php

declare(strict_types=1);

namespace Madoka\Units;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Context\ApplicationContext;
use Psr\Http\Message\ServerRequestInterface;

class Clients
{
    /**
     * 获取客户端ip
     * @param ServerRequestInterface $request
     * @return string
     */
    public static function ip(ServerRequestInterface $request): string
    {
        foreach (['X-REAL-IP', 'X-FORWARDED-FOR', 'CLIENT-IP', 'X-CLIENT-IP', 'X-CLUSTER-CLIENT-IP'] as $item){
            $header = $request->getHeaderLine($item);
            if( !empty( $header ) ){
                return $header;
            }
        }
        return $request->getServerParams()['remote_addr'];
    }

    /**
     * 获取本地ip
     * @return string
     */
    public static function localIp(): string
    {
        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $local_ip = $config->get('inahime.service_local_id', null);
        if(!isset($local_ip)){
            $eth = swoole_get_local_ip();
            if(!empty($eth)){
                $local_ip = current($eth);
            }
        }
        return $local_ip;
    }
}