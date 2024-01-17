<?php

declare(strict_types=1);

namespace Madoka\Security;

class HashToken
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var string
     */
    private string $method = 'AES-256-CBC';

    /**
     * @var string
     */
    private string $hashMethod = 'md5';

    /**
     * @var int
     */
    private int $option = OPENSSL_RAW_DATA;

    /**
     * @var int
     */
    private int $ivLength = 16;

    /**
     * @var int
     */
    private int $hashLength = 16;

    /**
     * @param string $key
     * @return static
     */
    public function setKey(string $key): self
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @param int $option
     * @return static
     */
    public function setOption(int $option): self
    {
        $this->option = $option;
        return $this;
    }

    /**
     * 使用md5
     */
    public function useMd5(): void
    {
        $this->hashMethod = 'md5';
        $this->hashLength = 16;
    }

    /**
     * 使用sha1
     */
    public function useSha1(): void
    {
        $this->hashMethod = 'sha1';
        $this->hashLength = 20;
    }

    /**
     * 使用sha256
     */
    public function useSha256(): void
    {
        $this->hashMethod = 'sha256';
        $this->hashLength = 32;
    }

    /**
     * 生成
     * @param IntegerPackInterface $integerPack
     * @return string
     */
    public function generate(IntegerPackInterface $integerPack): string
    {
        $iv = random_bytes($this->ivLength);

        $data = $integerPack->getPack();

        $encrypt = openssl_encrypt($data, $this->method, $this->key, $this->option, $iv);

        $hash = hash($this->hashMethod, $iv . $encrypt, true);

        return $this->legibleEncode($encrypt . $iv . $hash);
    }

    /**
     * 验证
     * @param string $data
     * @return string
     */
    public function validate(string $data): string
    {
        $baseDecode = $this->legibleDecode($data);

        $ivHashLength = $this->ivLength + $this->hashLength;

        if (strlen($baseDecode) < $ivHashLength) {
            throw new \RuntimeException('字符串长度不正确');
        }

        $decodeEncrypt = substr($baseDecode, 0, -$ivHashLength);

        $ivHashString = substr($baseDecode, -$ivHashLength);

        $decodeIv = substr($ivHashString, 0, $this->ivLength);

        $decodeHash = substr($ivHashString, -$this->hashLength);

        if (hash_equals($decodeHash, hash($this->hashMethod, $decodeIv . $decodeEncrypt, true))) {
            return openssl_decrypt($decodeEncrypt, $this->method, $this->key, OPENSSL_RAW_DATA, $decodeIv);
        } else {
            throw new \RuntimeException('数据验证验证失败');
        }
    }

    /**
     * @param string $data
     * @return string
     */
    protected function legibleEncode(string $data): string
    {
        return base64_encode($data);
    }

    /**
     * @param string $data
     * @return string
     */
    protected function legibleDecode(string $data): string
    {
        return base64_decode($data);
    }
}