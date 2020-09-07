<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 店铺微信支付模型
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月10日 13:30:55
 */
class WeixinWechatPayment extends Model {
    /**
     * 软删除
     */
    use SoftDeletes;
    
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'weixin_wechat_payment';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 获取订单详情对应的订单
     * @return [type] [description]
     */
    public function weixin() {
        return $this->belongsTo('App\Model\Weixin', 'wid')->select(Schema::getColumnListing('weixin'));
    }
}
