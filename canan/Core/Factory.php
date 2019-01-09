<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 18:47
 */

namespace Canan\Core;

use Canan\Kernel;

/**
 * Trait Factory 工厂Trait
 * @package Canan\Core
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
            $_multiple_instances[$static][$index] = new static(...$arguments); # 分拆数组
        }
        return $_multiple_instances[$static][$index];
    }

    private function __construct(...$arguments)
    {
    }
}