<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LiFriend extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='li_friend';

    /**
     * 日期属性
     */
    public $timestamps = false;

}