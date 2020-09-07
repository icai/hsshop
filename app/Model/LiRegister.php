<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LiRegister extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='li_register';

    /**
     * 日期属性
     */
    public $timestamps = false;

}
