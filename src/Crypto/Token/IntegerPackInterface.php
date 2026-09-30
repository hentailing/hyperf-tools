<?php
declare(strict_types=1);

namespace Madoka\Crypto\Token;

interface IntegerPackInterface
{
    /**
     * 获取序列化格式 详见php手册pack函数
     * @see \pack
     * @return string[]
     */
    public static function getFormat(): array;

    /**
     * 获取序列化的keys [对应序列化的解析字段名称]
     * @return string[]
     */
    public static function getFormatKeys(): array;

    /**
     * 打包数据
     * @return string
     */
    public function getPack(): string;

    /**
     * 获取pack之后的长度(字节)
     * 长度可以查看php手册的pack函数
     * @return int
     */
    public function getPackSize(): int;

    /**
     * 获取解包的数据
     * @return int[]
     */
    public function getIntegers(): array;

    /**
     * 获取序列化key的数据
     * @return array<string, int>
     */
    public function getFormatData(): array;
}