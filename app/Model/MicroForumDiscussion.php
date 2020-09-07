<?php

namespace App\Model;

use App\Lib\Redis\MicroForumRedis;
use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroForumDiscussion extends Model
{
    use RedisKeyTrait;

    protected $table = 'microforum_discussions';

    protected $redisKey = 'discussion';

	protected static $unguarded = true;

}
