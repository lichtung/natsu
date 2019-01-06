<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 20:55
 */

namespace Natsu\Core;

/**
 * Class Request 请求类
 * @package Natsu\Core
 */
class Request extends \Symfony\Component\HttpFoundation\Request
{
    use Singleton;

    /**
     * @return Request
     */
    public static function getInstance(): Request
    {
        if (self::isCommandLineInterface()) {
            return new static(); # $_GET $_POST 等是空的
        } else {
            return self::createFromGlobals();
        }
    }


    /**
     * 是否是命令行模式
     * @return bool
     */
    public static function isCommandLineInterface(): bool
    {
        return PHP_SAPI === 'cli'; # cli === command line interface
    }
}