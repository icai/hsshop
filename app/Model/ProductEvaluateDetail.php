<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 商品模型
 */
class ProductEvaluateDetail extends Model
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
    protected $table = 'product_evaluate_detail';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联到用户
     */
    public function member() {
        return $this->belongsTo('App\Model\Member','mid')->select(Schema::getColumnListing('member'));
    }

    /**
     * 关联到商品
     */
    public function reply() {
		return $this->belongsTo('App\Model\Member','reply_id')->select(Schema::getColumnListing('member'));
    }

}
