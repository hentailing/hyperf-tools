<?php
declare (strict_types=1);

namespace Madoka\Crypto\Token;

use Madoka\Crypto\Signing\HashSigner;
use Madoka\Crypto\Symmetric\Cipher;

class HashToken
{
    private Cipher $cipher;
    private HashSigner $signer;
    private int $signatureLength;

    public function __construct(Cipher $cipher, HashSigner $signer)
    {
        $this->cipher = $cipher;
        $this->signer = $signer;
        $this->signatureLength = strlen($this->sign(""));
    }

    /**
     * 生成
     * @param IntegerPackInterface $integerPack
     * @return string
     */
    public function generate(IntegerPackInterface $integerPack): string
    {
        $data = $integerPack->getPack();

        $cipherResult = $this->cipher->encrypt($data);

        $hash = $this->sign($cipherResult->getIv() . $cipherResult->getEncrypted());

        return $this->legibleEncode($cipherResult->getEncrypted() . $cipherResult->getIv() . $hash);
    }

    /**
     * 验证
     * @param string $data
     * @return string
     */
    public function validate(string $data): string
    {
        $baseDecode = $this->legibleDecode($data);

        $ivLength = $this->cipher->getIvLength();
        $signatureLength = $this->signatureLength;
        $encryptedLength = strlen($baseDecode) - $ivLength - $signatureLength;

        if ($encryptedLength <= 0) {
            throw new \RuntimeException('字符串长度不正确');
        }

        $decodeEncrypt = substr($baseDecode, 0, $encryptedLength);
        $decodeIv = substr($baseDecode, $encryptedLength, $ivLength);
        $decodeHash = substr($baseDecode, $encryptedLength + $ivLength, $signatureLength);

        if (!hash_equals($decodeHash, $this->sign($decodeIv . $decodeEncrypt))) {
            throw new \RuntimeException('数据验证验证失败');
        }

        return $this->cipher->decrypt($decodeEncrypt, $decodeIv);
    }

    /**
     * 编码为可读性的字符串
     * @param string $data
     * @return string
     */
    protected function legibleEncode(string $data): string
    {
        return base64_encode($data);
    }

    /**
     * 解码字符串
     * @param string $data
     * @return string
     */
    protected function legibleDecode(string $data): string
    {
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            throw new \RuntimeException('字符串编码不正确');
        }

        return $decoded;
    }

    private function sign(string $data): string
    {
        return $this->signer->hash_hmac($data, true);
    }
}