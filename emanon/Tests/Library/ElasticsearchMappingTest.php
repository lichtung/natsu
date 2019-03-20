<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 14:54
 */

namespace Emanon\Tests\Library;


use Emanon\Library\Elasticsearch;
use Emanon\Tests\UnitTest;

class ElasticsearchMappingTest extends UnitTest
{
    /**
     * @return void
     * @throws \Emanon\Throwable\Library\ElasticsearchException
     * @throws \Elasticsearch\Common\Exceptions\BadRequest400Exception
     */
    public function testCreateMappingIndex()
    {
        $elasticsearch = Elasticsearch::factory([
            'hosts' => ['192.168.200.100:9200'],
        ]);
        $indexName = 'mapping_index_' . time();
        $result = $elasticsearch->createIndex($indexName, [
            '_doc' => [ # Add a mapping type called doc.
                'properties' => [
                    'text' => ['type' => 'text'],
                    'keyword' => ['type' => 'keyword'],
                    'date' => ['type' => 'date'],
                    'long' => ['type' => 'long'],
                    'double' => ['type' => 'double'],
                    'boolean' => ['type' => 'boolean'],
                    'ip' => ['type' => 'ip'],
                ],
            ],
        ]);
        $this->assertTrue($result->acknowledged);
        $index = $elasticsearch->index($indexName);
        $result = $index->add([
            'text' => 'this is text' . microtime(true),
        ]);
        $this->assertTrue($result->getSuccessful() > 0);

        $result = $index->add([
            'text' => 'this is text' . microtime(true),
        ]);
        $this->assertTrue($result->getSuccessful() > 0);

        # 添加日期 @see https://www.elastic.co/guide/en/elasticsearch/reference/current/date.html
        $result = $index->add([
            'date' => '2015-01-01T12:10:30Z',
        ]);
        $this->assertTrue($result->getSuccessful() > 0);
        $result = $index->add([
            'date' => microtime(true) * 1000, # 毫秒时间戳
        ]);

        dump($result);

        sleep(1);

        $result = $index->query()->fetch();
        foreach ($result as $item) {
            dump('--', $item);
        }
        $this->assertTrue(count($result) === 4);# 添加了4次

        $this->assertTrue(true);
    }
}