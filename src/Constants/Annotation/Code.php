<?php
declare(strict_types=1);

namespace Madoka\Constants\Annotation;

use Attribute;

/**
 * 名称为code值为int
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Code extends ConstProperty
{
    public function __construct(int $code)
    {
        parent::__construct("code", $code);
    }
}
