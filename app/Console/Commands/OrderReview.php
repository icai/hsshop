<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OrderService;
use OrderDetailService;
class OrderReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OrderReview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将前面的数据进行更新';

    /**
     * Execute the console command.
     *
     * todo
     * @return mixed
     */
    public function handle()
    {
        \Log::info('将前面的数据进行更新');
        $obj = OrderDetailService::init()->model->select(['oid','is_evaluate'])->where(['is_evaluate' => 1]) ->groupBy('oid')->get();
        if($obj){
            $data = $obj->toArray();
            $orderIds = [];
            foreach ($data as $val){
                $orderIds[] =  $val['oid'];
            }

            $getList = OrderService::init()->getList(false, $orderIds);

            if($getList)
            {
                foreach($getList[0]['data'] as $k=>$v)
                {
                    $detail = $v['orderDetail'];
                    $result = count($detail);
                    $countt = 0;
                    foreach($detail as $val)
                    {
                        if($val['is_evaluate'] != 0){
                            $countt++;
                        }
                    }
                    if($countt == $result){
                        OrderService::init()->model->where(['id'=>$v['id']])->update(['ievaluate'=>1],false);
                    }
                }
            }
        }

    }
}






