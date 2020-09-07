<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class WeixinCouponLog extends Model
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
    protected $table = 'weixin_coupon_log';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联主表
     *
     * @return Bulider
     */
    public function marketingActivityRuleCoupon() {
        return $this->belongsTo('App\Model\MarketingActivityRuleCoupon', 'coupon_id')->select(Schema::getColumnListing('marketing_activity_rule_coupon'));
    }
}






?>