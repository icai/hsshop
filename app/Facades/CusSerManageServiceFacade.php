<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/11
 * Time: 12:13
 */

namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class CusSerManageServiceFacade extends Facade
{
    public static  function getFacadeAccessor()
    {
        return 'CusSerManagerService';
    }
}