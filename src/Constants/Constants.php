<?php
declare(strict_types=1);

namespace Madoka\Constants;

use Attribute;
use Hyperf\Constants\ConstantsCollector;
use Hyperf\Di\Annotation\AbstractAnnotation;
use Madoka\Constants\Annotation\ConstProperty;
use ReflectionClass;

#[Attribute(Attribute::TARGET_CLASS)]
class Constants extends AbstractAnnotation
{
    public function collectClass(string $className): void
    {
        $ref = new ReflectionClass($className);
        ConstantsCollector::set($className, $this->loadConstProperty($ref));
    }

    /**
     * @param ReflectionClass $ref
     * @return array
     */
    public function loadConstProperty(ReflectionClass $ref): array
    {
        $results = [];
        $constants = $ref->getReflectionConstants();
        if (!empty($constants)) {
            foreach ($constants as $constant) {
                $attributes = $constant->getAttributes();
                if (!empty($attributes)) {
                    foreach ($attributes as $attribute) {
                        $instance = $attribute->newInstance();
                        if ($instance instanceof ConstProperty) {
                            $results[$constant->getName()][$instance->name] = $instance->value;
                        }
                    }
                }
            }
        }
        return $results;
    }
}