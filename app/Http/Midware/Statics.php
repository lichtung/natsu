<?php
/**
 * Created by Linzh.
 * Email: linzhv@qq.com
 * Date: 2019/1/7
 * Time: 12:37
 */

namespace App\Http\Midware;

use Natsu\Core\Midware;
use Natsu\Core\Request;

class Statics extends Midware
{
    public function handle(Request $request): bool
    {
        return true;
    }
}