<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 20:55
 */

namespace Emanon\Core;

/**
 * Class Request 请求类
 * @package Emanon\Core
 */
class Request extends \Symfony\Component\HttpFoundation\Request
{
    use Singleton;

    /**
     *
     * @return Request
     */
    public static function getInstance(): Request
    {
        if (Command::isCommandLineInterface()) {
            return new static(); # $_GET $_POST 等是空的
        } else {
            $instance = self::createFromGlobals();

            switch ($instance->getRequestMethod()) {
                case '':# client script
                    break;
                case 'GET': # Get resource from server(one or more)
                    break;
                case 'POST': # Create resource
                    break;
                case 'PUT': # Update resource with full properties
                case 'PATCH': # Update resource with some properties
                case 'DELETE': # Delete resource
                    if ($_input = file_get_contents('php://input') ?: '') {
                        parse_str($_input, $_request_data);
                        $_request_data and $_REQUEST = array_merge($_REQUEST, $_request_data);
                    }
                    break;
            }
            return $instance;
        }
    }

    /**
     * 当请求的方法为PUT,PATCH,DELETE时，需要手动去请求的body中获取
     * php://input is a read-only stream that allows you to read raw data from the request body
     * 获取请求的原始数据的流输入 PHP 5.6 之前 php://input 打开的数据流只能读取一次 @see http://php.net/manual/zh/wrappers.php.php
     * @return string
     */
    public function getRawRequestBody(): string
    {
        return (string)file_get_contents('php://input');
    }

    /**
     * 是否是ajax请求
     * @return bool
     */
    public function isAjax(): bool
    {
        return 'XMLHttpRequest' === $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    }

    /**
     * 获取http请求方法，cli模式下返回空
     * @return string
     */
    public function getRequestMethod(): string
    {
        return strtoupper($_SERVER['X-HTTP-METHOD-OVERRIDE'] ?? $_SERVER['REQUEST_METHOD'] ?? '');
    }
}