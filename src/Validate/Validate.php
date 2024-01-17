<?php
declare (strict_types=1);

namespace Madoka\Validate;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Hyperf\Validation\ValidationException;
use Psr\Container\ContainerInterface;
use Hyperf\Contract\ValidatorInterface;

abstract class Validate
{
    /**
     * @var ValidatorFactoryInterface
     */
    private ValidatorFactoryInterface $validatorFactory;

    public function __construct(ContainerInterface $container)
    {
        $this->validatorFactory = $container->get(ValidatorFactoryInterface::class);
    }

    /**
     * 默认验证规则
     * @var array
     */
    private static array $defaultRule = [
        'page' => 'integer|min:1',
        'size' => 'integer|min:1',
        'last_id' => 'integer|min:1'
    ];

    /**
     * @param array $data
     * @param string|null $scene
     * @return ValidatorInterface
     */
    public function check(array $data, string $scene = null): ValidatorInterface
    {
        $validator = $this->validatorFactory->make($data, $this->getRule($scene), $this->messages(), $this->attributes());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
        return $validator;
    }

    /**
     * 当前验证规则
     * @return array
     */
    abstract public function rules(): array;

    /**
     * 验证提示信息
     * @return array
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * 自定义场景
     * @return array
     */
    abstract public function scenes(): array;

    /**
     * 自定义属性名称
     * @return array
     */
    public function attributes(): array
    {
        return [];
    }

    private function getRule(string $scene = null): array
    {
        $rules = [];
        $rule = array_merge(self::$defaultRule, $this->rules());
        $scenes = $this->scenes();
        if (!is_null($scene) && isset($scenes[$scene])) {
            foreach ($scenes[$scene] as $key) {
                $rules[$key] = $rule[$key];
            }
        } else {
            $rules = $rule;
        }
        return $rules;
    }
}