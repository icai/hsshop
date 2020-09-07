<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnifiedMember extends Model
{
    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table='unified_member';
    protected $dates = ['created_at', 'updated_at'];

}
