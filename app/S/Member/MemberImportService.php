<?php

namespace App\S\Member;

use App\Lib\Redis\Member as MemberRedis;
use App\S\S;

class MemberImportService extends S
{
    /**
     * 构造方法
     *
     *
     * @return void
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id', 'wid', 'total', 'success_num', 'fail_num', 'card_id', 'isverify',
            'editor', 'created_at'];

        parent::__construct('MemberImport');
    }

    public function getListByConditionWithPage($input)
    {
        /* 查询条件数组 */
        $where = ['1'=>1];

        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                if ( empty($value) ) {
                    continue;
                }
                switch ( $key ) {
                    case 'wid':
                        $where['wid'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $this->getListWithPage($where);
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new MemberRedis('import');
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key])) 
                $redisId[] = $value;
            else 
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) { 
            $mysqlData = $this->model->whereIn('id',$redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData); 
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    public function add($input)
    {
        return $this->model->insertGetId($input);
    }

    public function update($id, $data)
    {
        $updateReturnValue = $this->model->where(['id'=>$id])->update($data);
        $redis = new MemberRedis('import');
        $redis->updateHashRow($id, $data);
        return true;
    }
}