<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 22:27
 */

namespace Emanon\Throwable\Core;

use Emanon\Throwable\Exception;

/**
 * Class ClassInstantiationException 类实例化异常
 * @package Emanon\Throwable\Core
 */
class ClassInstantiationException extends Exception
{
    public function __construct(string $className)
    {
        parent::__construct("failed to instantiate [{$className}].");
    }
}