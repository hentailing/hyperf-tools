<?php

declare(strict_types=1);

namespace Madoka\Cache\Driver;

use SplFileInfo;
use SplFileObject;

/**
 * 读取文件缓存过期时间
 */
class FileCacheExpireReader
{
    /**
     * 过期时间
     * @var int
     */
    protected int $expired = -1;

    /**
     * 时间戳段长度
     */
    protected const TIMESTAMP_LENGTH = 13;

    /**
     * FileCacheReader constructor.
     * @param SplFileInfo|string $file
     */
    public function __construct(SplFileInfo|string $file)
    {
        $this->load($file);
    }

    /**
     * 加载内容
     * @param SplFileInfo|string $file
     * @return void
     */
    protected function load(SplFileInfo|string $file): void
    {
        if ($file instanceof SplFileInfo) {
            $content = $file->openFile()->fread(self::TIMESTAMP_LENGTH);
        } else {
            $content = (new SplFileObject($file, 'r'))->fread(self::TIMESTAMP_LENGTH);
        }
        if ($content !== false) {
            $this->expired = (int)$content;
        }
    }

    /**
     * @return int
     */
    public function getExpiredTime(): int
    {
        return $this->expired;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        $expired = $this->getExpiredTime();
        return $expired !== 0 && time() > $expired;
    }

    /**
     * 打包缓存内容
     * @param int $expired
     * @param string $content
     * @return string
     */
    public static function pack(int $expired, string $content): string
    {
        return sprintf("%012d\n%s", $expired, $content);
    }
}