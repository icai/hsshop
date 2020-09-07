<?php

namespace App\Model;

use App\Lib\RedisKeyTrait;
use Illuminate\Database\Eloquent\Model;

class NotificationSubscribe extends Model
{
    use RedisKeyTrait;

	protected $table = 'notification_subscribe';
	
    protected $redisKey = 'subscribe';

	protected static $unguarded = true;

	//update 梅杰 2018年10月22日 注释发给买家消息
	protected static $topicItems = [
		1 => ['title' => '买家发起退款', 'introduce' => '', 'isSubscribed' => false],
		2 => ['title' => '新订单提醒', 'introduce' => '', 'isSubscribed' => false],
		3 => ['title' => '买家已退货提醒', 'introduce' => '', 'isSubscribed' => false],
		4 => ['title' => '退款临近超时提醒', 'introduce' => '', 'isSubscribed' => false],
//        5 => ['title' => '商家发货成功', 'introduce' => '', 'isSubscribed' => false],
//        6 => ['title' => '买家付款成功', 'introduce' => '', 'isSubscribed' => false],
	];

	public function scopeSubscriberId($query, $subscriberId)
	{
		return $query->where('subscriber_id', $subscriberId);
	}

	public function scopeSubscriberIdType($query, $subscriberIdType)
	{
		return $query->where('subscriber_id_type', $subscriberIdType);
	}

	public function scopeNotificationType($query, $notificationType)
	{
		return $query->where('notification_type', $notificationType);
	}

	public function getTopicItems()
	{
		return static::$topicItems;
	}
}
