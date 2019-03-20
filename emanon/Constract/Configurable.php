<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/9
 * Time: 15:05
 */

namespace Emanon\Constract;


/**
 * Interface Configurable
 * @property array $config
 * @package Emanon\Constract
 */
interface Configurable
{
    /**
     * 配置首选项
     * @return array
     */
    public function preference(): array;

    /**
     * 应用初始化配置
     * @param array $config 配置
     * @return void
     */
    public function apply(array $config): void;
}