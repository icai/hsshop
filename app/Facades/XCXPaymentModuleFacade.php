<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/15
 * Time: 13:57
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class XCXPaymentModuleFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'xcxPaymentModule';
    }
}