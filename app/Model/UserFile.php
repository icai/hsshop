<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-3
 * Time: 下午1:25
 */

namespace App\Model;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserFile extends Model
{
    use SoftDeletes;

    /**
     * @desc  表名
     */
    protected $table = 'user_file';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function fileInfo()
    {
        return $this->belongsTo('App\Model\FileInfo', 'file_info_id')->select();
    }

}