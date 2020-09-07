<?php
namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;

class MicroForumLog extends Model
{
    use RedisKeyTrait;

    protected $table = 'microforum_log';

    protected $redisKey = 'log';

	protected static $unguarded = true;
	//
}
