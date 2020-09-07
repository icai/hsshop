<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/7
 * Time: 16:34
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class CurlBuilderFacade extends Facade
{
    public static  function getFacadeAccessor()
    {
        return 'curlBuilder';
    }
}