<?php
/**
 * Created by PhpStorm.
 * Author: MeiJay
 * Date: 2018/6/11
 * Time: 11:01
 */

namespace App\Http\Controllers\WebApi;


use App\Http\Controllers\Controller;
use App\Lib\Redis\Member;
use Illuminate\Http\Request;
use MemberService;
use DB;

class DataCenterController extends Controller
{
    /**
     * 更新最近访问时间
     * Author: MeiJay
     * @param Request $request
     * @update 2018年10月25日 同步修改缓存
     */
    public function updateLastLoginTime(Request $request)
    {
        $data = $request->input();
        foreach ( $data as &$v) {
            $v['latest_access_time'] = date('Y-m-d H:i:s',$v['latest_access_time']);
        }
        if ($data) {
            $this->updateBatch($data);
            (new Member())->batchUpdateHash($data);
        }
        
    }


    /**
     * 批量更新
     * Author: MeiJay
     * @param array $multipleData
     * @return bool
     */
    public function updateBatch($multipleData = [])
    {
        try {
            if (empty($multipleData)) {
                throw new \Exception("数据不能为空");
            }
            $tableName = 'ds_member'; // 表名
            $firstRow  = current($multipleData);
            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
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