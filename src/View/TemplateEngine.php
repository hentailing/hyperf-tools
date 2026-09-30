<?php
declare(strict_types=1);

namespace Madoka\View;

use Hyperf\View\Engine\EngineInterface;

/**
 * 自定义视图驱动
 * Class TemplateEngine
 * @package Madoka\View
 */
class TemplateEngine implements EngineInterface
{
    public function render(string $template, array $data, array $config): string
    {
        $engine = new Template($config);
        return $engine->render($template, $data);
    }
}