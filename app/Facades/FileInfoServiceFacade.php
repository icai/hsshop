<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/5/25
 * Time: 17:19
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class FileInfoServiceFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'fileInfoService';
    }
}