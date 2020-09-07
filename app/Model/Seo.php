<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seo extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='seo';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
