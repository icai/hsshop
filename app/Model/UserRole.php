<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='user_role';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
     public $timestamps=false;


    /**
     * 获取关联到订单详情
     */
    public function userInfo() {
        return $this->hasMany('App\Model\UserInfo', 'uid')->select(['uid', 'nickname', 'headpic', 'mobile', 'offline_name', 'qq', 'balance']);
    }

}
