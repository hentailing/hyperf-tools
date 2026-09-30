<?php
declare(strict_types=1);

namespace Madoka\Crypto\Token;

class PackInteger implements IntegerPackInterface
{
    /**
     * @var int[]
     */
    protected array $integers;

    /**
     * @inheritDoc
     */
    public static function getFormat(): array
    {
        return ['L', 'L'];
    }

    /**
     * @inheritDoc
     */
    public static function getFormatKeys(): array
    {
        return ['id', 'uid'];
    }

    /**
     * @inheritDoc
     */
    public function getPack(): string
    {
        return pack(implode('', static::getFormat()), ...$this->getIntegers());
    }

    /**
     * @inheritDoc
     */
    public function getPackSize(): int
    {
        return 8;
    }

    /**
     * 从打包字符串获取参数
     * @param string $packString
     * @return static
     */
    public static function fromPack(string $packString): self
    {
        $instance = new static();
        $formats = [];
        foreach (static::getFormat() as $index => $format) {
            $formats[] = $format . 'index' . $index;
        }
        $instance->setIntegers(array_values(unpack(implode('/', $formats), $packString)));
        return $instance;
    }

    /**
     * @param int[] $integers
     * @return static
     */
    public static function fromIntegers(array $integers): self
    {
        $instance = new static();
        $instance->setIntegers($integers);
        return $instance;
    }

    /**
     * @inheritDoc
     */
    public function getIntegers(): array
    {
        return $this->integers;
    }

    /**
     * @param int[] $integers
     * @return $this
     */
    public function setIntegers(array $integers): self
    {
        if (count($integers) != count(static::getFormat())) {
            throw new \RuntimeException('数字数量与format不匹配');
        }
        $this->integers = $integers;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFormatData(): array
    {
        $formatKeys = static::getFormatKeys();
        $data = [];
        foreach ($this->integers as $index => $integer) {
            if (isset($formatKeys[$index])) {
                $data[$formatKeys[$index]] = $integer;
            } else {
                $data[$index] = $integer;
            }
        }
        return $data;
    }
}