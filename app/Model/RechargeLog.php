<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 订单操作记录模型
 * @author 黄东 406764368@qq.com
 * @version 2017年2月8日 10:43:04
 */
class RechargeLog extends Model
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
    // protected $table = 'order_logs';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 获取订单操作记录对应的订单
     */
    public function recharge() {
        return $this->belongsTo('App\Model\Recharge')->select(Schema::getColumnListing('recharge'));
    }
}
