<?php
namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class MicPageFacade extends Facade
{
    public static  function getFacadeAccessor()
    {
        return 'MicPage';
    }
}