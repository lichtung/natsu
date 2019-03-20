<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 15:41
 */

namespace Emanon\Library\Elasticsearch\Result;

/**
 * Class Index 编入索引结果
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/docs-index_.html
 *
 *
 * "_index" => "mapping_index_1547221476"
 * "_type" => "doc"
 * "_id" => "nQSWPWgBFpfj1c_9Jas9"
 * "_version" => 1
 * "result" => "created"
 * "_shards" => array:3 [
 *      "total" => 2
 *      "successful" => 1
 *      "failed" => 0
 * ]
 * "_seq_no" => 0
 * "_primary_term" => 1
 *
 * @package Emanon\Library\Elasticsearch\Result
 */
class DocumentIndex extends Result
{
    /** @var string */
    private $_index;
    /** @var string */
    private $_type;
    /** @var string */
    private $_id;
    /** @var string */
    private $_version;
    /** @var array */
    private $_shards;
    /** @var string */
    private $_seq_no;
    /** @var string */
    private $_primary_term;

    public function __construct(array $result)
    {
        parent::__construct($result);
        $this->_index = $result['_index'] ?? '';
        $this->_type = $result['_type'] ?? '';
        $this->_id = $result['_id'] ?? '';
        $this->_version = $result['_version'] ?? 0;
        $this->_shards = $result['_shards'] ?? [];
        $this->_seq_no = $result['_seq_no'] ?? 0;
        $this->_primary_term = $result['_primary_term'] ?? 0;
    }

    /**
     * 应该要保存文档的分片数
     * Indicates to how many shard copies (primary and replica shards) the index operation should be executed on.
     * @return int
     */
    public function getTotal()
    {
        return $this->_shards['total'] ?? 0;
    }

    /**
     * 成功保存文档的分片数
     * Indicates the number of shard copies the index operation succeeded on.
     * @return int
     */
    public function getSuccessful()
    {
        return $this->_shards['successful'] ?? 0;
    }

    /**
     * 发送错误的数目
     * An array that contains replication related errors in the case an index operation failed on a replica shard.
     * @return int
     */
    public function getFailed()
    {
        return $this->_shards['failed'] ?? 0;
    }

}