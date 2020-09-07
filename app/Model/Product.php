<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 商品模型
 */
class Product extends Model
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
    protected $table = 'product';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联到规格表
     */
    public function productProp() {
        return $this->hasMany('App\Model\ProductProp', 'product_id')->select(Schema::getColumnListing('product_prop'));
    }

    /**
     * 关联到规格表
     */
    public function productImg() {
        return $this->hasMany('App\Model\ProductImg', 'product_id')->select(Schema::getColumnListing('product_img'));
    }

    /**
     * 关联到规格表
     */
    public function productMsg() {
        return $this->hasMany('App\Model\ProductMsg', 'product_id')->select(Schema::getColumnListing('product_msg'));
    }

    public function template() {
        return $this->belongsTo('App\Model\DistributeTemplate', 'distribute_template_id')->select(Schema::getColumnListing('distribute_template'));
    }

    public function productGroup()
    {
        return $this->hasMany('App\Model\ProductGroup', 'id','group_id')->select(Schema::getColumnListing('product_group'));
    }

    /**
     * 关联到批发价表
     */
    public function productWholesale() {
        return $this->hasMany('App\Model\ProductWholesale', 'product_id')->select(Schema::getColumnListing('product_wholesale'));
    }
}
