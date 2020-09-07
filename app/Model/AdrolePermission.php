<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdrolePermission extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='adrole_permission';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    public function permission()
    {
        return $this->hasOne('App\Model\Permission', 'id','permission_id')->select();
    }

}
