<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 11:31
 */

namespace Canan\Library\Elasticsearch\Result;

use Canan\Library\Elasticsearch\Document;

/**
 * Class Search 查询结果
 *
 * .array:4 [
 *  "took" => 1
 *  "timed_out" => false
 *  "_shards" => array:4 [
 *      # The _shards element tells us the total number of shards that were involved in the query and, of them, how many were successful and how many failed.
 *      # 有多少个节点参与此查询 以及他们中有多少个成功，多少个失败
 *      # We wouldn’t normally expect shards to fail, but it can happen. If we were to suffer a major disaster in which we lost both the primary and the
 *      # replica copy of the same shard, there would be no copies of that shard available to respond to search requests.
 *      # In this case, Elasticsearch would report the shard as failed, but continue to return results from the remaining shards.
 *      # 我们通常不希望分片查询是吧，但是它们的确会发生，如果主分片和拷贝分片的数据同时丢失了，es会报告这个错误，但是其他的一些结构仍然会返回
 *      "total" => 5
 *      "successful" => 5
 *      "skipped" => 0
 *      "failed" => 0
 *  ]
 *  "hits" => array:3 [
 *      "total" => 1
 *      "max_score" => 0.2876821 # 最高的匹配度得分，hits的文档会按照分数从高到低排列
 *      "hits" => array:1 [
 *          0 => array:5 [
 *              "_index" => "basic_index_1547141293"
 *              "_type" => "doc"
 *              "_id" => "ZgTOOGgBFpfj1c_9pqrR"
 *              "_score" => 0.2876821 # 匹配度得分
 *              "_source" => array:2 [ # 文档的数据部分
 *                  "name" => "nara"
 *                  "lts" => 1547141293
 *              ]
 *          ]
 *      ]
 *  ]
 * ]
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/guide/current/empty-search.html
 * @package Canan\Library\Elasticsearch
 */
class Search extends Result implements \Iterator, \Countable
{
    /**
     * took 表示查询花费的毫秒数（1 second  = 1,000 millisecond = 1,000,000 microsecond = 1,000,000,000 nanosecond ）
     * @var int how many milliseconds the entire search request took to execute.
     */
    private $took;
    /** @var bool */
    private $time_out;
    /** @var array */
    private $_shards;
    /**
     * hits包含了匹配总数和前10个最匹配的文档
     * @var array The most important section of the response is hits, which contains the total number of documents that matched our query, and a hits array containing the first 10 of those matching documents—the results.
     */
    private $hits;

    private $list = [];

    private $position;

    public function __construct(array $result)
    {
        $this->took = $result['took'] ?? 0;
        $this->time_out = $result['time_out'] ?? false;
        $this->_shards = $result['_shards'] ?? [];
        $this->hits = $result['hits'] ?? ['hits' => []];
        # 遍历对象
        $this->list = &$this->hits['hits']; # 去掉 & 会再复制一份数组，占用更多的内存
        $this->position = 0;
    }

    public function count()
    {
        return count($this->list);
    }

    public function getTotal(): int
    {
        return $this->hits['total'] ?? 0;
    }

    public function getMaxScore(): float
    {
        return $this->hits['max_score'] ?? 0;
    }

    /**
     * @return int
     */
    public function getTook(): int
    {
        return $this->took;
    }

    /**
     * @return bool
     */
    public function isTimeOut(): bool
    {
        return $this->time_out;
    }

    /**
     * @return array
     */
    public function getShards(): array
    {
        return $this->_shards;
    }

    /**
     * @return array
     */
    public function getHits(): array
    {
        return $this->hits;
    }

    /**
     * @return Document
     */
    public function current()
    {
        return new Document($this->list[$this->position]);
    }

    public function next()
    {
        ++$this->position;
    }

    public function key()
    {
        return $this->position;
    }

    public function valid()
    {
        return isset($this->list[$this->position]);
    }

    /**
     * Rewind the Iterator to the first element
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

}