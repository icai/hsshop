<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/27
 * Time: 16:04
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class SignRecordServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'signRecordService';
    }
}