<?php

namespace App\Jobs;

use App\S\Message\MessageTemplateLogService;
use App\S\Message\MessageTemplateService;
use App\S\WXXCX\WXXCXSendTplService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMarketingMsg implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    CONST  MARKETING_ORDER = 0;
    CONST  REDUCE_PRICE = 1;
    CONST  MARKETING_SIGN = 2;
    const CARD_CERTIFICATIONS_EXPIRE = 3; //卡券到期
    const PRODUCT_ADVANCE_SALE = 4; //商品预售
    const SERVER_EXPIRE = 5; //商品预售

    public $tries = 3;
    public $timeout = 60;
    protected $data;
    protected $sendTime;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data = [], $sendTime)
    {
        //
        $this->data = $data;
        $this->sendTime = $sendTime;
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
            return;
        }
        switch ($tplSetting['type']) {
            case self::MARKETING_ORDER :
                $return = $this->marketingOrder($tplSetting);
                break;
            case self::REDUCE_PRICE :
                $return = $this->marketingReduce($tplSetting);
                break;
            case self::MARKETING_SIGN :
                $return = $this->marketingSign($tplSetting);
                break;
            case self::CARD_CERTIFICATIONS_EXPIRE :
                $return = $this->CardCertificationExpire($tplSetting);
                break;
            case self::PRODUCT_ADVANCE_SALE :
                $return = $this->ProductAdvanceSale($tplSetting);
                break;
            case self::SERVER_EXPIRE :
                $return = $this->ServerExpire($tplSetting);
                break;
            default:
                return;
        }
        //插入日志
        if ($return && $return['errcode'] == 0) {
            $this->createLog();
        }
    }


    public function marketingOrder($tplSetting = [])
    {
        $data = $this->data;
        $postData = [
            'touser' => $data['toUser'],
            'form_id' => $data['formId'],
            'page' => $tplSetting['url'],
            'data' => [
                'keyword1' => [
                    'value' => $tplSetting['content']['bookTime']
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['bookContent'],
                    'color' => '#ff0015'
                ],
                'keyword3' => [
                    'value' => $tplSetting['remark']
                ],
            ]
        ];
        return (new WXXCXSendTplService($data['wid']))->sendTplNotify($postData, 6);
    }

    public function marketingReduce($tplSetting = [])
    {
        $data = $this->data;
        $postData = [
            'touser' => $data['toUser'],
            'form_id' => $data['formId'],
            'page' => $tplSetting['url'],
            'data' => [
                'keyword1' => [
                    'value' => $tplSetting['content']['productTitle'],
                    'color' => '#ff0015'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['price']
                ],
                'keyword3' => [
                    'value' => $tplSetting['content']['cost_price']
                ],
                'keyword4' => [
                    'value' => $tplSetting['remark']
                ],
            ]
        ];

        return (new WXXCXSendTplService($data['wid']))->sendTplNotify($postData, 7);
    }


    public function marketingSign($tplSetting = [])
    {
        $data = $this->data;
        $postData = [
            'touser' => $data['toUser'],
            'form_id' => $data['formId'],
            'page' => $tplSetting['url'],
            'data' => [
                'keyword1' => [
                    'value' => $tplSetting['content']['remindContent'],
                    'color' => '#ff0015'
                ],
                'keyword2' => [
                    'value' => $tplSetting['remark']
                ],
            ]
        ];

        return (new WXXCXSendTplService($data['wid']))->sendTplNotify($postData, 8);
    }


    /**
     * 发送成功创建记录信息
     * Author: MeiJay
     */
    public function createLog()
    {
        $where = [
            'send_time' => $this->sendTime,
            'message_template_id' => $this->data['msgId'],
            'wid' => $this->data['wid']
        ];
        $service = new MessageTemplateLogService();
        return $service->increase($where);

    }

    /**
     * 卡券到期
     * @param $tplSetting
     * @return mixed
     * @author: 梅杰 2018年8月3日
     */
    public function CardCertificationExpire($tplSetting)
    {

        $data = $this->data;
        $postData = [
            'touser' => $data['toUser'],
            'form_id' => $data['formId'],
            'page' => $tplSetting['url'],
            'data' => [
                'keyword1' => [
                    'value' => $tplSetting['content']['name'],
                    'color' => '#ff0015'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['use_limit'],
                    'color' => '#ff0015'
                ],
                'keyword3' => [
                    'value' => $tplSetting['content']['expiration_time'],
                    'color' => '#ff0015'
                ],

                'keyword4' => [
                    'value' => $tplSetting['remark']
                ]

            ]
        ];

        return (new WXXCXSendTplService($data['wid']))->sendTplNotify($postData, 14);

    }

    /**
     * 商品预售
     * @param $tplSetting
     * @author: 梅杰 2018年8月3日
     */
    public function ProductAdvanceSale($tplSetting)
    {
        $data = $this->data;
        $postData = [
            'touser' => $data['toUser'],
            'form_id' => $data['formId'],
            'page' => $tplSetting['url'],
            'data' => [
                'keyword1' => [
                    'value' => $tplSetting['content']['product_name'],
                    'color' => '#ff0015'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['sale_time'],
                    'color' => '#ff0015'
                ],
                'keyword3' => [
                    'value' => $tplSetting['content']['sale_price'],
                    'color' => '#ff0015'
                ],
                'keyword4' => [
                    'value' => $tplSetting['remark']
                ],
            ]
        ];

        return (new WXXCXSendTplService($data['wid']))->sendTplNotify($postData, 15);
    }

    /**
     * 服务过期
     * @param $tplSetting
     * @return mixed
     * @author: 梅杰 2018年8月3号
     */
    public function ServerExpire($tplSetting)
    {
        $data = $this->data;
        $postData = [
            'touser' => $data['toUser'],
            'form_id' => $data['formId'],
            'page' => $tplSetting['url'],
            'data' => [
                'keyword1' => [
                    'value' => $tplSetting['content']['server_name'],
                    'color' => '#ff0015'
                ],
                'keyword2' => [
                    'value' => $tplSetting['content']['expiration_reason'],
                    'color' => '#ff0015'
                ],
                'keyword3' => [
                    'value' => $tplSetting['content']['server_expiration_time'],
                    'color' => '#ff0015'
                ],
                'keyword4' => [
                    'value' => $tplSetting['remark']
                ],
            ]
        ];

        return (new WXXCXSendTplService($data['wid']))->sendTplNotify($postData, 16);
    }


}
