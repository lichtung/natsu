<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/7
 * Time: 12:31
 */

namespace Emanon\Core;


abstract class Midware
{
    use Singleton;

    /**
     * @param Request $request
     * @param Response $response
     * @return bool 是否继续往下走
     */
    abstract public function handle(Request $request, Response $response): bool;

}