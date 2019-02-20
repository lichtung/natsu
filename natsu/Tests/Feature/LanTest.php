<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/14 0014
 * Time: 12:31
 */

namespace Emanon\Tests\Feature;


use Emanon\Tests\UnitTest;

class LanTest extends UnitTest
{

    public function testIsset()
    {
        $a = null;
        $b = null;
        $c = 1;
        $d = 1;
        $this->assertFalse(isset($a, $b)); # 均不满足isset
        $this->assertFalse(isset($a, $c)); # 一个不满足isset
        $this->assertTrue(isset($c, $d));  # 都满足isset  => true

        # empty() 无法测试多个变量
        
        $this->assertTrue(true);
    }

}