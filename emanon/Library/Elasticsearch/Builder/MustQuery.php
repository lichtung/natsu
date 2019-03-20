<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 14:14
 */

namespace Emanon\Library\Elasticsearch\Builder;

use Emanon\Library\Elasticsearch\Result\Search;

/**
 * Class Query 查询构造器
 * @package Emanon\Library\Elasticsearch
 */
class MustQuery extends Builder
{
    /**
     * 如果同时存在两个条件，如range何match的时候，需要用bool-query连接两个条件
     * 问题解答 @see https://discuss.elastic.co/t/elasticsearch-watcher-error-for-range-query/70347
     * 文档地址 @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
     * @return Search
     */
    public function fetch(): Search
    {
        if (count($this->query) > 1) {
            $must = [];
            # 多项
            foreach ($this->query as $key => $value) {
                $must[] = [
                    $key => $value,
                ];
            }
            $this->query = [
                'bool' => [
                    'must' => $must,
                ],
            ];
        }
        $params = [
            'index' => $this->index->getIndexName(),
            'type' => '_doc',
            'body' => [],
        ];
        if ($this->query) {
            $params['body']['query'] = $this->query;
        }
        $result = $this->index->getElasticsearch()->getClient()->search($params);
        dump($params);
        return new Search($result);
    }

}