<?php
declare (strict_types=1);

namespace Madoka\Crypto\Rsa;

use Madoka\Crypto\EncryptException;
use SplFileInfo;

class RSAOption
{
    private static array $allowedPadding = [
        OPENSSL_PKCS1_PADDING,
        OPENSSL_PKCS1_OAEP_PADDING,
        OPENSSL_NO_PADDING,
    ];

    public function __construct(
        private string  $key,
        private int     $padding = OPENSSL_PKCS1_PADDING,
        private ?string $passphrase = null
    )
    {
        if (trim($this->key) === '') {
            throw new EncryptException('RSA key cannot be empty');
        }

        if (!in_array($this->padding, self::$allowedPadding, true)) {
            throw new EncryptException("RSA padding '$this->padding' is not supported");
        }
    }

    public static function fromFile (
        string  $file,
        int     $padding = OPENSSL_PKCS1_PADDING,
        ?string $passphrase = null
    ): self
    {
        $fileInfo = new SplFileInfo($file);
        if (!$fileInfo->isFile()) {
            throw new EncryptException('RSA key file does not exist: ' . $file);
        }
        if (!$fileInfo->isReadable()) {
            throw new EncryptException('RSA key file is not readable: ' . $file);
        }

        $content = file_get_contents($fileInfo->getPathname());
        if ($content === false || $content === '') {
            throw new EncryptException('RSA key file is empty: ' . $file);
        }

        return new static($content, $padding, $passphrase);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getPadding(): int
    {
        return $this->padding;
    }

    public function getPassphrase(): ?string
    {
        return $this->passphrase;
    }
}