<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 20:21
 */

namespace Canan\Throwable\Core;


use Canan\Throwable\Exception;

/**
 * Class InstantiateException 实例化失败
 * @package Canan\Throwable\Core
 */
class InstantiateException extends Exception
{
    public function __construct(string $className, string $message)
    {
        parent::__construct("{$className}:{$message}");
    }

}