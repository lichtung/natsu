<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 22:27
 */

namespace Natsu\Throwable\Core;

use Natsu\Throwable\NatsuException;

/**
 * Class ClassInstantiationException 类实例化异常
 * @package Natsu\Throwable\Core
 */
class ClassInstantiationException extends NatsuException
{
    public function __construct(string $className)
    {
        parent::__construct("failed to instantiate [{$className}].");
    }
}