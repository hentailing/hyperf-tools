<?php

declare(strict_types=1);

namespace Madoka\Protocol;

class Text implements ProtocolInterface
{
    /**
     * 最大包体长度 8 * 1024 * 1024
     */
    const MAX_LEN = 8388608;

    /**
     * @inheritDoc
     */
    public static function length(string $buffer): int
    {
        if (strlen($buffer) >= self::MAX_LEN) {
            return -1;
        }
        $pos = strpos($buffer, "\n");
        if ($pos === false) {
            return 0;
        }
        return $pos + 1;
    }

    /**
     * @inheritDoc
     */
    public static function encode(string $buffer): string
    {
        return trim($buffer) . "\n";
    }

    /**
     * @inheritDoc
     */
    public static function decode(string $buffer): string
    {
        return trim($buffer);
    }
}