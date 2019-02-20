<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 15:27
 */

namespace Emanon\Library\Elasticsearch\Result;

/**
 * Class Result 操作结果
 * @package Emanon\Library\Elasticsearch\Result
 */
abstract class Result
{

    protected $result;

    public function __construct(array $result)
    {
        $this->result = $result;
    }

    public function __get($name)
    {
        return $this->result[$name];
    }

}