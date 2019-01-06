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
 * @method Dispatcher factory(Route $route) static
 * @package Natsu\Core
 */
class Dispatcher
{
    use Factory;

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
     * @return Response
     */
    public function dispatch(): Response
    {


    }

}