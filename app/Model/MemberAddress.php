<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 客户/会员
 */
class MemberAddress extends Model
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
    protected $table = 'member_address';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function province()
    {
        return $this->belongsTo('App\Model\Region','province_id')->select(['id','title']);
    }
    public function city()
    {
        return $this->belongsTo('App\Model\Region','city_id')->select(['id','title']);
    }
    public function area()
    {
        return $this->belongsTo('App\Model\Region','area_id')->select(['id','title']);
    }

}
