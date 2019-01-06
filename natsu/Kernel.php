<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 18:12
 */

namespace Natsu;

use Natsu\Core\Request;
use Natsu\Core\Singleton;
use Natsu\Throwable\IO\File\NotFoundException as FileNotFoundException;
use Natsu\Throwable\IO\File\WriteException as FileWriteException;
use Natsu\Throwable\Validation\FormatException;

/**
 * Class Kernel 框架核心
 * @package Natsu
 */
final class Kernel
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
    private $config = [];

    /**
     * Kernel constructor.
     * @param
     */
    private function __construct(array $config = [])
    {
        self::status('onBegin');
        $this->initialize($config);
    }

    #################################### 实例方法 ################################################

    private function initialize(array $config): void
    {
        $config and $this->config = array_merge($this->config, $config);

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

    public function start()
    {
        $request = Request::getInstance();
        if ($request->isCommandLineInterface()) {

        } else {
            $pathInfo = $request->getPathInfo();

        }
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
}