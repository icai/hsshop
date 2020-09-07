<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 订单模型
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年1月12日 21:35:02
 */
class Order extends Model
{
    /**
     * 软删除
     */
    use SoftDeletes;

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order';

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
    public $withAll = ['orderDetail', 'orderLog','weixin'];

    /**
     * 关联到订单详情
     *
     * @author 黄东 406764368@qq.com
     * @version  2017年1月12日 21:45:02
     *
     * @return Builder
     */
    public function orderDetail() {
        return $this->hasMany('App\Model\OrderDetail', 'oid')->select(Schema::getColumnListing('order_detail'));
    }

    /**
     * 关联到订单操作
     *
     * @author 黄东 406764368@qq.com
     * @version  2017年1月12日 21:55:02
     *
     * @return Builder
     */
    public function orderLog() {
        return $this->hasMany('App\Model\OrderLog', 'oid')->select(Schema::getColumnListing('order_logs'));
    }


	public function weixin() {
		return $this->belongsTo('App\Model\Weixin', 'wid')->select(Schema::getColumnListing('weixin'));
	}

    /**
     * 关联用户
     * @author 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月10日 21:11:55
     */
	public function member()
    {
        return $this->belongsTo('App\Model\Member', 'mid');
    }

    /**
     * 订单状态map
     * @var array
     */
    const ORDER_STATUS_MAP = [
        '0' => '待付款',
        '1' => '待发货',
        '2' => '已发货',
        '3' => '已完成',
        '4' => '已关闭',
        '5' => '退款中',
        '7' => '待抽奖',
    ];

    /**
     * 团购订单状态map
     * @var array
     */
    const GROUP_ORDER_STATUS_MAP = [
        '1' => '待成团',
        '2' => '已成团',
        '3' => '未成团',
    ];

    /**
     * 订单成交方式
     * @var array
     */
    const ORDER_PAY_WAY_MAP = [
        '1' => '微信支付',
//        '2' => '支付宝支付',
        '3' => '储值余额支付',
        '4' => '货到付款/到店付款',
//        '5' => '找人代付',
        '6' => '领取赠品',
        '7' => '优惠兑换',
//        '8' => '银行卡支付',
        '9' => '会员卡支付',
        '10' => '小程序支付',
    ];
}
