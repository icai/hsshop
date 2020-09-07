<?php

namespace App\Jobs;

use App\S\Message\MessageTemplateLogService;
use App\S\Message\MessageTemplateService;
use App\S\WXXCX\WXXCXSendTplService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Service\ApiService;
use App\Module\WechatBakModule;

class SendWeixinTemp implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    CONST  COMMON_NOTIFY = 0;
    CONST  COURSE_NOTIFY = 1;
    CONST  SERVER_EXPIRE_NOTIFY = 2;
    CONST  PRODUCT_ADVANCE_SALE_NOTIFY = 3;

    public $tries = 3;
    public $timeout = 60;
    protected $data;
    protected $sendTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = [] ,$sendTime)
    {
        //
        $this->data      = $data;
        $this->sendTime  = $sendTime;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $service = new MessageTemplateService();
        $tplSetting = $service->getRowById($this->data['msgId']);
        if (!$tplSetting) {
            return ;
        }
        switch ($tplSetting['type']){
            case self::COMMON_NOTIFY :
                $return = $this->commonNotity($tplSetting);
                break;
            case self::COURSE_NOTIFY :
                $return = $this->courseNotify($tplSetting);
                break;
            case self::SERVER_EXPIRE_NOTIFY :
                $return = $this->advanceServerExpire($tplSetting);
                break;
            case self::PRODUCT_ADVANCE_SALE_NOTIFY :
                $return = $this->ProductAdvanceSale($tplSetting);
                break;
            default:
                return ;
                break;
        }
        //插入日志
        if ($return && $return['errcode'] == 0) {
            $this->createLog();
        }
    }


    /**
     * 通用通知
     * @param  array  $tplSetting [description]
     * @return [type]             [description]
     */
    public function commonNotity($tplSetting = [])
    {
        $data = $this->data;
        $postData = [
            'touser'      => $data['toUser'],
            'url'         => $tplSetting['url'],
            "topcolor"    =>"#FF0000",
            'data'        => [
                'first' => [
                    'value' => $tplSetting['content']['title']
                ],
                'keyword1' => [
                    'value' => $tplSetting['content']['news_type'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['follow_time'],
                    'color' => '#173177'
                ],
                'remark'    => [
                    'value' => $tplSetting['remark'],
                ],
            ]
        ];
        $wechatBakModule = new WechatBakModule();
        return $wechatBakModule->sendTplNotify($data['wid'],$postData,$wechatBakModule::COMMON_NOTIFY);
    }

    /**
     * 课程通知
     * @param  array  $tplSetting [description]
     * @return [type]             [description]
     */
    public function courseNotify($tplSetting = [])
    {
        $data = $this->data;
        $postData = [
            'touser'      => $data['toUser'],
            'url'         => $tplSetting['url'],
            "topcolor"    =>"#FF0000",
            'data'   => [
                'first' => [
                    'value' => $tplSetting['content']['title']
                ],
                'keyword1' => [
                    'value' => $tplSetting['content']['course_name'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['start_time'],
                    'color' => '#173177'
                ],
                'remark'    => [
                    'value' => $tplSetting['remark'],
                ],
            ]
        ];
        $wechatBakModule = new WechatBakModule();
        return $wechatBakModule->sendTplNotify($data['wid'],$postData,$wechatBakModule::COURSE_NOTIFY);
    }


    /**
     * 商品预售通知
     * @param $data
     * @author: 梅杰 2018年8月3日
     */
    private function ProductAdvanceSale($tplSetting)
    {
        $data = $this->data;
        $postData = [
            'touser'      => $data['toUser'],
            'url'         => $tplSetting['url'],
            "topcolor"    =>"#FF0000",
            'data'        => [
                'first' => [
                    'value' => $tplSetting['content']['title']
                ],
                'keyword1' => [
                    'value' => $tplSetting['content']['sale_content'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['sale_time'],
                    'color' => '#173177'
                ],
                'remark'    => [
                    'value' => $tplSetting['remark'],
                ],
            ]
        ];
        $wechatBakModule = new WechatBakModule();
        return $wechatBakModule->sendTplNotify($data['wid'],$postData,$wechatBakModule::PRODUCT_ADVANCE_NOTIFY);
    }

    /**
     * 活动过期
     * @param $data
     * @author: 梅杰 2018年8月3号
     */
    public function advanceServerExpire($data)
    {
        $data = $this->data;
        $postData = [
            'touser'      => $data['toUser'],
            'url'         => $tplSetting['url'],
            "topcolor"    =>"#FF0000",
            'data'        => [
                'first' => [
                    'value' => $tplSetting['content']['title']
                ],
                'keyword1' => [
                    'value' => $tplSetting['content']['book_content'],
                    'color' => '#173177'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['book_time'],
                    'color' => '#173177'
                ],
                'remark'    => [
                    'value' => $tplSetting['remark'],
                ],
            ]
        ];
        $wechatBakModule = new WechatBakModule();
        return $wechatBakModule->sendTplNotify($data['wid'],$postData,$wechatBakModule::SERVER_EXPIRE_NOTIFY);

    }




    /**
     * 发送成功创建记录信息
     * Author: MeiJay
     */
    public function createLog()
    {
        $where = [
            'send_time'             => $this->sendTime,
            'message_template_id'   => $this->data['msgId'],
            'wid'                   => $this->data['wid']
        ];
        $service = new MessageTemplateLogService();
        return $service->increase($where);

    }


}
