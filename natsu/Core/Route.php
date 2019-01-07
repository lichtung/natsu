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
 * @package Natsu\Core
 */
class Route
{
    use Singleton;
    /** @var string 当前分组 */
    private static $currentGroup = '';
    /** @var array 静态路由，直接匹配请求地址 */
    private static $staticRoute = [];
    /** @var array 正则路由，使用正则表达式匹配路由地址 */
    private static $wildcardRoute = [];
    /** @var array 虚拟主机与控制器的绑定 */
    private static $vhost2controller = [];
    /** @var Request 请求 */
    private $request;
    /** @var array */
    private $config = [
        'default_modules' => '',
        'default_controller' => 'index',
        'default_action' => 'index',

        'api_mode_on' => false,
        'api_modules_variable' => 'm',
        'api_controller_variable' => 'c',
        'api_action_variable' => 'a',
    ];

    /**
     * Route constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 解析路由并设置路由信息
     * @param string $pathInfo
     * @return Route
     */
    public function parse(string $pathInfo): Route
    {
        if (!empty(self::$vhost2controller)) {
            $host = $_SERVER['HTTP_HOST'] ?? '';
            if ($controller = self::$vhost2controller[$host] ?? null) {
                $action = trim($pathInfo, '/');
                # $controller, $action ?: $this->config['default_action']
                return $this;
            }
        }

        $method = strtolower(DRI_REQUEST_METHOD);
        # 静态式路由
        if (!empty(self::$staticRoute) and $rule = self::$staticRoute[$method . '-' . $pathInfo] ?? self::$staticRoute['any-' . $pathInfo] ?? false) {
            return $rule;
        } elseif ($wildcard = self::$wildcardRoute) {
            # 规则式路由
            foreach ($wildcard as $pattern => $rule) {
                if (strpos($pattern, $method) === 0) { # 检查请求方法
                    $pattern = substr($pattern, strlen($method) + 1);
                } elseif (strpos($pattern, 'any') === 0) {
                    $pattern = substr($pattern, 4);
                } else {
                    continue;
                }
                $matched = self::match($pathInfo, $pattern);
                if (isset($matched)) {
                    $request->setParams($matched);
                    return $rule;
                }
            }
        }
        list($modules, $controller, $action) = Request::parsePathInfo($pathInfo);
        $modules or $modules = $this->config['default_modules'];

        $request->setModule(is_array($modules) ? implode('/', $modules) : $modules);
        $request->setController($controller ?: $this->config['default_controller']);
        $request->setAction($action ?: $this->config['default_action']);
        return $this;
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