<?php
declare(strict_types=1);

namespace Madoka\Constants;

use BadMethodCallException;
use Hyperf\Constants\ConstantsCollector;
use Hyperf\Context\ApplicationContext;
use Hyperf\Stringable\Str;
use ReflectionClass;

trait EnumConstantsTrait
{
    public function __call(string $name, array $arguments): mixed
    {
        if (!Str::startsWith($name, 'get')) {
            throw new BadMethodCallException('The function is not defined!');
        }
        $className = $this::class;
        if (!ConstantsCollector::has($className)) {
            if (!ApplicationContext::hasContainer()) {
                return null;
            }
            // 解析枚举值
            $reflectionClass = new ReflectionClass($className);
            $constants = ApplicationContext::getContainer()->get(Constants::class);
            $constants->loadConstProperty($reflectionClass);
            ConstantsCollector::set($className, $constants->loadConstProperty($reflectionClass));
        }

        $name = lcfirst(substr($name, 3));

        $message = ConstantsCollector::getValue($className, $this->name, $name);

        $count = count($arguments);
        if ($count > 0) {
            return sprintf($message, ...$arguments);
        }

        return $message;
    }
}