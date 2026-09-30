<?php
declare(strict_types=1);

namespace Madoka\Constants\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS_CONSTANT | Attribute::IS_REPEATABLE)]
class ConstProperty
{
    public string $name;
    public mixed $value;

    public function __construct(string $name, mixed $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
}
