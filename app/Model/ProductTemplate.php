<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/23
 * Time: 11:47
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 商品模板实体类
 * Class Product_Template
 * @package App\Model
 * @author jonzhang guo.jun.zhang@163.com
 * @date 2017-03-22
 */
class ProductTemplate extends Model
{
    use SoftDeletes;
    protected $table='product_template';
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}