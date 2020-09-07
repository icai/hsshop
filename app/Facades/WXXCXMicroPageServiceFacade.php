<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/9/14
 * Time: 10:25
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class WXXCXMicroPageServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'xcxMicroPage';
    }
}