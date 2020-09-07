<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/10
 * Time: 19:25
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class WXXCXCacheFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'wXXCXCacheRedis';
    }
}