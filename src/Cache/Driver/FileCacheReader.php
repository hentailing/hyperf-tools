<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use SplFileInfo;
use SplFileObject;

class FileCacheReader
{
    /**
     * @var array
     */
    private array $data;

    /**
     * @var SplFileObject
     */
    private SplFileObject $handle;

    /**
     * 时间戳段长度
     */
    private const TIMESTAMP_LENGTH = 11;

    /**
     * FileCacheReader constructor.
     * @param SplFileInfo|string $file
     */
    public function __construct(SplFileInfo|string $file)
    {
        if ($file instanceof SplFileInfo) {
            $this->handle = $file->openFile();
        } else {
            $this->handle = new SplFileObject($file, 'r');
        }
    }

    /**
     * @return int
     */
    public function getExpiredTime(): int
    {
        if (!isset($this->data['expired'])) {
            $expired = $this->handle->fread(self::TIMESTAMP_LENGTH);
            if ($expired === false) {
                $expired = -1;
            } else {
                $expired = (int)$expired;
            }
            $this->data['expired'] = $expired;
        }
        return $this->data['expired'];
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        if (!isset($this->data['content'])) {
            $this->getExpiredTime();
            $this->data['content'] = $this->handle->fread($this->handle->getSize());
        }
        return (string)$this->data['content'];
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        $expired = $this->getExpiredTime();
        return $expired !== 0 && time() > $expired;
    }
}