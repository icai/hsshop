<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/6
 * Time: 14:44
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class SignServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'signService';
    }
}