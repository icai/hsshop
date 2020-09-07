<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 订单模型
 * @author 黄东 406764368@qq.com
 * @version 2017年1月12日 21:35:02
 */
class OrderDetail extends Model {
    /**
     * 软删除
     */
    use SoftDeletes;
    
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'order_detail';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 获取订单详情对应的订单
     */
    public function order() {
        return $this->belongsTo('App\Model\Order', 'oid')->select(Schema::getColumnListing('order'));
    }

    public function product() {
        return $this->belongsTo('App\Model\Product', 'product_id')->select(['id','cam_id','is_distribution','distribute_template_id']);
    }
}
