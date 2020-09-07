<?php
/*
|--------------------------------------------------------------------------
| 省市区模型
|--------------------------------------------------------------------------
|所有的省市区操作
|
*/
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Region extends Model
{
    use  SoftDeletes;
    /**
     * 应该被调整为日期的属性
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
}
