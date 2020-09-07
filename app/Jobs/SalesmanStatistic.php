<?php

namespace App\Jobs;

use App\S\Member\MemberService;
use App\S\ShareEvent\LiSalesmanService;
use App\S\ShareEvent\SalesmanStatisticService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SalesmanStatistic implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 3;
    public $timeout = 60;
    private $mid;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mid)
    {
        //
        $this->mid = $mid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MemberService $memberService)
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $memberData = $memberService->getRowById($this->mid);
        $this->_deal($memberData);
    }

    private  function _deal($data)
    {
        if ($data['pid'] == 0 ){
            return true;
        }
        $liSalesmanService = new LiSalesmanService();
        $salesmanStatisticService = new SalesmanStatisticService();
        $res = $liSalesmanService->getList(['wx_mid'=>$data ['pid']]);
        if ($res){
            $adData = [
                'mid' => $data['id'],
                'wid' => $data['wid'],
                'nickname' => $data['nickname'],
                'headimgurl'=> $data['headimgurl'],
                'sex' => $data['sex'],
                'pid' => $data['pid'],
                'topid' => $data['pid'],
                'intime' => $data['created_at'],
                'level' => 1,
            ];
            try{
                $salesmanStatisticService->add($adData);
            }catch (\Exception $exception){
                \Log::info($exception->getMessage());
            }
            return true;

        }else{
            $salesmanStatisticData = $salesmanStatisticService->getList(['mid'=>$data ['pid']]);
            if (!$salesmanStatisticData){
                return true;
            }
            $salesmanStatisticData = current($salesmanStatisticData);
            $adData = [
                'mid' => $data['id'],
                'wid' => $data['wid'],
                'nickname' => $data['nickname'],
                'headimgurl'=> $data['headimgurl'],
                'sex' => $data['sex'],
                'pid' => $data['pid'],
                'topid' => $salesmanStatisticData['topid'],
                'intime' => $data['created_at'],
                'level' => $salesmanStatisticData['level']+1,
            ];
            try{
                $salesmanStatisticService->add($adData);
            }catch (\Exception $exception){
                \Log::info($exception->getMessage());
            }
            return true;
        }

    }

}
