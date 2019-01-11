<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 11:31
 */

namespace Canan\Library\Elasticsearch;

/**
 * Class SearchResult
 *
 *
 * .array:4 [
 *  "took" => 1
 *  "timed_out" => false
 *  "_shards" => array:4 [
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
class SearchResult implements \Iterator
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

    public function __construct(array $result)
    {
        $this->took = $result['took'] ?? 0;
        $this->time_out = $result['time_out'] ?? false;
        $this->_shards = $result['_shards'] ?? [];
        $this->hits = $result['hits'] ?? [];
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


    public function current()
    {
        // TODO: Implement current() method.
    }

    public function next()
    {
        // TODO: Implement next() method.
    }

    public function key()
    {
        // TODO: Implement key() method.
    }

    public function valid()
    {
        // TODO: Implement valid() method.
    }

    public function rewind()
    {
        // TODO: Implement rewind() method.
    }

}