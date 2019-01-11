<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/8
 * Time: 9:41
 */

namespace Canan\Library;


use Canan\Constract\Configurable;
use Canan\Core\Factory;
use Canan\Library\Elasticsearch\Index;
use Canan\Throwable\Library\Elasticsearch\IndexNotFoundException;
use Canan\Throwable\Library\Elasticsearch\ResourceAlreadyExistsException;
use Canan\Throwable\Library\ElasticsearchException;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Exception;

/**
 * Class Elasticsearch
 *
 * match和match_phrase的区别 @see http://leotse90.com/2015/11/10/ElasticSearch-match-VS-match-phrase/
 *
 * @package Canan\Library
 */
class Elasticsearch implements Configurable
{
    use Factory;

    public function preference(): array
    {
        return [
            'retries' => 2, # 在一个集群中，如果操作抛出如下异常：connection refusal, connection timeout, DNS lookup timeout 等等（不包括4xx和5xx），客户端便会重连。客户端默认重连 n （n=节点数）次
            'log_path' => CNN_RUNTIME_PATH . 'es/elasticsearch.log',
            'log_severity' => Logger::INFO,
            'hosts' => ['127.0.0.1:9200'], # 为空时默认连接 localhost:9200
        ];
    }

    /** @var array */
    protected $config;

    public function apply(array $config): void
    {
        $this->config = $config;
        /** @var LoggerInterface $logger */
        $logger = ClientBuilder::defaultLogger($config['log_path'], $config['log_severity']);
        $this->client = ClientBuilder::create()
            ->setRetries($config['retries'])# OperationTimeoutException
            ->setHosts($config['hosts'])
            ->setLogger($logger)
            ->build();
    }

    /** @var array */
    private $hosts;
    /** @var Client */
    private $client;

    /**
     * 获取es客户端
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->client, $name], $arguments);
    }

    /**
     * @param string $indexName
     * @param array $mappings
     * @return array
     * @throws ElasticsearchException
     * @throws BadRequest400Exception
     */
    public function createIndex(string $indexName, array $mappings = [])
    {
        try {
            $params = ['index' => $indexName];
            if ($mappings) {
                isset($params['body']) or $params['body'] = [];
                $params['body']['mappings'] = $mappings;
            }
            return $this->client->indices()->create($params);
        } catch (BadRequest400Exception $exception) {
            $this->checkException($exception);
            throw $exception;
        }
    }


    /**
     * 获取index信息
     * array:1 [
     *  "basic_index_1547051249" => array:3 [
     *      "aliases" => []
     *      "mappings" => []
     *      "settings" => array:1 [
     *      "index" => array:6 [
     *      "creation_date" => "1547051249714"
     *      "number_of_shards" => "5"
     *      "number_of_replicas" => "1"
     *      "uuid" => "a5H-bkfPQZeSHOdYKg49zA"
     *      "version" => array:1 [
     *          "created" => "6050499"
     *      ]
     *      "provided_name" => "basic_index_1547051249"
     *
     * @param string $indexName
     * @return bool
     * @throws ElasticsearchException
     * @throws Missing404Exception
     */
    public function getIndex(string $indexName)
    {
        try {
            return $this->client->indices()->get([
                'index' => $indexName,
            ]);
        } catch (Missing404Exception $exception) {
            $this->checkException($exception);
            throw $exception;
        }
    }

    public function index(string $indexName): Index
    {
        static $_indices = [];
        if (!isset($_indices[$indexName])) {
            $_indices[$indexName] = new Index($indexName, $this);
        }
        return $_indices[$indexName];
    }

    /**
     * @param Exception $exception
     * @return void
     * @throws ElasticsearchException
     */
    protected function checkException(Exception $exception)
    {
        $message = $exception->getMessage();
        $response = json_decode($message, true);
        $errorType = $response['error']['type'] ?? '';
        switch ($errorType) {
            case 'resource_already_exists_exception':
                throw new ResourceAlreadyExistsException($message);
            case 'index_not_found_exception':
                throw new IndexNotFoundException($message);
        }
    }

}