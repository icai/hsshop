<?php

namespace App\Lib\Redis;


class NotificationRedis extends RedisInterface
{
    protected $prefixKey = 'notification';
    //过期时间默认为秒 过期时间为30s
    protected $timeOut   = 30;

	public function publishNotification($channelId, $content)
	{
		$this->redis->PUBLISH('channel_msg_' . $channelId, serialize($content));
	}
}
