<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 店铺配置主表
 *
 * @author 黄东 406764368@qq.com
 * @version 2017年2月28日 14:06:08
 */
class WeixinConfigSub extends Model
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
    protected $table = 'weixin_config_sub';

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
    public function weixin() {
        return $this->belongsTo('App\Model\Weixin', 'wid')->select(Schema::getColumnListing('weixin'));
    }

    //关联之前的微信支付表，为之后的数据转移做准备
    public function payment()
    {
        return $this->hasOne('App\Model\WeixinPayment', 'wid','wid');
    }

    /**
     * @description 定义店铺关联关系
     *
     * @return mixed
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年08月14日 16:04:28
     */
    public function shop() {
        return $this->belongsTo(Weixin::class, 'wid')->select(['id', 'shop_name']);
    }
}
