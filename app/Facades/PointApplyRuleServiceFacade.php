<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/1
 * Time: 13:30
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class PointApplyRuleServiceFacade  extends Facade
{
    protected static function getFacadeAccessor() {
        return 'pointApplyRuleService';
    }
}