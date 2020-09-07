<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class MicroForumReply extends Model
{
    use SoftDeletes,RedisKeyTrait;

    protected $table = 'microforum_replies';

    protected $redisKey = 'reply';

	protected static $unguarded = true;

	public function getRepliesByPids(array $postIds)
	{
		return (new static)->whereIn('posts_id', $postIds)->get(['id', 'posts_id', 'parent_id', 'content', 'id_type', 'user_id'])->toArray();	
	}


	public function getRepliesCountByPids(array $postIds)
	{
		return (new static)->select('posts_id', DB::raw('count(*) as num'))->whereIn('posts_id', $postIds)->groupBy('posts_id')->get();	
	}
}
