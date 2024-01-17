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
    private array $data;

    /**
     * @var SplFileObject
     */
    private SplFileObject $handle;

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
        $this->load();
    }

    /**
     * 请保持目录的写入唯一，并不保证读取数据100%正确
     */
    private function load(): void
    {
        $data = $this->handle->fread($this->handle->getSize());
        $this->data = [-1, 0];
        if (!empty($data)) {
            foreach (explode('.', $data, 2) as $index => $value) {
                $this->data[$index] = (int)$value;
            }
        }
    }

    /**
     * @return int
     */
    public function getExpiredTime(): int
    {
        return $this->data[0];
    }

    /**
     * @return int
     */
    public function getContent(): int
    {
        return $this->data[1];
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