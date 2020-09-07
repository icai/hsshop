<?php

namespace App\Jobs;

use App\Module\ByteDance\SendTemplateModule;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * @desc 发送字节跳动小程序模板消息
 * @date2019年10月10日16:20:30
 * @author  张永辉 【zhangyh_private@foxmail.com】
 * Class SendByteDanceTemplate
 * @package App\Jobs
 */
class SendByteDanceTemplate implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @desc 消息类型
     *
     * @var int
     */
    public $type;

    /**
     * @desc 消息参数
     *
     * @var  array
     */
    public $data;

    /**
     * Create a new job instance.
     * 构造函数
     * @return void
     * @param $type int 发送消息类型
     * @param $data array 消息参数
     */
    public function __construct($type, $data)
    {
        $this->data = $data;
        $this->type = $type;
        $this->queue = 'SendByteDanceTemplate';

    }

    /**
     * Execute the job.
     *
     * @return void
     * @desc 发送消息队列
     * @author  张永辉 2019年10月10日16:33:54
     */
    public function handle()
    {
        $sendTemplateModule = new SendTemplateModule();
        $sendTemplateModule->setData(['type' => $this->type, 'param' => $this->data])->send();
    }
}
