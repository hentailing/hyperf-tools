<?php
declare (strict_types=1);

namespace Madoka\Crypto\Symmetric;

use Madoka\Crypto\EncryptException;
use Random\RandomException;

/**
 * Openssl 加密解密类
 */
class Cipher
{
    private CipherOption $cipherOption;

    /**
     * Cipher constructor.
     * @param CipherOption $cipherOption
     */
    public function __construct(CipherOption $cipherOption)
    {
        $this->cipherOption = $cipherOption;
    }

    /**
     * @param string $string
     * @return CipherResult
     */
    public function encrypt(string $string): CipherResult
    {
        $iv = $this->createIv();
        $encrypt = openssl_encrypt($string, $this->cipherOption->getCipherAlgo(), $this->cipherOption->getKey(), $this->cipherOption->getOption(), $iv);
        if ($encrypt === false) {
            throw new EncryptException('加密失败 ' . openssl_error_string());
        }
        return new CipherResult($encrypt, $iv);
    }

    /**
     * @param string $encrypt
     * @param string $iv
     * @return string
     */
    public function decrypt(string $encrypt, string $iv): string
    {
        $decrypted = openssl_decrypt($encrypt, $this->cipherOption->getCipherAlgo(), $this->cipherOption->getKey(), $this->cipherOption->getOption(), $iv);
        if ($decrypted === false) {
            throw new EncryptException('解密失败 ' . openssl_error_string());
        }
        return $decrypted;
    }

    public function getIvLength(): int
    {
        return $this->cipherOption->getIvLength();
    }

    /**
     * 生成iv
     * @return string
     */
    private function createIv(): string
    {
        try {
            return random_bytes($this->cipherOption->getIvLength());
        }catch (RandomException $randomException) {
            throw new EncryptException('生成iv失败' . $randomException->getMessage());
        }
    }
}