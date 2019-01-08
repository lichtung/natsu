<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 18:12
 */

namespace Natsu;

use Natsu\Core\Command;
use Natsu\Throwable\Core\ClassInstantiationException;
use Natsu\Throwable\Core\ClassNotFoundException;
use ReflectionException;
use ReflectionClass;
use Natsu\Core\Dispatcher;
use Natsu\Core\Request;
use Natsu\Core\Route;
use Natsu\Core\Singleton;
use Natsu\Throwable\IO\File\NotFoundException as FileNotFoundException;
use Natsu\Throwable\IO\File\WriteException as FileWriteException;
use Natsu\Throwable\Validation\FormatException;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

define('NT_BASE_PATH', __DIR__ . '/../');
define('NT_RUNTIME_PATH', NT_BASE_PATH . 'runtime/');

/**
 * Class Kernel 框架核心
 * @package Natsu
 */
class Kernel
{
    use Singleton;

    /** @var string 版本号 */
    const VERSION = '1.0.0';

    const TYPE_BOOL = 'boolean';
    const TYPE_INT = 'integer';
    const TYPE_FLOAT = 'double'; # gettype(1.7) === 'double'
    const TYPE_STR = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJ = 'object'; # gettype(function (){})
    const TYPE_RESOURCE = 'resource';
    const TYPE_NULL = 'NULL'; # gettype(null) === 'NULL'
    const TYPE_UNKNOWN = 'unknown type';

    /** @var array 状态列表(运行时、内存) */
    private static $status = [];
    /** @var array 核心配置 */
    private $config = [
        'timezone_zone' => 'Asia/Shanghai',
        'session.save_handler' => 'files',# redis
        'session.save_path' => NT_RUNTIME_PATH,# tcp://127.0.0.1:6379
        'session.gc_maxlifetime' => 3600,
        'session.cache_expire' => 3600,
        'debug' => true,
        'midware' => [
        ],
    ];

    /**
     * Kernel constructor.
     * @param array $config
     */
    private function __construct(array $config = [])
    {
        self::status('before_initialize');
        $this->initialize($config);
        self::status('after_initialize');
    }

    #################################### 实例方法 ################################################

    private function initialize(array $config): void
    {
        # 合并配置
        if ($config) foreach ($config as $key => $value) {
            if (is_array($value)) { # $key 可能是一个类名
                $value = array_merge($this->config[$key] ?? [], $value);
            }
            $this->config[$key] = $value;
        }

        date_default_timezone_set($this->config['timezone_zone']) or die('timezone set failed!');
        # ini_set('expose_php', 'Off'); # ini_set 无效，需要修改 php.ini 文件
        false === ini_set('session.save_handler', $this->config['session.save_handler']) and die('set session.save_handler failed');
        false === ini_set('session.save_path', $this->config['session.save_path']) and die('set session.save_path failed');
        false === ini_set('session.gc_maxlifetime', (string)$this->config['session.gc_maxlifetime']) and die('set session.gc_maxlifetime failed');
        false === ini_set('session.cache_expire', (string)$this->config['session.cache_expire']) and die('set session.cache_expire failed');


        if ($this->config['debug']) {
            ExceptionHandler::register(); # registers an error handler, an exception handler and a special class loader
            ErrorHandler::register();
        } else {

        }

    }

    public function start()
    {
        if (!Command::isCommandLineInterface()) {
            self::status('start');
            $pathInfo = Request::getInstance()->getPathInfo();
            [$module, $controller, $action, $params] = Route::getInstance()->parse($pathInfo);
            $response = Dispatcher::getInstance()->dispatch($module, $controller, $action, $params);
            echo $response;
        }
    }

    /**
     * 获取类配置
     * @param string $className
     * @return array
     */
    public function config(string $className): array
    {
        return $this->config[$className] ?? [];
    }

    #################################### 静态方法 ################################################

    public static function status(string $tag)
    {
        self::$status[$tag] = [microtime(true), memory_get_usage()];
    }

    /**
     * @param string $file
     * @return array
     * @throws FileNotFoundException 配置文件地址不存在
     * @throws FormatException 配置文件返回的不是数组
     */
    public static function readConfig(string $file): array
    {
        if (!is_file($file)) throw new FileNotFoundException($file);
        $result = include $file;
        if (!is_array($file)) throw new FormatException("expect [$file] return array.");
        return $result;
    }

    /**
     * @param string $file
     * @param array $config
     * @return void
     * @throws FileWriteException 创建文件失败时抛出
     */
    public static function writeConfig(string $file, array $config): void
    {
        $dirname = dirname($file);
        if (!is_dir($dirname)) {
            if (false === mkdir($dirname, 0777, true)) {
                throw new FileWriteException($dirname); #  文件夹也是文件
            }
        }
        $content = '<?php class_exists(Kernel::class, false) or die(\'No Permission\'); return ' . var_export($config, true) . ';';
        if (!file_put_contents($file, $content)) {
            throw new FileWriteException($file);
        }
    }

    /**
     * 将参数转为散列值(哈希)
     * @param mixed $params
     * @return string
     */
    public static function hash($params): string
    {
        $hash = '';
        switch (gettype($params)) {
            case self::TYPE_ARRAY:
                foreach ($params as $item) $hash .= self::hash($item);
                break;
            case self::TYPE_OBJ:
                $hash = spl_object_hash($params);
                break;
            case self::TYPE_RESOURCE:
                $hash = get_resource_type($params);
                break;
            default:
                $hash = serialize($params);
        }
        return sha1($hash);
    }


    /**
     * @param string $className
     * @return ReflectionClass
     * @throws ClassNotFoundException
     */
    public static function reflect(string $className): ReflectionClass
    {
        static $_instances = [];
        if (!isset($_instances[$className])) {
            try {
                $_instances[$className] = new ReflectionClass($className);
            } catch (ReflectionException $exception) { # ReflectionException will be thrown if class does not exist
                throw new ClassNotFoundException($exception);
            }
        }
        return $_instances[$className];
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return object
     * @throws ClassNotFoundException
     * @throws ClassInstantiationException
     */
    public static function instantiate(string $className, array $arguments = [])
    {
        static $_instances = [];
        $key = $className . self::hash($arguments);
        if (!isset($_instances[$key])) {
            $reflectionClass = self::reflect($className);
            try {
                $_instances[$key] = $arguments ? $reflectionClass->newInstanceArgs($arguments) : $reflectionClass->newInstance();
            } catch (ReflectionException $exception) { # 构造方法私有或者保护时抛出
                throw new ClassInstantiationException($className);
            }
        }
        return $_instances[$key];
    }
}