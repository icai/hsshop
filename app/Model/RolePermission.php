<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-6
 * Time: 下午9:50
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends  Model
{
    /**
     * @desc  表名
     */
    protected $table = 'role_permission';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function permission()
    {
        return $this->hasOne('App\Model\Permission', 'id','permission_id')->select();
    }

}