<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 19:55
 */

namespace Canan\Core;

use Canan\Constract\Configurable;
use Canan\Kernel;

/**
 * Trait Singleton 单例Trait
 * 备注：
 * 使用如下的代码实例化非共有构造的类时会抛出异常，因为反射是从外部进行的
 *  (new \ReflectionClass($className))->newInstance();
 * 使用如下的代码实例化可以解决这类问题，因为它是从内部访问的
 *  new $className();
 * @package Canan\Core
 */
trait Singleton
{
    /**
     * 获取单例
     * @return static
     */
    public static function getInstance()
    {
        static $_instances = [];
        $className = static::class;
        if (!isset($_instances[$className])) {
            $instance = new $className();
            if ($instance instanceof Configurable) {
                Kernel::getInstance()->applyConfig($instance);
            }
            $_instances[$className] = $instance;
        }
        return $_instances[$className];
    }
}