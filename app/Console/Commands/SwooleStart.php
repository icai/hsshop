<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lib\Swoole\RedisClientPool;

class SwooleStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'start websocket service';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		//include '/home/johnson/Phpworkspace/hsshop/app/Lib/Swoole/PoolInterface.php';
		//include '/home/johnson/Phpworkspace/hsshop/app/Lib/Swoole/RedisClientPool.php';
        $ws = new \swoole_websocket_server('0.0.0.0', config('app.websocket_port'));
		$ws->set(array(
			'worker_num' => 1,    //worker process num
//			'max_request' => 50,
//			'daemonize' => 1,
		));

		$ws->on('open', function ($ws, $request) {
			if (!isset($request->get['user'])) {
				printf("%s\n", "无效的用户");
				return;
			}
			$redis_client = RedisClientPool::getRedisClient($request->fd);
			$redis_client->on('message', function ($client, $message) use ($ws, $request) {
				if ($message[0] === 'message') {
					$ws->push($request->fd, json_encode(unserialize($message[2])));
				}
			});
			$redis_client->connect(config('database.redis.default.host'), config('database.redis.default.port'), function ($client, $result) use ($request) {
				if ($result === false) {
					printf("%s\n", $client->errMsg);
					return;
				}
				$client->subscribe(config('database.redis.default.prefix') . 'channel_msg_' . $request->get['user']);
			});
		});
		
		$ws->on('message', function ($ws, $frame) {
		});
		
		$ws->on('close', function ($ws, $fd) {
			RedisClientPool::releaseRedisClient($fd);
		});
		
		$ws->start();
    }
}
