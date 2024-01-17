<?php

declare(strict_types=1);

namespace Madoka\Units;

class Action
{
    /**
     * @param int $code
     * @param string $message
     * @param mixed|null $data
     * @return array
     */
    public static function error(int $code, string $message, mixed $data = null): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * @param mixed|null $data
     * @param string $message
     * @return array
     */
    public static function success(mixed $data = null, string $message = 'success'): array
    {
        return self::error(0, $message, $data);
    }
}