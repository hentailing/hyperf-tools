<?php
declare(strict_types=1);

namespace Madoka\View;

/**
 * 自定义视图处理
 * Class Template
 * @package Madoka\View
 */
class Template
{
    /**
     * @var array
     */
    public array $config = [
        'view_path' => '',
        'cache_path' => '',
    ];

    /**
     * Template constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $template
     * @param array $data
     * @return false|string
     */
    public function render(string $template, array $data): bool|string
    {
        $templateFile = $this->parseTemplateFile($template);
        // 页面缓存
        ob_start();
        ob_implicit_flush(false);
        if (!empty($data)) {
            extract($data);
        }
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * @param string $template
     * @return string
     */
    private function parseTemplateFile(string $template): string
    {
        if ('' == pathinfo($template, PATHINFO_EXTENSION)) {
            if (!str_starts_with($template, '/')) {
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