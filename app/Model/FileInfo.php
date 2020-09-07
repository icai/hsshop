<?php
/**
 * Created by PhpStorm.
 * User: zhangyh
 * Date: 17-3-3
 * Time: 上午10:41
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FileInfo extends Model
{
    use SoftDeletes;

    /**
     * @desc  表名
     */
    protected $table = 'file_info';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * 关联关系
     */
    public function userFile() {
        return $this->hasOne('App\Model\UserFile', 'file_info_id')->select(['id','user_id','file_info_id','file_classify_id','created_at']);
    }
}