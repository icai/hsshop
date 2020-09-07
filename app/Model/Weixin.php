<?php

namespace App\Model;

use App\Model\Traits\WeixinLoginLogTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 店铺/公众号
 * 
 * @author 黄东 406764368@qq.com
 * @version  2017年2月28日 15:30:25
 */
class Weixin extends Model
{
    use WeixinLoginLogTrait;

    /**
     * 软删除
     */
    use SoftDeletes;
    
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 所有关联关系
     * 
     * @var array
     */
    public $withAll = ['weixinConfigMaster', 'weixinPayment', 'weixinConfigSub'];

    /**
     * 关联到配置主表(店铺配置)
     * 
     * @return Builder
     */
    public function weixinConfigMaster()
    {
        return $this->hasOne('App\Model\WeixinConfigMaster', 'wid')->select(Schema::getColumnListing('weixin_config_master'));
    }

    /**
     * 关联到配置从表(公众号信息)
     * 
     * @return Builder
     */
    public function weixinConfigSub()
    {
        return $this->hasOne('App\Model\WeixinConfigSub', 'wid')->select(Schema::getColumnListing('weixin_config_sub'));
    }

    /**
     * 关联到配置从表(小程序信息)
     * @return [type] [description]
     */
    public function wxxcxConfig()
    {
        return $this->hasOne('App\Model\WXXCXConfig','wid');
    }

    /**
     * 关联到支付配置表
     * 
     * @return Builder
     */
    public function weixinPayment()
    {
        return $this->hasMany('App\Model\WeixinPayment', 'wid')->select(Schema::getColumnListing('weixin_payment'));
    }

	/**
     * 关联到商品
     * @author 吴晓平 <2018年09月18日>
     * @return [type] [description]
     */
    public function product()
    {
        return $this->hasMany('App\Model\Product','wid');
    }

    /**
     * 关联到用户表
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User','uid');
    }

    /**
     * 微商城前台面获取店铺信息
     *
     * @param   integer $wid [店铺id]
     * @return  array
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年4月17日 18:03:30
     */
    public function getStageShop($wid)
    {
        $uid = session('weixin_uid');

        if ( empty($uid) ) {
            // 从数据库读取uid
            $uid = $this->where('id', $wid)->value('uid');
            $request = app('request');
            $request->session()->put('weixin_uid', $uid);
            $request->session()->save();
        }

        return D('Weixin', 'uid', $uid)->getInfo($wid);
    }

    /**
     * 关联用户角色
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author: 梅杰 2018年9月26日
     */
    public function ShopRole()
    {
        return $this->hasOne('App\Model\WeixinRole','wid');
    }
}
