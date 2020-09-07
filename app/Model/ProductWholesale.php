<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ProductWholesale extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'product_wholesale';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at'];

    /**
     * 关联主表
     *
     * @return Bulider
     */
    public function product() {
        return $this->belongsTo('App\Model\Product', 'product_id')->select(Schema::getColumnListing('product'));
    }
}
