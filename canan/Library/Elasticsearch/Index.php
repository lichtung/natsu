<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/10 0010
 * Time: 16:18
 */

namespace Canan\Library\Elasticsearch;


use Canan\Library\Elasticsearch;

class Index
{

    /** @var Elasticsearch */
    private $context;
    /** @var string */
    private $indexName;

    public function __construct(string $indexName, Elasticsearch $context)
    {
        $this->indexName = $indexName;
        $this->context = $context;
    }

    /**
     * array:8 [
     * "_index" => "basic_index_1547138409"
     * "_type" => "doc"
     * "_id" => "RNGiOGgBuBaDGjJspyNP"
     * "_version" => 1
     * "result" => "created"
     * "_shards" => array:3 [
     * "total" => 2
     * "successful" => 1
     * "failed" => 0
     * ]
     * "_seq_no" => 0
     * "_primary_term" => 1
     * ]
     * @param array $data
     * @return array
     */
    public function add(array $data)
    {
        $data['lts'] = time();
        $result = $this->context->getClient()->index([
            'index' => $this->indexName,
            'type' => 'doc',
            'body' => $data,
        ]);
        return $result;
    }

    /**
     *
     * @param array $filter
     * @return SearchResult
     */
    public function get(array $filter): SearchResult
    {
        $result = $this->context->getClient()->search([
            'index' => $this->indexName,
            'type' => 'doc',
            'body' => $filter,
        ]);
        return new SearchResult($result);
    }

    /**
     * 单词匹配
     * 如果是短语则会拆分成单词，只要有单词匹配上返回该文档
     *  如 "name is"会被拆分成 name 和 is，如果内容中有name或者is可以匹配出该文档
     * @param array $filter
     * @return array
     */
    public function match(array $filter)
    {
        return $this->get(['query' => [
            'match' => $filter,
        ]]);
    }

    /**
     * 短语匹配
     * 内容中必须行有该词语才能匹配得上
     * 此外可以加slop来允许移动单词的位置来进行匹配
     *  如
     *      "his name" 到 "his first name" 需要 slop = 1（name往后挪一格）
     *      "name is" 到 "is name" 需要 slop = 2 （交换位置需要两步操作）
     * @param array $filter
     * @return array
     */
    public function matchPhrase(array $filter)
    {
        return $this->get([
            'query' => [
                'match_phrase' => $filter,
            ],
        ]);
    }

    /**
     * 精确查找
     * @param array $filter
     * @return array
     */
    public function term(array $filter)
    {
        return $this->get(['query' => [
            'term' => $filter,
        ]]);
    }

}