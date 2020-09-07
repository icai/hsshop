<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/17
 * Time: 14:56
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class CommonModuleFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'commonModule';
    }
}