<?php

namespace App\Console\Commands;

use App\Model\AdStatistic;
use App\S\Member\MemberService;
use Illuminate\Console\Command;
use DB;

class AdStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'AdStatistics {start_time?} {end_time?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计广告流量';
    protected $memberService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(MemberService $memberService)
    {
        parent::__construct();
        $this->memberService = $memberService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $start_time = $this->argument('start_time');
        $end_time = $this->argument('end_time');
        if (empty($start_time)){
            $start_time = date('Y-m-d',strtotime("-1 day"))." 00:00:00";
        }
        if (empty($end_time)){
            $end_time = date('Y-m-d',time()).' 00:00:00';
        }
        $where = [
            'wid' => ['in',['626','661']],
            'pid' => ['<>','0'],
            'created_at' => ['between', [$start_time,$end_time ]]
        ];
        $memberData = $this->memberService->getList($where);
        echo count($memberData);
        foreach ($memberData as $val){
            $this->_deal($val,$val);
        }
        $sql = 'SELECT ads.mid FROM ds_ad_statistic as ads LEFT JOIN  ds_groups_detail as gd ON ads.mid = gd.member_id WHERE ads.is_open_groups=0 AND gd.id IS NOT NULL GROUP BY mid';
       $data = DB::select($sql);
       $data = json_decode(json_encode($data),true);
       $ids = array_column($data,'mid');
       if ($ids){
           AdStatistic::whereIn('mid',$ids)->update(['is_open_groups'=>'1']);
       }

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180512
     * @desc 处理数据
     * @param $data
     */
    private  function _deal($tempData,$data,$level=1)
    {
        $ids = ['127011','231431','231446'];
        $source = [
            '127011'     => '小程序',
            '231431'     => '公众号文章',
            '231446'     => '分众电梯',
        ];
        if (in_array($tempData['pid'],$ids)){
            $adData = [
                'mid' => $data['id'],
                'wid' => $data['wid'],
                'nickname' => $data['nickname'],
                'headimgurl'=> $data['headimgurl'],
                'sex' => $data['sex'],
                'pid' => $data['pid'],
                'topid' => $tempData['pid'],
                'intime' => $data['created_at'],
                'level' => $level,
            ];
            try{
                AdStatistic::insertGetId($adData);
            }catch (\Exception $exception){
                $this->info($exception->getMessage()) ;
            }
            return true;

        }elseif (!in_array($tempData['pid'],$ids) && $tempData['pid'] != 0){
            $this->info('pid = '.$tempData['pid']) ;
            $this->info('level='.$level);
            if ($level>10){
                return true;
            }
            $val = $this->memberService->getRowById($tempData['pid']);
            $this->_deal($val,$data,$level+1);
            return true;
        }


    }


}
