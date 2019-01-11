<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/10 0010
 * Time: 16:18
 */

namespace Canan\Library\Elasticsearch;

use Canan\Library\Elasticsearch;
use Canan\Library\Elasticsearch\Result\DocumentIndex;

class Index
{

    /** @var Elasticsearch */
    private $elasticsearch;
    /** @var string */
    private $indexName;

    public function __construct(string $indexName, Elasticsearch $elasticsearch)
    {
        $this->indexName = $indexName;
        $this->elasticsearch = $elasticsearch;
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
     * @return DocumentIndex
     */
    public function add(array $data)
    {
        $data['lts'] = time();
        $result = $this->elasticsearch->getClient()->index([
            'index' => $this->indexName,
            'type' => 'doc',
            'body' => $data,
        ]);
        return new DocumentIndex($result);
    }

    /**
     * @return Elasticsearch
     */
    public function getElasticsearch(): Elasticsearch
    {
        return $this->elasticsearch;
    }

    /**
     * @return string
     */
    public function getIndexName(): string
    {
        return $this->indexName;
    }

    public function query()
    {
        return new Elasticsearch\Builder\MustQuery($this);
    }

}