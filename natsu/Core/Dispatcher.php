<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 21:57
 */

namespace Natsu\Core;

/**
 * Class Dispatcher
 * @package Natsu\Core
 */
class Dispatcher
{
    use Singleton;

    /** @var Route */
    private $route;

    /**
     * Dispatcher constructor.
     * @param Route $route
     */
    private function __construct(Route $route)
    {
        $this->route = $route;
    }

    /**
     * @param string $controller
     * @param string $method
     * @return Response
     */
    public function dispatch(string $controller, string $method): Response
    {


    }

}