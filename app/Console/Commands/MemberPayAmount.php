<?php

namespace App\Console\Commands;

use App\Lib\Redis\Member;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Predis\Response\Status;

class MemberPayAmount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MemberPayAmount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        DB::table('order')->select(['mid as id',DB::raw('SUM(pay_price) as amount')])
            ->whereIn('status',[1,2,3,7])->groupBy('mid')->chunk(500,function ($orders){
                $data = [];
                foreach ($orders as &$v) {
                    $data[] = [
                        'id' => $v->id,
                        'amount' => $v->amount
                    ];
                }

                if ( false === $this->updateBatch($data,'member') ) {

                    \Log::info('统计消费金额更新数据库失败');

                    return false;
                }
                //批量更新Redis
                $re = (new Member())->batchUpdateHash($data);

                if ($re instanceof Status && $re->getPayload() == 'OK') {
                    \Log::info('统计消费金额success');
                }else {

                    \Log::info('统计消费金额更新redis失败');
                }


        });
    }


    /**
     * 批量更新
     * @param array $multipleData
     * @param string $tableName
     * @param string $referenceColumn
     * @return bool
     * @author: 梅杰 2018年9月6号
     */
    public function updateBatch($multipleData = [],$tableName = '',$referenceColumn = 'id')
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception("数据不能为空");
            }
            $tableName = DB::getTablePrefix() . $tableName; // 表名
            $firstRow  = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
//            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets      = [];
            $bindings  = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn   = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings  = array_merge($bindings, $whereIn);
            $whereIn   = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            // 传入预处理sql语句和对应绑定数据
            return DB::update($updateSql, $bindings);
        } catch (\Exception $e) {
            return false;
        }
    }
}
