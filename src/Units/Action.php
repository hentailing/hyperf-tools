<?php

declare(strict_types=1);

namespace Madoka\Units;

class Action
{
    /**
     * @param int $code
     * @param string $message
     * @param mixed $data
     * @return array
     */
    public static function error($code, $message, $data = null){
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * @param mixed $data
     * @param string $message
     * @return array
     */
    public static function success($data = null, $message = 'success'){
        return self::error(0, $message, $data);
    }
}