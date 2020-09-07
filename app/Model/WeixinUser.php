<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/3/8
 * Time: 10:22
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WeixinUser extends Model
{

    use  SoftDeletes;
    /**
     * @desc  表名
     */
    protected $table = 'weixin_user';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    function weixin()
    {
        return $this->hasOne('App\Model\Weixin', 'id','wid')->select();
    }
    function user()
    {
        return $this->hasOne('App\Model\User', 'id','uid')->select();
    }
    function oper()
    {
       return $this->hasOne('App\Model\User', 'id','oper_id')->select();
    }
    function  role()
    {
        return $this->hasOne('App\Model\Role', 'id','role_id')->select();
    }
    function  member()
    {
        return $this->hasOne('App\Model\Member', 'id','hexiao_mid')->select();
    }

    /**
     * 关联所属店铺
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月25日 15:01:09
     */
    public function shop()
    {
        return $this->belongsTo(Weixin::class, 'wid');
    }
}