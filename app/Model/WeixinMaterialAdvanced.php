<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 高级图文模型
 * 
 * @author 黄东 406764368@qq.com
 * @version  2017年3月27日 11:39:12
 */
class WeixinMaterialAdvanced extends Model
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
    protected $table = 'weixin_material_advanced';

    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
