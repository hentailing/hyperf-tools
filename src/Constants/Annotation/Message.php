<?php
declare(strict_types=1);

namespace Madoka\Constants\Annotation;

use Attribute;

/**
 * 名称为message值为string
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class Message extends ConstProperty
{
    public function __construct(string $value)
    {
        parent::__construct("message", $value);
    }
}
