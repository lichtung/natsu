<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:18
 */

namespace Natsu\Core;


use Natsu\Kernel;

trait Drive
{

    /**
     * 获取驱动实例
     * @param string $index 驱动器角标
     * @return DriverInterface  返回驱动实例
     * @throws DriverNotFoundException 适配器未定义
     * @throws ClassNotFoundException  适配器类不存在
     */
    public function drive(string $index = 'default')
    {
        static $_instances = [];
        $key = static::class . '-' . $index;
        if (isset($_instances[$key])) {
            $config = Kernel::getInstance()->config(static::class);
            if (isset($config['drivers'][$this->index])) {
                $driverName = $config['drivers'][$this->index]['name'];
                $driverConfig = $config['drivers'][$this->index]['config'] ?? [];
                $_instances[$key] = new $driverName($driverConfig, $this);
            } else {
//                throw new DriverNotFoundException("driver '{$this->index}' not found");
            }
        }
        return $_instances[$key];
    }

    /**
     * 访问驱动对应的方法
     * @param string $name 方法名称
     * @param array $arguments 方法参数列表
     * @return mixed 返回对应方法的返回值
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->drive(), $name], $arguments);
    }

}