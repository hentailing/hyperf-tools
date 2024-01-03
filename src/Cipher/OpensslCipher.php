<?php

declare(strict_types=1);

namespace Madoka\Cipher;

use Madoka\Exception\EncryptException;

/**
 * Openssl 加密解密类
 * Class OpensslCipher
 * @package Madoka\Cipher
 */
class OpensslCipher
{
    protected $config = [
        'cipher' => 'AES-128-CBC',
        'option' => OPENSSL_RAW_DATA,
        'key'    => '',
        'iv_length' => 0,
    ];

    public function __construct(array $config)
    {
        $this->config = array_merge($this->config, $config);
        $this->checkKey();
        $this->makeIvLength();
    }

    /**
     * @param string $string
     * @return string
     * @throws EncryptException
     */
    public function encrypt(string $string)
    {
        return $this->pack(function ($iv) use ($string){
            $key = $this->getKey();
            $encrypt = openssl_encrypt($string, $this->getCipher(), $key, $this->getOption(), $iv);
            if($encrypt === false){
                throw new EncryptException('加密失败 ' . openssl_error_string());
            }
            return $encrypt;
        });
    }

    /**
     * @param string $encrypt
     * @return string
     * @throws EncryptException
     */
    public function decrypt(string $encrypt)
    {
        return $this->unpack(function ($body, $iv) use ($encrypt){
            $key = $this->getKey();
            $decrypted = openssl_decrypt($body, $this->getCipher(), $key, $this->getOption(), $iv);
            if($decrypted === false){
                throw new EncryptException('解密失败 ' . openssl_error_string());
            }
            return $decrypted;
        }, $encrypt);
    }

    /**
     * 设置key
     * @param string $key
     * @return $this
     * @throws EncryptException
     */
    public function withKey(string $key){
        $this->config['key'] = $key;
        $this->checkKey();
        return $this;
    }

    /**
     * 设置加密算法
     * @param string $cipher
     * @return $this
     * @throws EncryptException
     */
    public function withCipher(string $cipher){
        $this->config['cipher'] = $cipher;
        return $this->makeIvLength();
    }

    /**
     * 设置加密下标记的按位或
     * @param int $option
     * @return $this
     */
    public function withOption(int $option){
        $this->config['option'] = $option;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey(){
        return $this->config['key'];
    }

    /**
     * @return string
     */
    public function getCipher(){
        return $this->config['cipher'];
    }

    /**
     * @return int
     */
    public function getOption(){
        return $this->config['option'];
    }

    /**
     * @return int
     */
    public function getIvLength(){
        return $this->config['iv_length'];
    }

    /**
     * @param callable $callback
     * @return string
     * @throws EncryptException
     */
    protected function pack($callback){
        $length = $this->getIvLength();
        $iv = '';
        $pack = '';
        switch ($length){
            case 4:
                $iv = rand(0, 9999);
                $pack = pack('S', $iv);
                break;
            case 8:
                $iv = rand(0, 99999999);
                $pack = pack('L', $iv);
                break;
            case 12:
                $iv = rand(0, 4294967295);
                $pack = pack('L', $iv);
                $iv = sprintf('%012d', $iv);
                break;
            case 16:
                $iv = rand(0, 4294967295);
                $pack = pack('L', $iv);
                $iv = sprintf('%016d', $iv);
                break;
        }
        return $pack . call_user_func($callback, $iv);
    }

    /**
     * @param callable $callback
     * @param string $encrypt
     * @return string
     * @throws EncryptException
     */
    protected function unpack($callback, string $encrypt){
        $length = $this->getIvLength();
        switch ($length){
            case 4:
                if( strlen($encrypt) < 2 ){
                    throw new EncryptException('encrypt 字符串长度小于2');
                }
                $head = substr($encrypt, 0, 2);
                $body = substr($encrypt, 2);
                $temp = unpack('Siv', $head);
                if( $temp === false ){
                    throw new EncryptException('unpack 失败');
                }
                $iv = $temp['iv'];
                break;
            case 8:
            case 12:
            case 16:
                if( strlen($encrypt) < 4 ){
                    throw new EncryptException('encrypt 字符串长度小于4');
                }
                $head = substr($encrypt, 0, 4);
                $body = substr($encrypt, 4);
                $temp = unpack('Liv', $head);
                if( $temp === false ){
                    throw new EncryptException('unpack 失败');
                }
                $iv = $temp['iv'];
                if( $length === 12 ){
                    $iv = sprintf('%012d', $iv);
                }

                if( $length === 16 ){
                    $iv = sprintf('%016d', $iv);
                }
                break;
            default:
                $iv = null;
                $body = $encrypt;
                break;
        }
        return call_user_func($callback, $body, $iv);
    }


    /**
     * @return $this
     */
    protected function makeIvLength(){
        $cipher = $this->getCipher();
        $length = openssl_cipher_iv_length($cipher);
        if($length === false){
            throw new EncryptException('获取iv长度失败' . openssl_error_string());
        }
        $this->config['iv_length'] = $length;
        return $this;
    }

    /**
     * @throws EncryptException
     */
    protected function checkKey(){
        if(empty($this->config['key'])){
            throw new EncryptException('请设置密钥');
        }
    }
}