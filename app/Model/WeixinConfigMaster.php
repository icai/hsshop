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
class WeixinConfigMaster extends Model
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
    protected $table = 'weixin_config_master';

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
}
