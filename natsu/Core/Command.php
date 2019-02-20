<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/7
 * Time: 15:05
 */

namespace Emanon\Core;

/**
 * Class Command
 * @package Emanon\Core
 */
abstract class Command extends \Symfony\Component\Console\Command\Command
{
    use Singleton;

    /**
     * 是否是命令行模式
     * @return bool
     */
    public static function isCommandLineInterface(): bool
    {
        return php_sapi_name() === 'cli'; # PHP_SAPI
    }



}