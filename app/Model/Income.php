<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 商品模型
 */
class Income extends Model
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
    protected $table = 'income';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function member() {
        return $this->belongsTo('App\Model\Member', 'mid')->select(Schema::getColumnListing('member'));
    }
    public function order() {
        return $this->belongsTo('App\Model\Order', 'oid')->select(['id','oid']);
    }
    public function orderMember() {
        return $this->belongsTo('App\Model\Member', 'omid')->select(['id','nickname','mobile']);
    }

}
