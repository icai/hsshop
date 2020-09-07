<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 客户/会员
 */
class MarketingActivityRuleCoupon extends Model
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
    protected $table = 'marketing_activity_rule_coupon';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联到微信卡券表
     */
    public function weixinCoupon() {
        return $this->hasOne('App\Model\WeixinCoupon', 'coupon_id')->select(Schema::getColumnListing('weixin_coupon'));
    }
}