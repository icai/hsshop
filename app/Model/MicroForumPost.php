<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroForumPost extends Model
{
    use RedisKeyTrait;

    protected $table = 'microforum_posts';

    protected $redisKey = 'post';

	protected static $unguarded = true;

}
