<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;

class MemberHomeModule extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='member_home_module';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

    

}
