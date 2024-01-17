<?php
declare(strict_types=1);

namespace Madoka\Cipher;

/**
 * hash签名器
 * Class HashSigner
 * @package Madoka\Cipher
 */
class HashSigner
{
    /**
     * @var string
     */
    protected string $key;

    public function __construct(string $key)
    {
        $this->withKey($key);
    }

    /**
     * @param string $key
     * @return $this
     */
    public function withKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * 签名
     * @param string $string
     * @param string|null $algorithm
     * @return string
     */
    public function hash(string $string, string $algorithm = null): string
    {
        if (is_null($algorithm)) {
            $algorithm = 'md5';
        }
        return hash($algorithm, sprintf('%s%s', $string, $this->key));
    }

    /**
     * 摘要
     * @param string $string
     * @param string|null $algorithm
     * @return string
     */
    public function hash_hmac(string $string, string $algorithm = null): string
    {
        if (is_null($algorithm)) {
            $algorithm = 'md5';
        }
        return hash_hmac($algorithm, $string, $this->key);
    }
}