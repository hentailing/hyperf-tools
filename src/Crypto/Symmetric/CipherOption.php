<?php
declare (strict_types=1);

namespace Madoka\Crypto\Symmetric;

use Madoka\Crypto\EncryptException;

/**
 * 加密选项
 */
class CipherOption
{
    private static array $allowedCipherAlgo = ["AES-128-CBC", "AES-256-CBC"];
    private string $cipherAlgo;
    private int $option;
    private string $key;

    public function __construct(string $cipherAlgo, int $option, string $key) {
        if (!in_array($cipherAlgo, self::$allowedCipherAlgo, true)) {
            throw new EncryptException("Cipher algorithm '$cipherAlgo' is not supported");
        }
        $this->cipherAlgo = $cipherAlgo;
        $this->option = $option;
        $keyLength = $this->getKeyLength();
        $this->key = str_pad(substr($key, 0, $keyLength), $keyLength, "\0");
    }

    public static function defaultOption(string $key): self
    {
        return new static("AES-128-CBC", OPENSSL_RAW_DATA, $key);
    }

    public function getCipherAlgo(): string
    {
        return $this->cipherAlgo;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getOption(): int
    {
        return $this->option;
    }

    public function getKeyLength(): int
    {
        $length = openssl_cipher_key_length($this->cipherAlgo);
        if ($length === false) {
            throw new EncryptException('获取key长度失败' . openssl_error_string());
        }
        return $length;
    }

    public function getIvLength(): int
    {
        $length = openssl_cipher_iv_length($this->getCipherAlgo());
        if ($length === false) {
            throw new EncryptException('获取iv长度失败' . openssl_error_string());
        }
        return $length;
    }
}