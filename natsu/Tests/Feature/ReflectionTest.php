<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 20:04
 */

namespace Tests\Feature;

use Natsu\Core\Factory;
use Natsu\Core\Singleton;
use Natsu\Tests\UnitTest;


class ReflectionTest extends UnitTest
{
    /**
     * 如果反射的类的构造方法是私有或保护的，创建反射类的时候不会抛出异常
     * @return void
     */
    public function testPrivateConstruct()
    {
        try {
            $reflectionClass = new \ReflectionClass(A::class);
            $this->assertTrue(true);
            try {
                $reflectionClass->newInstance();
                $this->assertTrue(false);
            } catch (\ReflectionException $exception) {
                $this->assertTrue(true); # $exception->getMessage() ===  Access to non-public constructor of class Tests\Feature\A
            }
        } catch (\ReflectionException $exception) {
            echo get_class($exception) . $exception->getMessage() . PHP_EOL;
            $this->assertTrue(false);
        }
    }

    /**
     * @return void
     */
    public function testSingleton()
    {
        B::getInstance();
        $this->assertTrue(true);
    }

    public function testFactory()
    {
        $description = 'I\'m Conan, now 12 years old.';
        $this->assertTrue((string)Person::factory(...['Conan', 12]) === $description); # 数组分拆
        $this->assertTrue((string)Person::factory('Conan', 12) === $description); # === 不会强转
    }

}

class A
{
    private function __construct()
    {
    }
}

class B
{

    use Singleton;

    private function __construct()
    {
    }
}

/**
 * Class Person
 * @method Person factory(string $name, int $age) static
 * @package Tests\Feature
 */
class Person
{
    use Factory;

    protected $name;
    protected $age;

    protected function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function __toString(): string
    {
        return "I'm {$this->name}, now {$this->age} years old.";
    }
}