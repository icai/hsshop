<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/26
 * Time: 9:18
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class MicroPageTypeServiceFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() {
        return 'microPageTypeService';
    }
}