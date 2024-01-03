<?php
declare(strict_types=1);

namespace Madoka\View;

use Hyperf\View\Engine\EngineInterface;

/**
 * 自定义视图驱动
 * Class DummyTemplateEngine
 * @package Madoka\Cipher\View
 */
class DummyTemplateEngine implements EngineInterface
{
    public function render($template, $data, $config): string
    {
        $engine = new DummyTemplate($config);
        return $engine->render($template, $data);
    }
}