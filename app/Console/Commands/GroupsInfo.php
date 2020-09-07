<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GroupsInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'groupsInfo:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'groupsinfo info';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redis = new \swoole_redis();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->server = $server = new \swoole_websocket_server("127.0.0.1", 9502);
        $server->on('open', [$this, 'onOpen']);
        $server->on('message', [$this, 'onMessage']);
        $server->on('close', [$this, 'onClose']);
        $server->start();
    }

    public function onOpen(\swoole_websocket_server $server, \swoole_http_request $request)
    {

    }

    public function onMessage(\swoole_websocket_server $server, \swoole_websocket_frame $frame)
    {
        $this->redis->on('message', function ($client, $message) use ($server, $frame) {
            $server->push($frame->fd,json_encode($message[2]));
        });
        $this->redis->connect(config('database.redis.default.host'), config('database.redis.default.port'), function ($client, $result) use ($server,$frame) {
            $client->subscribe('groupsInfo');
        });
    }


    public function onClose(\swoole_websocket_server $server, $fd)
    {

    }






}

















