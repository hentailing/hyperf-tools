<?php

declare(strict_types=1);

namespace Madoka\Cipher;

use Madoka\Exception\EncryptException;
use SplFileInfo;

/**
 * RSA 加密解密类
 * Class RSACipher
 * @package Madoka\Cipher
 */
abstract class RSACipher
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * @var int
     */
    protected int $padding = OPENSSL_PKCS1_PADDING;

    /**
     * @var array
     */
    private array $details;

    public function __construct(string $rsaKeyFile)
    {
        if(!is_file($rsaKeyFile)){
            throw new EncryptException('没有配置密钥');
        }
        $content = $this->getFileContent($rsaKeyFile);
        $this->resource = $this->withKey($content);
    }

    /**
     * @param int $padding
     * @return static
     */
    public function withPadding(int $padding): self
    {
        $this->padding = $padding;
        return $this;
    }

    /**
     * 获取密钥信息
     * @return array
     */
    public function getKeyDetails(): array
    {
        if(!isset($this->details)){
            $this->details = openssl_pkey_get_details($this->resource);
        }
        return $this->details;
    }

    /**
     * 获取文件内容
     * @param string $file
     * @return string
     * @throws EncryptException
     */
    protected function getFileContent(string $file): string
    {
        $splFile = new SplFileInfo($file);
        if( ! $splFile->isFile() ){
            throw new EncryptException( '密钥文件不存在 '.$file);
        }
        if( !$splFile->isReadable() ){
            throw new EncryptException( '密钥文件没有读取权限 '.$file);
        }
        $splFileObject = $splFile->openFile('r');
        $size = $splFile->getSize();
        if($size === 0){
            throw new EncryptException( '密钥文件为空 '.$file);
        }
        return $splFileObject->fread($size);
    }

    /**
     * 设置密钥
     * @param string $content 密钥内容
     * @return \OpenSSLAsymmetricKey
     * @throws EncryptException
     */
    abstract public function withKey(string $content): \OpenSSLAsymmetricKey;

    /**
     * 加密内容
     * @param string $string
     * @return string
     * @throws EncryptException
     */
    abstract public function encrypt(string $string): string;

    /**
     * 解密内容
     * @param string $string
     * @return string
     * @throws EncryptException
     */
    abstract public function decrypt(string $string): string;
}