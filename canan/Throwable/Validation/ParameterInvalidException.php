<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/9
 * Time: 14:58
 */

namespace Canan\Throwable\Validation;


use Canan\Throwable\Exception;

class ParameterInvalidException extends Exception
{
    public function __construct($param, string $message = "")
    {
        parent::__construct($message . ' now is:' . var_export($param, true));
    }
}