<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:18
 */

namespace Natsu\Core;


trait Drive
{

    /**
     * 获取驱动实例
     * @param string $index 驱动器角标
     * @return DriverInterface  返回驱动实例
     * @throws DriverNotFoundException 适配器未定义
     * @throws ClassNotFoundException  适配器类不存在
     */
    public function drive(string $index = '')
    {

        $index and $this->index = $index;
        if (!isset($this->driver)) {
            if (isset($this->config['drivers'][$this->index])) {
                $this->driverName = $this->config['drivers'][$this->index]['name'];
                $this->driverConfig = $this->config['drivers'][$this->index]['config'] ?? [];
                $this->driver = Kernel::factory($this->driverName, [$this->driverConfig, $this]);
            } else {
                throw new DriverNotFoundException("driver '{$this->index}' not found");
            }
        }
        return $this->driver;
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