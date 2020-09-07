<?php
/**
 * Created by PhpStorm.
 * Author: wuxiaoping
 * Date: 2018/4/17
 * Time: 14:00
 * 微信公众号粉丝统计队列任务
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;
use App\S\Wechat\WeixinConfigSubService;
use App\Services\Wechat\ApiService;

class UserSummary implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $dates;
    protected $wid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($wid=0,$dates=[])
    {
        $this->wid = $wid;
        $this->dates = $dates;
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

        $bi = new Bi();
        $apiService = new ApiService();
        if ($this->wid) {
            //指定公众号并指定日期时间段的统计
            if ($this->dates) {
                $result = $apiService->getUserSummary($this->wid,$this->dates);
                if (!isset($result['errcode'])) {
                    $rs = $bi->storeUserSersummary($this->wid,$result);
                    \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
                    \Log::info($rs);
                }
                
            }else {
                //指定公众号并未指定日期时间段的统计
                $dates = $bi->getDates();
                foreach ($dates as $key => $value) {
                    $newDates['begin_date'] = $value[0];
                    if ($key == 13) {
                        $newDates['end_date'] = $value[5];
                    }else {
                        $newDates['end_date'] = $value[6];
                    }
                    $result = $apiService->getUserSummary($this->wid,$newDates); 
                    if (!isset($result['errcode'])) {
                        $rs = $bi->storeUserSersummary($this->wid,$result);
                        \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
                        \Log::info($rs);
                    }else {
                        \Log::info('调用微信接口统计粉丝量出错原因--'.$result['errmsg']);
                        break;
                    }
                }
                
            }
        }else {
            $weixinConfigSubService = new WeixinConfigSubService();
            $list = $weixinConfigSubService->getAllList();
            if ($list) {
                //未指定微信公众号同时也未指定日期时间段的统计
                if (empty($this->dates)) {
                    $dates = $bi->getDates();
                    foreach ($list as $value) {
                        foreach ($dates as $key=>$date) {
                            $newDates['begin_date'] = $date[0];
                            if ($key == 13) {
                                $newDates['end_date'] = $date[5];
                            }else {
                                $newDates['end_date'] = $date[6];
                            }
                            $result = $apiService->getUserSummary($value['wid'],$newDates);
                            if (!isset($result['errcode'])) {
                                $rs = $bi->storeUserSersummary($value['wid'],$result);
                                \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
                                \Log::info($rs);
                            }else {
                                \Log::info('调用微信接口统计粉丝量出错原因--'.$result['errmsg']);
                                continue 2;
                            }
                        }   
                    }
                }else {
                    //未指定微信公众号，并指定了日期时间段
                    foreach ($list as $key => $value) {
                        $result = $apiService->getUserSummary($value['wid'],$this->dates);
                        if (!isset($result['errcode'])) {
                            $rs = $bi->storeUserSersummary($value['wid'],$result);
                            \Log::info('队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:');
                            \Log::info($rs);
                        }else{
                            \Log::info('调用微信接口统计粉丝量出错原因--'.$result['errmsg']);
                            continue;
                        }
                    }
                }
            }
        }


    }
}
