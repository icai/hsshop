<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    /**
     * 加入软删除，用于注销用户账号 (这里不加其他字段，主要是为了兼容之前的老数据查询未过滤注销的情况，所以用软删除方便实现)
     * @update 吴晓平 2019年12月27日 17:58:40
     */
    use  SoftDeletes;
	/**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    // public $timestamps=false;

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联到店铺表
     * @return [type] [description]
     */
    public function weixin()
    {
        return $this->hasMany('App\Model\Weixin','uid');
    }

    public function saleAchieve()
    {
        return $this->hasOne('App\Model\SaleAchieve','uid');
    }



}
