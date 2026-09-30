<?php
declare (strict_types=1);

namespace Madoka\Crypto\Symmetric;

class CipherResult
{
    /**
     * 加密内容(bytes)
     * @var string
     */
    private string $encrypted;
    /**
     * 向量(bytes)
     * @var string
     */
    private string $iv;
    public function __construct(string $encrypted, string $iv) {
        $this->encrypted = $encrypted;
        $this->iv = $iv;
    }

    public function getEncrypted() : string {
        return $this->encrypted;
    }

    public function getIv() : string {
        return $this->iv;
    }
}