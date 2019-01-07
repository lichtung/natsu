<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/7
 * Time: 15:12
 */

namespace {

    use App\Console\Command\StartLdap;
    use Symfony\Component\Console\Application;

    require __DIR__ . '/vendor/autoload.php';

    $application = new Application();
    $application->add(new StartLdap());
    $application->run();
}