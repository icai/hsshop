<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/5/2
 * Time: 14:39
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class MicroPageTypeRelationServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'microPageTypeRelationService';
    }
}