<?php

namespace App\Jobs;

use App\S\WXXCX\SubscribeMessagePushService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class SendAdvanceSubMsg
 * 延时处理队列用于处理生成发送预售商品的订阅模板消息
 * @package App\Jobs
 * @author 吴晓平 2019年12月27日 16:32:12
 */
class SendAdvanceSubMsg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var 生成发送预售商品订阅模板所需的数据（数组形式）
     *
     * [wid, mid, pid]
     */
    private $data;

    /**
     * 构造函数
     * @param: $data  生成添加模板所需的数据（数组形式）

     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月27日 16:02:15
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->queue = 'send_advance_sub_msg';
    }

    /**
     * @description：发送执行
     * @return bool
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月27日 16:07:51
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        // 模板发送的初步数据
        $data = [
            'wid' => $this->data['wid'],
            'openid' => '',
            'param' => []
        ];
        // 发送模板的相关内容
        $param = [
            'mid' => $this->data['mid'],
            'product_id' => $this->data['pid'],
            'time' => $this->data['time'],
        ];
        // 组装后的数据
        $sendData = app(SubscribeMessagePushService::class)->packageSendData(4, $data);
        dispatch(new SubMsgPushJob(4, $this->data['wid'], $sendData, $param));
    }
}
