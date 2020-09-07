<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/31
 * Time: 15:03
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class OrderPointRuleServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'orderPointRuleService';
    }
}