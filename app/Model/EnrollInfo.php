<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnrollInfo extends Model
{
	public $timestamps = false;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='enroll_info';

}
