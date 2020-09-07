<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CapitalWithdraw extends Model
{
    protected $table='capital_withdraw';

    public $timestamps=false;

    public function bankInfo() {
        return $this->hasMany('App\Model\bankInfo', 'id')->select(['id', 'account_name', 'account_no', 'bank_name']);
    }
}