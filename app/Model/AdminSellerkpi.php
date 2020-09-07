<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdminSellerkpi extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='admin_sellerkpi';
    protected $dates = [ 'updated_at', 'deleted_at'];

    public function SellerkpiRegister()
    {
        return $this->hasOne('App\Model\LiRegister','phone','mid');
    }

    public function SellerkpiSalesman()
    {
        return $this->hasOne('App\Model\LiSalesman','mobile','manage_mid');
    }

}
