<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/11 0011
 * Time: 15:31
 */

namespace Canan\Library\Elasticsearch\Builder;

use Canan\Library\Elasticsearch\Index;

/**
 * Class Builder 查询构造器
 * @package Canan\Library\Elasticsearch\Builder
 */
abstract class Builder
{
    protected $index;

    protected $query = [];

    public function __construct(Index $index)
    {
        $this->index = $index;
    }

    /**
     * 单词匹配
     * 如果是短语则会拆分成单词，只要有单词匹配上返回该文档
     *  如 "name is"会被拆分成 name 和 is，如果内容中有name或者is可以匹配出该文档
     * @param array $filter
     * @return $this
     */
    public function match(array $filter)
    {
        $this->query['match'] = $filter;
        return $this;
    }

    /**
     * 短语匹配
     * 内容中必须行有该词语才能匹配得上
     * 此外可以加slop来允许移动单词的位置来进行匹配
     *  如
     *      "his name" 到 "his first name" 需要 slop = 1（name往后挪一格）
     *      "name is" 到 "is name" 需要 slop = 2 （交换位置需要两步操作）
     * @param array $filter
     * @return $this
     */
    public function matchPhrase(array $filter)
    {
        $this->query['match_phrase'] = $filter;
        return $this;
    }

    /**
     * 精确查找
     * @param string $name
     * @param string|int|float|bool $value
     * @return $this
     */
    public function term(string $name, $value)
    {
        $this->query['term'] = [$name => $value]; # [term] query does not support multiple fields
        return $this;
    }

    /**
     * 文档中只要有一项匹配即可
     * Filters documents that have fields that match any of the provided terms
     * 注意:
     *  'query' => [
     *      'terms' => [
     *          'age' => [12,14], # 值必须为数组
     *      ],
     *  ]
     * @param string $name 字段名称
     * @param array $values 字段值
     * @return $this
     */
    public function terms(string $name, array $values)
    {
        $this->query['terms'] = [$name => $values]; # [terms] query does not support multiple fields
        return $this;
    }

    /**
     * @param string $name
     * @param array $filter
     * @return $this
     */
    public function range(string $name, array $filter)
    {
        isset($this->query['range']) or $this->query['range'] = [];
        $this->query['range'][$name] = $filter;
        return $this;
    }
}