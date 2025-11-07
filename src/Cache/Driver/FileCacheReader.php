<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use SplFileInfo;

class FileCacheReader extends FileCacheExpireReader
{
    /**
     * 缓存内容
     * @var string
     */
    private string $content;

    protected function load(SplFileInfo|string $file): void
    {
        if ($file instanceof SplFileInfo) {
            $content = $file->openFile()->fread($file->getSize());
        } else {
            $content = file_get_contents($file);
        }
        if ($content !== false) {
            $this->expired = (int)substr($content, 0, self::TIMESTAMP_LENGTH);
            $this->content = substr($content, self::TIMESTAMP_LENGTH);
        }
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
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