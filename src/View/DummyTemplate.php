<?php
declare(strict_types=1);

namespace Madoka\View;

use Madoka\Exception\TemplateException;

/**
 * 自定义视图处理
 * Class DummyTemplate
 * @package Madoka\Cipher\View
 */
class DummyTemplate
{
    /**
     * @var array
     */
    public $config = [
        'view_path'     => '',
        'cache_path'    => '',
    ];

    /**
     * DummyTemplate constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * @param string $template
     * @param array $data
     * @return false|string
     */
    public function render($template, $data){
        $templateFile = $this->parseTemplateFile($template);
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        if(!empty($data)){
            extract($data, EXTR_OVERWRITE);
        }
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * @param string $template
     * @return string
     */
    private function parseTemplateFile($template){
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            if (0 !== strpos($template, '/')) {
                $template = str_replace(['/', ':'], DIRECTORY_SEPARATOR, $template);
            } else {
                $template = str_replace(['/', ':'], DIRECTORY_SEPARATOR, substr($template, 1));
            }

            $template = $this->config['view_path'] . $template . '.' . ltrim('php', '.');
        }

        if (is_file($template)) {
            return $template;
        }

        throw new TemplateException('template not exists:' . $template);
    }
}