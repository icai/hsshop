<?php

namespace App\Console\Commands;

use App\S\Wechat\WeChatShopConfService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HandleWXConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handle:wxconfig';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将之前的payment数据导入weixin_config_sub';

    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(WeChatShopConfService $chatShopConfService)
    {
        $this->service = $chatShopConfService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $orData = $this->getAll();
        foreach ($orData as $v) {
            $temp['id'] = $v['id'];
            $temp['status'] = $v['payment']['status'];
            $conf = json_decode($v['payment']['config'], 1);
            if (empty($conf['app_secret'])) {
                continue;
            }
            $a = array_merge($temp, $conf);
            $data[] = $a;
        }
        $re = self::updateBatch('ds_weixin_config_sub',$data);
        dd($re);
        return $re;
    }

    //获取服务号相关信息
    public function getAll()
    {
        return $this->service->model->select(['id','wid','app_id'])->with(['payment'=>function($query){
            $query->orderBy('created_at','desc');
        }])->where(['deleted_at' => null,'service_type_info'=>2])->get()->toArray();
    }
    static function updateBatch($tableName = "", $multipleData = array()){

        if( $tableName && !empty($multipleData) ) {
            // column or fields to update
            $updateColumn = array_keys($multipleData[0]);
            $referenceColumn = $updateColumn[0]; //e.g id
            unset($updateColumn[0]);
            $whereIn = "";
            $sql = "UPDATE ".$tableName." SET ";
            foreach ( $updateColumn as $uColumn ) {
                $sql .=  $uColumn." = CASE ";
                foreach( $multipleData as $data ) {
                    $sql .= "WHEN ".$referenceColumn." = ".$data[$referenceColumn]." THEN '".$data[$uColumn]."' ";
                }
                $sql .= "ELSE ".$uColumn." END, ";
            }
            foreach( $multipleData as $data ) {
                $whereIn .= "'".$data[$referenceColumn]."', ";
            }
            $sql = rtrim($sql, ", ")." WHERE ".$referenceColumn." IN (".  rtrim($whereIn, ', ').")";
            // Update
            return DB::update(DB::raw($sql));
        } else {
            return false;
        }
    }
}
