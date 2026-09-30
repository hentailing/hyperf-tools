<?php
declare(strict_types=1);

namespace Madoka\Crypto\Rsa;

use Madoka\Crypto\EncryptException;
use OpenSSLAsymmetricKey;

/**
 * RSA 公钥、私钥执行类的公共基类。
 */
abstract class RSACipher
{
    protected OpenSSLAsymmetricKey $resource;

    private RSAOption $option;

    private ?array $details = null;

    public function __construct(RSAOption $option)
    {
        $this->option = $option;
        $this->resource = $this->createKey($option);
    }

    /**
     * 获取密钥信息。
     */
    public function getKeyDetails(): array
    {
        if ($this->details !== null) {
            return $this->details;
        }

        $details = openssl_pkey_get_details($this->resource);
        if ($details === false) {
            throw new EncryptException('获取 RSA 密钥信息失败: ' . openssl_error_string());
        }

        return $this->details = $details;
    }

    protected function getPadding(): int
    {
        return $this->option->getPadding();
    }

    /**
     * 加载RSA密钥
     * @param RSAOption $option
     * @return OpenSSLAsymmetricKey
     */
    abstract protected function createKey(RSAOption $option): OpenSSLAsymmetricKey;

    /**
     * 加密内容
     * @param string $string
     * @return string
     */
    abstract public function encrypt(string $string): string;

    /**
     * 解密内容
     * @param string $string
     * @return string
     */
    abstract public function decrypt(string $string): string;
}