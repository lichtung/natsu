<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:07
 */

namespace Emanon\Core;

use Emanon\Throwable\Core\MethodNotFoundException;

/**
 * Class Route 路由
 *
 * @method void get(string $url, $rule) static
 * @method void post(string $url, $rule) static
 * @method void put(string $url, $rule) static
 * @method void delete(string $url, $rule) static
 * @method void any(string $url, $rule) static
 *
 * @package Emanon\Core
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
     * 解析路由并设置路由信息
     * @param string $pathInfo
     * @return array
     */
    public function parse(string $pathInfo): array
    {
        $method = strtolower(Request::getInstance()->getRequestMethod());
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
                    $ruleParams = $rule[4] ?? [];
                    $rule[4] = array_merge($ruleParams, $matched);
                    return $rule;
                }
            }
        }
        list($modules, $controller, $action) = self::parsePathInfo($pathInfo);
        $modules or $modules = $this->config['default_modules'];

        $modules = is_array($modules) ? implode('/', $modules) : $modules;
        $controller = $controller ?: $this->config['default_controller'];
        $action = $action ?: $this->config['default_action'];
        return [$modules, $controller, $action, []];
    }

    public static function parsePathInfo(string $pathInfo): array
    {
        $parsed = [[], '', ''];
        if ($pathInfo = trim($pathInfo, ' /')) {
            $capos = strrpos($pathInfo, '/');
            if (false === $capos) {
                $parsed[2] = $pathInfo;
            } else {
                $parsed[2] = substr($pathInfo, $capos + 1);

                //CA存在衔接符 则说明一定存在控制器
                $mcaLength = strlen($pathInfo);
                $mcPart = substr($pathInfo, 0, $capos - $mcaLength);

                if (strlen($pathInfo)) {
                    $mcPosition = strrpos($mcPart, '/');
                    if (false === $mcPosition) {
                        //不存在模块
                        if (strlen($mcPart)) {
                            //全部是控制器的部分
                            $parsed[1] = $mcPart;
                        }   //没有控制器部分，则使用默认的
                    } else {
                        //截取控制器的部分
                        $parsed[1] = substr($mcPart, $mcPosition + 1);

                        //既然存在MC衔接符 说明一定存在模块
                        $mPart = substr($mcPart, 0, $mcPosition - strlen($mcPart));//以下的全是模块部分的字符串
                        if (strlen($mPart)) {
                            if (false === strpos($mPart, '/')) {
                                $parsed[0] = [$mPart];
                            } else {
                                $parsed[0] = explode('/', $mPart);
                            }
                        }
                    }
                }
            }
        }
        return $parsed;
    }

    /**
     * @param string $pathInfo
     * @param string $pattern 正则/规则式
     * @return null|array 匹配成功返回数组，里面包含匹配出来的参数；不匹配时返回null
     */
    public static function match(string $pathInfo, string $pattern)
    {
        static $_matchCache = [];
        if (strpos($pattern, '{') !== false) {
            if (isset($_matchCache[$pattern])) {
                list($compiledPattern, $params) = $_matchCache[$pattern];
            } else {
                $params = [];
                $compiledPattern = preg_replace_callback('/\{[^\}]+?\}/', function ($matches) use (&$params) {
                    if ($name = $matches[0] ?? false) { # $matches[0]是完成的匹配 $matches[1]是第一个捕获子组的匹配（没有子组）
                        $params[trim($name, '{}')] = null;
                        return '([^/]+)';
                    } else {
                        return '';
                    }
                }, $pattern);
            }
        } else {
            $compiledPattern = $pattern; # 纯正则表达式
            $params = null;
        }
        $result = preg_match('#^' . $compiledPattern . '$#', rtrim($pathInfo, '/'), $matches);
        if ($result) { # 使用 '#' 代替开头和结尾的 '/'，可以忽略 $pattern 中的 "/"
            array_shift($matches);
            if (isset($params)) {
                $index = 0;
                foreach ($params as $name => &$val) {
                    $val = $matches[$index++] ?? null;
                }
            } else {
                $params = $matches;
            }
        } else {
            $params = null;
        }
        return $params;
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