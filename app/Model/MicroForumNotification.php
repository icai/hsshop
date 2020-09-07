<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MicroForumNotification extends Model
{
    use RedisKeyTrait;

    protected $table = 'microforum_notifications';

    protected $redisKey = 'notification';

	protected static $unguarded = true;

	public function getNotificationsCountByTid($toId, $isRead = 0, $toType = 0)
	{
		return (new static)->where(['is_read' => $isRead, 'to_type' => $toType, 'to_id' => $toId])->count();
	}

}
