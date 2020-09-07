<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/10
 * Time: 18:54
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 微页模板类
 * Class MicroPageTemplate
 * @package App\Model
 * @author jonzhang  guo.jun.zhang@163.com
 * @date 2017-03-10
 */
class MicroPageTemplate extends Model
{
    use SoftDeletes;
    protected $table='micro_page_template';
    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}