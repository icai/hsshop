<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class OrderZiti extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='order_ziti';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * 关联订单自提信息
     * @return [type] [description]
     */
    public function orderZiti()
    {
        return $this->belongsTo('App\Model\Reception','ziti_id','id')->select(Schema::getColumnListing('reception'));
    }

}
