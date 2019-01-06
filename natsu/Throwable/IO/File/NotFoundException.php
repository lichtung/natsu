<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 19:18
 */

namespace Natsu\Throwable\IO\File;

use Natsu\Throwable\IO\IOException;

/**
 * Class NotFoundException  文件不存在异常
 * @package Natsu\Throwable\IO\File
 */
class NotFoundException extends IOException
{
    /**
     * NotFoundException constructor.
     * @param string $file 文件地址
     */
    public function __construct(string $file)
    {
        parent::__construct("file [{$file}] not found.");
    }
}