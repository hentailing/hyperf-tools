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
    private $validatorFactory;

    public function __construct(ContainerInterface $container)
    {
        $this->validatorFactory = $container->get(ValidatorFactoryInterface::class);
    }

    /**
     * 默认验证规则
     * @var array
     */
    private static $defaultRule = [
        'page'      =>  'integer|min:1',
        'size'      =>  'integer|min:1',
        'last_id'   =>  'integer|min:1'
    ];

    /**
     * @param $data
     * @param string $scene
     * @return ValidatorInterface
     * @throws ValidationException
     */
    public function check($data, $scene = null){
        $validator = $this->validatorFactory->make($data, $this->getRule($scene), $this->messages(), $this->attributes());
        if( $validator->fails() ){
            throw new ValidationException($validator);
        }
        return $validator;
    }

    /**
     * 当前验证规则
     * @return array
     */
    abstract public function rules();

    /**
     * 验证提示信息
     * @return array
     */
    public function messages(){
        return [];
    }

    /**
     * 自定义场景
     * @return array
     */
    abstract public function scenes();

    /**
     * 自定义属性名称
     * @return array
     */
    public function attributes(){
        return [];
    }

    private function getRule($scene = null){
        $rules = [];
        $rule = array_merge(self::$defaultRule, $this->rules());
        $scenes = $this->scenes();
        if( !is_null($scene) && isset( $scenes[$scene] ) ){
            foreach ($scenes[$scene] as $key){
                $rules[$key] = $rule[$key];
            }
        }else{
            $rules = $rule;
        }
        return $rules;
    }
}