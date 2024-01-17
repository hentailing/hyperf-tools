<?php

declare(strict_types=1);

namespace Madoka\Security;

class PackInteger implements IntegerPackInterface
{
    protected array $format = ['L', 'L'];

    /**
     * @var int[]
     */
    protected array $integers;

    /**
     * @inheritDoc
     */
    public function getPack(): string
    {
        return pack(implode('', $this->getFormat()), ...$this->getIntegers());
    }

    public static function fromPack($packString): static
    {
        $instance = new static();
        $formats = [];
        foreach ($instance->getFormat() as $index => $format) {
            $formats[] = $format . 'index' . $index;
        }
        $instance->setIntegers(array_values(unpack(implode('/', $formats), $packString)));
        return $instance;
    }

    /**
     * @param int[] $integers
     * @return static
     */
    public static function fromIntegers(array $integers): static
    {
        $instance = new static();
        $instance->setIntegers($integers);
        return $instance;
    }

    /**
     * @return int[]
     */
    public function getIntegers(): array
    {
        return $this->integers;
    }

    /**
     * @return string[]
     */
    public function getFormat(): array
    {
        return $this->format;
    }

    /**
     * @param int[] $integers
     * @return $this
     */
    public function setIntegers(array $integers): self
    {
        if (count($integers) != count($this->getFormat())) {
            throw new \RuntimeException('数字数量与format不匹配');
        }
        $this->integers = $integers;
        return $this;
    }
}