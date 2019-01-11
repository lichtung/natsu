<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 11:41
 */

namespace Canan\Library\Elasticsearch;

/**
 * Class Document
 * @package Canan\Library\Elasticsearch
 */
class Document
{
    /** @var string 索引(Index)名称 */
    private $_index;
    /** @var string 文档类型(6.0以后将被启用，这里默认为"doc") */
    private $_type;
    /** @var string 文档ID */
    private $_id;
    /** @var float 匹配度 */
    private $_score;
    /** @var array 文档数据 */
    private $_source;

    public function __construct(array $data)
    {
        $this->_index = $data['_index'] ?? '';
        $this->_type = $data['_type'] ?? '';
        $this->_id = $data['_id'] ?? '';
        $this->_score = $data['_score'] ?? '';
        $this->_source = $data['_source'] ?? [];
    }

    /**
     * @return string
     */
    public function getIndex(): string
    {
        return $this->_index;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->_id;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->_score;
    }

    /**
     * @return array
     */
    public function getSource(): array
    {
        return $this->_source;
    }

    /**
     * @param string $key
     * @return mixed 如果不存在，返回null
     */
    public function get(string $key)
    {
        return $this->_source[$key] ?? null;
    }

}