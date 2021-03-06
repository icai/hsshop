<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ServicePartner extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='service_partner';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps=false;



}