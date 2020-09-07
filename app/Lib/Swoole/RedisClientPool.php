<?php
namespace App\Lib\Swoole;

class RedisClientPool implements PoolInterface
{

	protected static $redisClientInstances = [];

	protected function __construct()
	{
	
	}

	protected function __clone()
	{
	
	}

	public static function getRedisClient($fd)
	{
		if (isset(self::$redisClientInstances[$fd])) {
			return self::$redisClientInstances[$fd];
		}
		self::$redisClientInstances[$fd] = empty(config('database.redis.default.password')) ? new \swoole_redis() : new \swoole_redis(['password' => config('database.redis.default.password')]);
		return self::$redisClientInstances[$fd];
	}

	public static function releaseRedisClient($fd)
	{
		if (isset(self::$redisClientInstances[$fd])) {
			self::$redisClientInstances[$fd]->close();
			unset(self::$redisClientInstances[$fd]);
		}
	}

	public static function getPoolSize()
	{
		return count(self::$redisClientInstances);
	}
	
}
