<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 14:54
 */

namespace Canan\Tests\Library;


use Canan\Library\Elasticsearch;
use Canan\Tests\UnitTest;

class ElasticsearchMappingTest extends UnitTest
{
    /**
     * @return void
     * @throws \Canan\Throwable\Library\ElasticsearchException
     * @throws \Elasticsearch\Common\Exceptions\BadRequest400Exception
     */
    public function testCreateMappingIndex()
    {
        $elasticsearch = Elasticsearch::factory([
            'hosts' => ['192.168.200.100:9200'],
        ]);
        $indexName = 'mapping_index_' . time();
        $result = $elasticsearch->createIndex($indexName, [
            'doc' => [ # Add a mapping type called doc.
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

        $this->assertTrue(true);
    }
}