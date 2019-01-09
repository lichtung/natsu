<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:17
 */

namespace Canan\Throwable\Core;


use Canan\Throwable\Exception;

class MethodNotFoundException extends Exception
{

    public function __construct(string $method, string $className)
    {
        parent::__construct("method [{$className}::{$method}] not found");
    }
}