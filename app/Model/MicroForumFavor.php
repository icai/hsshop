<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Illuminate\Notifications\Notifiable;

class MicroForumFavor extends Model
{
    use RedisKeyTrait,Notifiable;

    protected $table = 'microforum_favors';

    protected $redisKey = 'favor';

	protected static $unguarded = true;

	public function getFavorsByPids(array $postId)
	{
		return (new static)->whereIn('posts_id', $postId)->get(['id', 'posts_id', 'id_type', 'user_id'])->toArray();	
	}

	public function getFavorsByPidsAndUids(array $postId, array $userId, $idType = 0)
	{
		return (new static)->whereIn('posts_id', $postId)->whereIn('user_id', $userId)->where('id_type', $idType)->get(['id', 'posts_id', 'id_type', 'user_id'])->toArray();
	}

}
