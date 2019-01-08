<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:57
 */

namespace Natsu\Core;

use Natsu\Kernel;
use Natsu\Throwable\Core\ActionNotFoundException;
use Natsu\Throwable\Core\ClassInstantiationException;
use Natsu\Throwable\Core\ClassNotFoundException;
use Natsu\Throwable\Core\ControllerNotFoundException;
use Natsu\Throwable\Core\ParameterNotFoundException;
use ReflectionClass;
use ReflectionException;
use Throwable;

/**
 * Class Dispatcher
 * @package Natsu\Core
 */
class Dispatcher
{
    use Singleton;

    /**
     * @param string $module
     * @param string $controller
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws ActionNotFoundException
     * @throws ClassInstantiationException
     * @throws ControllerNotFoundException
     * @throws ParameterNotFoundException
     * @throws ReflectionException
     */
    public function dispatch(string $module, string $controller, string $method, array $params = [])
    {
        $controllerName = 'App\\Http\\Controller\\';
        if ($module = trim($module, '/ ')) {
            if (strpos($module, '/')) { # 多个模块以'/'分隔,拆分并首字母转大写
                $modules = explode('/', $module);
                foreach ($modules as $index => $module) {
                    $modules[$index] = ucfirst($module);
                }
                $controllerName .= implode('\\', $modules) . '\\';
            } else {
                $controllerName .= ucfirst($module) . '\\';
            }
        }
        $controllerName .= ucfirst($controller);
        return self::runMethod($controllerName, $method, $params);
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @param array|null $arguments
     * @return void
     * @throws ActionNotFoundException
     * @throws ReflectionException
     * @throws ControllerNotFoundException
     * @throws ParameterNotFoundException
     * @throws ClassInstantiationException
     */
    public static function runMethod(string $controllerName, string $actionName, array $arguments = [])
    {
        try {
            /** @var ReflectionClass $controller */
            $controller = Kernel::reflect($controllerName);
            try {
                $method = $controller->getMethod($actionName); # A ReflectionException if the method does not exist.
            } catch (Throwable $e) {
                # A ReflectionException if the method does not exist.
                throw new ActionNotFoundException($actionName);
            }

            # 非公开方法、静态方法、以下划线开头的方法都是被禁止访问的
            if (!$method->isPublic() or $method->isStatic() or strpos($method->name, '_') === 0) {
                throw new ActionNotFoundException($method->name);
            }
            $controller = Kernel::instantiate($controllerName);
        } catch (ClassNotFoundException $e) {
            throw new ControllerNotFoundException($controllerName);
        }

//        $mc = explode('\\', substr($controllerName, 20));#strlen('App\Http\Controller\') == 10
        # 建立请求常量
//        Request::factory()->setController(array_pop($mc) ?? '')
//            ->setModule($mc ? implode('/', $mc) : '')
//            ->setAction($actionName);

        if ($method->getNumberOfParameters()) {//有参数
            $args = [];
            /** @var \ReflectionParameter[] $methodParams */
            $methodParams = $method->getParameters();
            isset($arguments) or $arguments = Command::isCommandLineInterface() ? getopt('p:') : $_REQUEST;
            if ($methodParams) {
                foreach ($methodParams as $param) {
                    $paramName = $param->getName();
                    if (isset($arguments[$paramName])) {
                        # filter dangerous input
                        $args[] = self::_filter($arguments[$paramName]);
                    } elseif ($param->isDefaultValueAvailable()) {
                        $args[] = $param->getDefaultValue();
                    } else {
                        throw new ParameterNotFoundException($paramName);
                    }
                }
            }
            $result = $method->invokeArgs($controller, $args);
        } else {
            $result = $method->invoke($controller);
        }
        if (isset($result) and $result instanceof Response) {
            echo $result;
        }
    }

    private static function _filter(string $str): string
    {
        return htmlentities(strip_tags($str), ENT_QUOTES, 'utf-8');
    }
}