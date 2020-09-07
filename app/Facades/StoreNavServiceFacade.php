<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/25
 * Time: 16:25
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class StoreNavServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'storeNavService';
    }
}