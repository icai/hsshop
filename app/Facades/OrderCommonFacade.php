<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/7
 * Time: 9:27
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class OrderCommonFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'orderCommon';
    }
}