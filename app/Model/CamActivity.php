<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class CamActivity extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='cam_activity';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];


    public function camList()
    {
        return $this->hasMany('App\Model\CamList','cam_id','id')->where(['is_send' => 0])->select(Schema::getColumnListing('cam_list'));
    }

}
