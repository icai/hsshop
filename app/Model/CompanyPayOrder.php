<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CompanyPayOrder extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='company_pay_order';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

}
