<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class XCXStatisticsLog extends Model
{
	//禁止使用  craeted_at 和 updated_at
	public $timestamps = false;
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='xcx_statistics_log';

}
