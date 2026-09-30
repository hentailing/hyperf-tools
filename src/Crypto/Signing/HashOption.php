<?php
declare (strict_types=1);

namespace Madoka\Crypto\Signing;

use Madoka\Crypto\EncryptException;

class HashOption
{
    private static array $allowedAlgo = ["md5", "sha1", "sha256", "sha512"];
    private string $algo;
    private string $key;
    public function __construct(string $key, string $algo = "md5")
    {
        if (!in_array($algo, self::$allowedAlgo, true)) {
            throw new EncryptException("The algorithm '$algo' is not supported.");
        }
        $this->algo = $algo;
        if (empty($key)) {
            throw new EncryptException('key cannot be empty');
        }
        $this->key = $key;
    }

    public function getAlgo(): string
    {
        return $this->algo;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}