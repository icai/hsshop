<?php

namespace App\Jobs;

use App\S\WXXCX\SubscribeMessagePushService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Created by PhpStorm.
 * User: Wuxiaoping
 * Date: 2019/12/10
 * Time: 20:32
 * Description: 添加入库对应店铺的订阅消息模板
 */
class AddSubTemplateJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var 生成添加模板所需的数据（数组形式）
     *
     * 小程序授权appid，模板标题 tid  模板发送场景值 scene
     * 模板对应的关键词id数组 tids
     */
    private $data;

    /**
     * 对应模板的操作（插入还是更新）
     * @var
     */
    private $action;

    /**
     * 构造函数
     * @param: $data  生成添加模板所需的数据（数组形式）

     * @param $mid 店铺id
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 21:00:55
     */
    public function __construct($data, $action)
    {
        $this->data = $data;
        $this->action = $action;
        $this->queue = 'add_sub_template';
    }

    /**
     * @description：执行添加订阅消息模板
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 21:00:55
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }
        if ($this->action == 'update') {
            app(SubscribeMessagePushService::class)->updatePriTmplId($this->data);
        } else {
            app(SubscribeMessagePushService::class)->getSubTemplate($this->data, true);
        }
    }
}
