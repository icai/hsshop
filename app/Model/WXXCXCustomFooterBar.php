<?php
/**
 * Created by PhpStorm.
 * User: hsz
 * Date: 2018/6/14
 * Time: 15:48
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WXXCXCustomFooterBar extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='wxxcx_custom_footer_bar';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}
