<?php
declare (strict_types=1);

namespace Madoka\Crypto\Rsa;

use Madoka\Crypto\EncryptException;
use OpenSSLAsymmetricKey;

/**
 * 私钥
 */
class RSAPrivate extends RSACipher
{
    /**
     * @inheritDoc
     */
    protected function createKey(RSAOption $option): OpenSSLAsymmetricKey
    {
        $resource = openssl_pkey_get_private($option->getKey(), $option->getPassphrase());
        if ($resource instanceof OpenSSLAsymmetricKey) {
            return $resource;
        }
        throw new EncryptException('private_key加载失败 ' . openssl_error_string());
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $string): string
    {
        if (!openssl_private_encrypt($string, $encrypted, $this->resource, $this->getPadding())) {
            throw new EncryptException('加密失败: ' . openssl_error_string());
        }

        return $encrypted;
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $string): string
    {
        if (!openssl_private_decrypt($string, $decrypted, $this->resource, $this->getPadding())) {
            throw new EncryptException('解密失败: ' . openssl_error_string());
        }

        return $decrypted;
    }
}