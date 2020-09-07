<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MemberAddressServiceFacade extends Facade {

    protected static function getFacadeAccessor() {
        return 'MemberAddressService';
    }

}