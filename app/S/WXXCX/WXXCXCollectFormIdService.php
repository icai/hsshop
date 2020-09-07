<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/1/15
 * Time: 11:43
 */

namespace App\S\WXXCX;


use App\Jobs\SendMarketingMsg;
use App\S\Message\MessageTemplateService;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendWeixinTemp;
use Illuminate\Support\Facades\Redis;

class WXXCXCollectFormIdService
{

    /**
     * 小程序formId 保存
     *
     * @param int $mid 用户id
     * @param array $formData 收集到的formId
     *
     * @return bool 是否保存成功
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月24日 16:23:57
     */
    public function save(int $mid, array $formData)
    {
        try {
            Redis::pipeline(function ($pipe) use ($formData, $mid) {
                $pipe->zremrangebyscore('form_ids:' . $mid, 0, time() + 100);
                foreach ($formData as $formId) {
                    if (!isset($formId['form_ids']) || !isset($formId['expire'])) {
                        continue;
                    }
                    $pipe->zAdd('form_ids:' . $mid, $formId['expire'] - 300, $formId['form_ids']);
                }
            });
        } catch (\Exception $exception) {
            \Log::info("formId保存失败：" . $exception->getMessage());
            return false;
        }
        return true;
    }


    /**
     * 获取一个formId
     *
     * @param int $mid 用户id
     *
     * @return bool|string formId
     *
     * @author: 梅杰[meijie3169@dingtalk.com] at 2019年09月24日 16:30:00
     */
    public function getFormId(int $mid)
    {
        if (Redis::ZCARD('form_ids:' . $mid)) {
            //删除调试的
            Redis::zrem('form_ids:' . $mid, 'the formId is a mock one');
            //删除马上过期或者已经过期的
            if ($data = Redis::zrange('form_ids:' . $mid, 0, 0)) {
                //返回并删除已经使用的
                Redis::zremrangebyrank('form_ids:' . $mid, 0, 0);
                return $data[0];
            }
        }
        return false;
    }

    /**
     * Author: MeiJay
     * @param $wid
     * @param $type
     * @param $msgId
     * @return int
     */
    public function send($wid,$msgId,$sendTime)
    {
        $where = [
            ['form_ids.wid',$wid],
            ['member.xcx_openid','<>',''],
            ['deleted_at','=',null]
        ];
        $messageService  = new MessageTemplateService();
        if (!$messageService->getRowById($msgId)) {
            return false;
        }
        $filed = ['member.id','member.xcx_openid'];
        DB::table('form_ids')->where($where)->select($filed)->join('member','form_ids.mid','=','member.id')
            ->chunk(50, function ($users) use( $wid ,$msgId ,$sendTime)
        {
            if ($users) {
                foreach ($users as &$user) {
                    $data = [
                        'toUser'    => $user->xcx_openid ,
                        'wid'       => $wid ,
                        'msgId'     => $msgId,
                        'mid'       => $user->id
                    ];
                   $this->sendOne($data,$sendTime);
                }
            }
        });
        return true;
    }


    /**
     * Author: MeiJay
     * @param array $data
     * @param $type
     */
    public function sendOne($data = [],$sendTime)
    {
        $formId = $this->getFormId($data['mid']);
        if ($formId) {
            $data['formId'] = $formId;
            $job = new SendMarketingMsg($data,$sendTime);
            dispatch($job->onQueue('SendMarkTplMsg'));
        }
    }

    /**
     * 发送微信模板消息
     * @return [type] [description]
     */
    public function sendWeixinTemp($wid,$msgId,$sendTime)
    {
        $messageService  = new MessageTemplateService();
        if (!$messageInfo = $messageService->getRowById($msgId)) {
            return false;
        }
        $where = [
            ['wid',$wid],
            ['status',0],
            ['openid','<>',''],
            ['deleted_at']
        ];
        DB::table('member')->where($where)->select('openid')->chunk(50,function($users) use($wid,$messageInfo,$sendTime,$msgId) 
        {
            if ($users) {
                foreach ($users as $user) {
                    $data = [
                        'toUser'    => $user->openid ,
                        'wid'       => $wid ,
                        'type'      => $messageInfo['type'],
                        'msgId'     => $msgId,
                    ];
                    $job = new SendWeixinTemp($data,$sendTime);
                    dispatch($job->onQueue('SendWeixinTemp'));
                }
            }

        });
        return true;
    }
}