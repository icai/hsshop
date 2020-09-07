<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/25
 * Time: 16:57
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class ProductGroupServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'productGroupService';
    }
}