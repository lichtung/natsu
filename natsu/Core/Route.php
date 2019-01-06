<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:07
 */

namespace Natsu\Core;

use Natsu\Throwable\Core\MethodNotFoundException;

/**
 * Class Route 路由
 *
 * @method void get(string $url, $rule) static
 * @method void post(string $url, $rule) static
 * @method void put(string $url, $rule) static
 * @method void delete(string $url, $rule) static
 * @method void any(string $url, $rule) static
 *
 * @method Route factory(Request $request) static
 * @package Natsu\Core
 */
class Route
{
    use Factory;
    /** @var string 当前分组 */
    private static $currentGroup = '';
    /** @var array 静态路由，直接匹配请求地址 */
    private static $staticRoute = [];
    /** @var array 正则路由，使用正则表达式匹配路由地址 */
    private static $wildcardRoute = [];
    /** @var Request 请求 */
    private $request;

    /**
     * Route constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return void
     * @throws MethodNotFoundException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (in_array($name, ['get', 'post', 'delete', 'put', 'any'])) {
            $url = $arguments[0];
            $rule = $arguments[1];
            self::$currentGroup and $url = '/' . self::$currentGroup . '/' . ltrim($url, '/');
            if (strpos($url, '{') === false and strpos($url, '[') === false) {
                self::$staticRoute[$name . '-' . $url] = $rule;
            } else {
                self::$wildcardRoute[$name . '-' . $url] = $rule;
            }
        } else {
            throw new MethodNotFoundException($name, static::class);
        }
    }
}