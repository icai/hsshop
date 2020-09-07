<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes,RedisKeyTrait;

	protected $table = 'notification';

    protected $redisKey = null;

	protected static $unguarded = true;

	public function scopeIsRead($query, $isRead)
	{
		return $query->where('is_read', $isRead);
	}

	public function scopeNotificationType($query, $notificationType)
	{
		return $query->where('notification_type', $notificationType);
	}
}
