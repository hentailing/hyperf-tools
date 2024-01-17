<?php

declare(strict_types=1);

namespace Madoka\Protocol;

interface ProtocolInterface
{
    /**
     * @param string $buffer
     * @return int
     */
    public static function length(string $buffer): int;

    /**
     * @param string $buffer
     * @return string
     */
    public static function encode(string $buffer): string;

    /**
     * @param string $buffer
     * @return mixed
     */
    public static function decode(string $buffer): mixed;
}