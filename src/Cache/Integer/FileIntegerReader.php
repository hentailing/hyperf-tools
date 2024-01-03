<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use SplFileInfo;
use SplFileObject;

class FileIntegerReader
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var SplFileObject
     */
    private $handle;

    /**
     * FileCacheReader constructor.
     * @param string|SplFileInfo $file
     */
    public function __construct($file)
    {
        if($file instanceof SplFileInfo){
            $this->handle = $file->openFile('r');
        }else{
            $this->handle = new SplFileObject($file, 'r');
        }
        $this->load();
    }

    /**
     * 请保持目录的写入唯一，并不保证读取数据100%正确
     */
    private function load(){
        $data = $this->handle->fread($this->handle->getSize());
        $this->data = [-1, 0];
        if(!empty($data)){
            foreach (explode('.', $data, 2) as $index => $value){
                $this->data[$index] = (int)$value;
            }
        }
    }

    /**
     * @return int
     */
    public function getExpiredTime()
    {
        return $this->data[0];
    }

    /**
     * @return int
     */
    public function getContent(){
        return $this->data[1];
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $expired = $this->getExpiredTime();
        return $expired !== 0 && time() > $expired;
    }
}