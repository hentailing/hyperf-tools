<?php

declare(strict_types=1);

namespace Madoka\Cache\Integer;

use SplFileInfo;

class FileIntegerReader
{
    /**
     * 过期时间
     * @var int
     */
    private int $expired = -1;

    /**
     * 自增值
     * @var int
     */
    private int $content = 0;

    private const PACK_CONTENT_LENGTH = 16;

    /**
     * FileCacheReader constructor.
     * @param SplFileInfo|string $file
     */
    public function __construct(SplFileInfo|string $file)
    {
        if ($file instanceof SplFileInfo) {
            $content = $file->openFile()->fread(self::PACK_CONTENT_LENGTH);
        } else {
            $content = file_get_contents($file);
        }
        if (!empty($content)) {
            list($this->expired, $this->content) = unpack('P2', $content);
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
     * @return int
     */
    public function getContent(): int
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

    /**
     * 将内容打包为string
     * @param int $expired
     * @param int $content
     * @return string
     */
    public static function pack(int $expired, int $content): string
    {
        return pack('P2', $expired, $content);
    }
}