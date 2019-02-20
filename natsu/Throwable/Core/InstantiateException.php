<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 20:21
 */

namespace Emanon\Throwable\Core;


use Emanon\Throwable\Exception;

/**
 * Class InstantiateException 实例化失败
 * @package Emanon\Throwable\Core
 */
class InstantiateException extends Exception
{
    public function __construct(string $className, string $message)
    {
        parent::__construct("{$className}:{$message}");
    }

}