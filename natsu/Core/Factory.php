<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 18:47
 */

namespace Emanon\Core;

use Emanon\Constract\Configurable;
use Emanon\Kernel;

/**
 * Trait Factory 工厂Trait
 * @package Emanon\Core
 */
trait Factory
{
    /**
     * 根据参数获取对应的实例
     * @param mixed ...$arguments
     * @return static
     */
    public static function factory(...$arguments)
    {
        static $_multiple_instances = [];
        $static = static::class;
        isset($_multiple_instances[$static]) or $_multiple_instances[$static] = [];
        $index = Kernel::hash($arguments);
        if (!isset($_multiple_instances[$static][$index])) {
            $instance = new static();
            if ($instance instanceof Configurable) {
                Kernel::getInstance()->applyConfig($instance, ... $arguments);
            }
            $_multiple_instances[$static][$index] = $instance; # 分拆数组
        }
        return $_multiple_instances[$static][$index];
    }

    private function __construct(...$arguments)
    {
    }
}