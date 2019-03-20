<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/14 0014
 * Time: 10:27
 */

namespace Emanon\Tests\Library;


use Emanon\Library\Elasticsearch;
use Emanon\Tests\UnitTest;

class ElasticsearchEcommerceSampleTest extends UnitTest
{

    public function testMust()
    {
        # 插入数据
        $indexName = 'kibana_sample_data_ecommerce';

        $elasticsearch = Elasticsearch::factory([
            'hosts' => ['192.168.200.100:9200'],
        ]);
        $index = $elasticsearch->index($indexName);
        # 全部数据量 4675
        $result = $index->query()->fetch();
        $this->assertTrue(count($result) === 4675);
        $count = 0;
        foreach ($result as $document) {
            $count += $document->getScore(); # 都是1.0
        }
        $this->assertTrue(10.0 === $count);

        $begin = strtotime('2019-01-01 00:00:00');
        $end = strtotime('2019-01-11 00:00:00');
        $result = $index->query()->match(['customer_last_name' => 'Perkins'])->range('order_date', [
            'gte' => $begin * 1000,
            'lte' => $end * 1000,
        ])->fetch();
        $this->assertTrue(count($result) === 13);
        foreach ($result as $document) {
            $this->assertTrue('Perkins' === $document->get('customer_last_name'));
            $order_timestamp = strtotime($document->get('order_date'));
            $this->assertTrue($order_timestamp >= $begin);
            $this->assertTrue($order_timestamp <= $end);
        }

        $result = $index->query()->matchPhrase(['products.manufacturer' => 'Elitelligence'])->range('order_date', [
            'gte' => $begin * 1000,
            'lte' => $end * 1000,
        ])->fetch();
        dump($result->getTotal());
        $this->assertTrue(34 === $result->getTotal());

        $this->assertTrue(true);
    }
}