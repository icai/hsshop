<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/26
 * Time: 9:51
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class PointRecordServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'pointRecordService';
    }
}