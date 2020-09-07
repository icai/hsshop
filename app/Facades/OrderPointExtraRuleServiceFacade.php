<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/31
 * Time: 15:04
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class OrderPointExtraRuleServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'orderPointExtraRuleService';
    }
}