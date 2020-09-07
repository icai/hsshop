<?php

namespace App\Listeners;

use App\Events\NewUserEvent;
use App\Jobs\sendDcLog;
use App\Lib\Redis\NewUserFlagRedis;
use App\S\Foundation\Bi;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewUserEventListen
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewUserEvent $event
     * @return void
     */
    public function handle(NewUserEvent $event)
    {
        //先做 享立减 拼团 新用户统计
        $data = $event->data;
        $data['source'] = app('request')->get('source');
        switch ($data['source']) {

            case 1:
                $data['mid'] = app('request')->session()->get('mid');
                $data['wid'] = app('request')->session()->get('wid');
                break;
            case 2:
                $data['mid'] = app('request')->input('mid');
                $data['wid'] = app('request')->input('wid');
                break;
            default:
                return ;
                break;
        }
        dispatch((new sendDcLog($data,1)));
        (new NewUserFlagRedis())->delete($data['mid']);
    }
}
