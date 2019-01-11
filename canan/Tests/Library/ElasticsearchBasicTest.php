<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/9
 * Time: 14:52
 */

namespace Canan\Tests\Library;


use Canan\Library\Elasticsearch;
use Canan\Tests\UnitTest;
use Canan\Throwable\Library\Elasticsearch\IndexNotFoundException;
use Canan\Throwable\Library\Elasticsearch\ResourceAlreadyExistsException;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;

class ElasticsearchBasicTest extends UnitTest
{
    /**
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-create-index.html
     * @return array
     * @throws BadRequest400Exception
     * @throws \Canan\Throwable\Library\ElasticsearchException
     * @throws \Elasticsearch\Common\Exceptions\Missing404Exception
     */
    public function testCreateIndex()
    {
        $elasticsearch = Elasticsearch::factory([
            'hosts' => ['192.168.200.100:9200'],
        ]);
        $result = [];
        $indexName = 'basic_index_' . time();
        $result['create_index'] = $elasticsearch->createIndex($indexName);
        #  indicates whether the index was successfully created in the cluster
        # 集群中索引是否已经在创建
//        $this->assertTrue($result['create_index']['acknowledged'] === true);
        #  indicates whether the requisite number of shard copies were started for each shard in the index before timing out.
        # timeout前是否有符合需要数量的分片启动了复制
//        $this->assertTrue($result['create_index']['shards_acknowledged'] === true);

        try {
            $elasticsearch->createIndex($indexName);
            $this->assertTrue(false);
        } catch (ResourceAlreadyExistsException $exception) {
            $result['create_exist_index'] = $exception->getMessage();
            $response = json_decode($result['create_exist_index'], true);
            $this->assertTrue($response['error']['type'] === 'resource_already_exists_exception');
        }

        $index = $elasticsearch->getIndex($indexName);
        dump($index);

        try {
            $elasticsearch->getIndex($indexName . '_not_exist');
            $this->assertTrue(false);
        } catch (IndexNotFoundException $exception) {
            $this->assertTrue(true);
        }

        return [$elasticsearch, $indexName];
    }

    /**
     * @depends testCreateIndex
     * @param array $params
     * @return Elasticsearch\Index
     */
    public function testCreateDocument(array $params)
    {
        /** @var Elasticsearch $elasticsearch */
        $elasticsearch = $params[0];
        /** @var string $indexName */
        $indexName = $params[1];
        $index = $elasticsearch->index($indexName);
        $index->add(['name' => 'nara', 'age' => 11, 'sign' => 'my name is nara']);
        $index->add(['name' => 'piece', 'age' => 12, 'sign' => 'his name is sarah']);
        $index->add(['name' => 'justin', 'age' => '13', 'sign' => 'ace is died']);
        $index->add(['name' => 'bie', 'age' => '14', 'sign' => 'masquerade']);
        # !important 需要建立文档需要一定的时间，如果立即查询可能会找不到
        sleep(1);
        $this->assertTrue(true);
        return $index;
    }

    /**
     * @depends testCreateDocument
     * @param Elasticsearch\Index $index
     * @return Elasticsearch\Index
     */
    public function testMatch(Elasticsearch\Index $index)
    {
        # 测试 match(只要有分词能匹配就返回)
        $result = $index->query()->match(['sign' => 'name is sara',])->fetch();
        $this->assertTrue(3 === $result->getTotal()); # 1~3 包含 is

        $result = $index->query()->match(['sign' => 'first name',])->fetch();
        $this->assertTrue(2 === $result->getTotal()); # 1~2包含 name

        $result = $index->query()->match(['sign' => 'ace name'])->fetch();
        $this->assertTrue(3 === $result->getTotal()); # 1~3
        return $index;
    }

    /**
     * @depends testMatch
     * @param Elasticsearch\Index $index
     * @return Elasticsearch\Index
     */
    public function testMatchPhrase(Elasticsearch\Index $index)
    {
        # 测试 match_phrase ()
        $result = $index->query()->matchPhrase(['sign' => ' name is'])->fetch();
        $this->assertTrue(2 === $result->getTotal()); # 不用交互位置即可匹配到1和2

        $result = $index->query()->matchPhrase(['sign' => 'is name'])->fetch();
        $this->assertTrue(0 === $result->getTotal());
        $result = $index->query()->matchPhrase(['sign' => ['query' => 'is name', 'slop' => 2]])->fetch(); # 这里name is交换位置占据了2个slop @see https://www.elastic.co/guide/en/elasticsearch/guide/current/slop.html
        $this->assertTrue(2 === $result->getTotal());

        $result = $index->query()->matchPhrase(['sign' => ['query' => 'my is', 'slop' => 1]])->fetch(); # is 往后面挪一格子
        $this->assertTrue(1 === $result->getTotal());

        $index->add(['name' => 'nara', 'age' => 11, 'sign' => 'my first name is nara']);
        sleep(1);
        $result = $index->query()->matchPhrase(['sign' => ['query' => 'my is', 'slop' => 1]])->fetch(); # my name is nara
        $this->assertTrue(1 === $result->getTotal());
        $result = $index->query()->matchPhrase(['sign' => ['query' => 'my is', 'slop' => 2]])->fetch(); # my name is nara 和 my first name is nara(is 挪两格)
        $this->assertTrue(2 === $result->getTotal());
        return $index;
    }

    /**
     * @depends testMatchPhrase
     * @param Elasticsearch\Index $index
     * @return Elasticsearch\Index
     */
    public function testTermAndTerms(Elasticsearch\Index $index)
    {

        $result = $index->query()->term('age', 14)->fetch();
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result->current()->get('name') === 'bie');

        $result = $index->query()->terms('age', [12, 14])->fetch();
        foreach ($result as $item) {
            $this->assertTrue($item->getScore() === 1.0);
        }

        $result = $index->query()->range('age', [
            'gte' => 12,
            'lte' => 13,
        ])->term('name', 'piece')->fetch();
        $this->assertTrue(count($result) === 1);
        $this->assertTrue($result->current()->get('name') === 'piece');


        dump($result);
        $this->assertTrue(true);
        return $index;
    }

}