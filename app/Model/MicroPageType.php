<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/10
 * Time: 14:21
 */

namespace App\Model;
use Illuminate\Database\Eloquent\Model;

/**
 * 微页面类型
 * Class MicroPageType
 * @package App\Model
 * @author jonzhang guo.jun.zhang@163.com
 * @date 2017-03-10
 */
class MicroPageType extends Model
{
    protected $table='micro_page_type';
    protected $dates = ['created_at', 'updated_at'];
}