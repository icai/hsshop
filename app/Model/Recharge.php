<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

/**
 * 充值模型
 *
 * @author 吴晓平
 * @version 2017年4月11日 
 */
class Recharge extends Model
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
    protected $table = 'recharge';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联到充值操作
     *
     * @author 吴晓平 
     * @version  2017年4月11日 
     *
     * @return Builder
     */
    public function rechargeLog() {
        return $this->hasMany('App\Model\RechargeLog')->select(Schema::getColumnListing('recharge_log'));
    }

    
}
