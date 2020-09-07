<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class InfoRecommend extends Model
{
    use  SoftDeletes;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='info_recommend';

    /**
     * 日期属性
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function information()
    {
        return $this->hasOne('App\Model\Information', 'id','info_id')->select(Schema::getColumnListing('information'));
    }
    public function recommend()
    {
        return $this->hasOne('App\Model\Recommend', 'id','rec_id')->select(Schema::getColumnListing('recommend'));

    }
}
