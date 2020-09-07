<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/31
 * Time: 18:47
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class SharePointRuleServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'sharePointRuleService';
    }
}