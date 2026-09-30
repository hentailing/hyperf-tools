<?php
declare (strict_types=1);

namespace Madoka\Crypto\Signing;

/**
 * hash签名器
 */
class HashSigner
{
    private HashOption $option;

    public function __construct(HashOption $option)
    {
        $this->option = $option;
    }

    /**
     * 签名
     * @param string $string
     * @param bool $binary
     * @return string
     */
    public function hash(string $string, bool $binary = false): string
    {
        return hash($this->option->getAlgo(), sprintf('%s%s', $string, $this->option->getKey()), $binary);
    }

    /**
     * 摘要
     * @param string $string
     * @param bool $binary
     * @return string
     */
    public function hash_hmac(string $string, bool $binary = false): string
    {
        return hash_hmac($this->option->getAlgo(), $string, $this->option->getKey(), $binary);
    }
}