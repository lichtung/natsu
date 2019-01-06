<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 20:21
 */

namespace Natsu\Throwable\Core;


use Natsu\Throwable\NatsuException;

/**
 * Class InstantiateException 实例化失败
 * @package Natsu\Throwable\Core
 */
class InstantiateException extends NatsuException
{
    public function __construct(string $className, string $message)
    {
        parent::__construct("{$className}:{$message}");
    }

}