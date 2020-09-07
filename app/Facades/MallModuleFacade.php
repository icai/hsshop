<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/25
 * Time: 19:31
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class MallModuleFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'mallModule';
    }
}