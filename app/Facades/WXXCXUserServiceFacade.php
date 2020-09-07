<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/10
 * Time: 14:12
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class WXXCXUserServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'wXXCXUserService';
    }
}