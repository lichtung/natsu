<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 22:27
 */

namespace Canan\Throwable\Core;

use Canan\Throwable\Exception;

/**
 * Class ClassInstantiationException 类实例化异常
 * @package Canan\Throwable\Core
 */
class ClassInstantiationException extends Exception
{
    public function __construct(string $className)
    {
        parent::__construct("failed to instantiate [{$className}].");
    }
}