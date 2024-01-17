<?php

declare(strict_types=1);

namespace Madoka\Cipher;

use Madoka\Exception\EncryptException;

/**
 * 私钥
 * Class RSAPrivate
 * @package Madoka\Cipher
 */
class RSAPrivate extends RSACipher
{
    /**
     * @inheritDoc
     */
    public function withKey(string $content): \OpenSSLAsymmetricKey
    {
        $resource = openssl_get_privatekey($content);
        if (false === $resource) {
            throw new EncryptException('private_key加载失败 ' . openssl_error_string());
        }
        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $string): string
    {
        $result = openssl_private_encrypt($string, $encrypted, $this->resource, $this->padding);
        if ($result) {
            return $encrypted;
        } else {
            throw new EncryptException('加密失败: ' . openssl_error_string());
        }
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $string): string
    {
        $result = openssl_private_decrypt($string, $decrypted, $this->resource, $this->padding);
        if ($result) {
            return $decrypted;
        } else {
            throw new EncryptException('解密失败: ' . openssl_error_string());
        }
    }
}