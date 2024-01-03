<?php

declare(strict_types=1);

namespace Madoka\Cipher;

use Madoka\Exception\EncryptException;

/**
 * 公钥
 * Class RSAPublic
 * @package Madoka\Cipher
 */
class RSAPublic extends RSACipher
{
    /**
     * @inheritDoc
     */
    public function withKey(string $content)
    {
        $resource = openssl_get_publickey($content);
        if( ! is_resource($resource) ){
            throw new EncryptException('public_key加载失败 ' . openssl_error_string());
        }
        return $resource;
    }

    /**
     * @inheritDoc
     */
    public function encrypt(string $string)
    {
        $result = openssl_public_encrypt($string, $encrypted, $this->resource, $this->padding);
        if( $result ){
            return $encrypted;
        }else{
            throw new EncryptException('加密失败: ' . openssl_error_string());
        }
    }

    /**
     * @inheritDoc
     */
    public function decrypt(string $string)
    {
        $result = openssl_public_decrypt($string, $decrypted, $this->resource, $this->padding);
        if( $result ){
            return $decrypted;
        }else{
            throw new EncryptException('解密失败: ' . openssl_error_string());
        }
    }
}