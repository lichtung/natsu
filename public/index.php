<?php
/**
 * Created by Linzh.
 * Email: 784855684@qq.com
 * Date: 2019-01-06
 * Time: 18:09
 */

namespace {

    use Emanon\Core\Route;
    use Emanon\Kernel;

    require __DIR__ . '/../vendor/autoload.php';
    $kernel = Kernel::getInstance();

    Route::get('/test', [\App\Http\Controller\Test::class, 'index']);

    $kernel->start();
}