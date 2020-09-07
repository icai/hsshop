<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2017/7/11
 * Time: 12:03
 */

namespace App\S\Staff;


use App\Lib\Redis\CusServiceRedis;
use App\S\S;

class CusSerManageService extends S
{
    protected $prefixKey = 'CusSerManage:';
    protected $timeOut   = 86400;
    protected $default = [
        'name'=>'客服',
        'phone'=> '13862414586'
    ];
    protected  $redis;

    //新版需求==============cwh
    protected $turnPhone = [
        0   =>  [
            'name'  => '会搜云客服',
            'phone' => '18657103883'
        ],
        1   =>  [
            'name'  =>  '会搜云客服',
            'phone' =>  '13862414586'
        ],
        2   =>  [
            'name'  =>  '会搜云客服',
            'phone' =>  '15657195953'
        ]
    ];
    protected $timeSet = 20180115;
    protected $timeRound = 3;  //3天一个周期

    //=======================

    public function __construct($modelName = '')
    {
        $this->redis = new CusServiceRedis('CusSerInfo');
        parent::__construct($modelName);
    }

    public function getAll($timeSet = 0)
    {
        if ($timeSet == 0) 
            $checkTime = date('Ymd', time());
        else
            $checkTime = date('Ymd', $timeSet);
        
        $checkTime = round((strtotime($checkTime)-strtotime($this->timeSet))/3600/24) ;

        $roundNum = count($this->turnPhone);

        $res = $checkTime%($this->timeRound * $roundNum);

        for ($i=0; $i < $roundNum; $i++) { 
            if (( ($i + 1) * $this->timeRound  ) > $res )
                break;
        }
        return $this->turnPhone[$i];
    }


    /**
     * 创建客服
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        unset($data['_token']);
        return $this->redis->set($data);
    }

    /**
     * 修改客服
     * @param $data
     * @return mixed
     */
    public function update($data)
    {
        unset($data['_token']);
        //写入操作日志表
        $staffOperLogService = new StaffOperLogService();
        $content = [
            'desc' => '修改客服信息',
            'info' => $data
        ];
        $re = $staffOperLogService->write(json_encode($content),1);
        if($re == false)
            return false;
        return $this->redis->set($data);
    }

    /**
     * 删除客服
     * @return bool
     */
    public function del()
    {
        return $this->redis->deleteByKey('CusSerInfo');
    }


    /**
     * 获取客服名
     * @return mixed
     */
    public function getName()
    {
        $data = $this->getAll();
        return $data['name'];
    }


    /**
     * 获取客服电话
     * @return mixed
     */
    public function getPhone()
    {
        $data = $this->getAll();
        return $data['phone'];
    }

    /**
     * @return array
     */
    public function getInfoFromLog()
    {
        $staffOperLogService = new StaffOperLogService();
        $where = [
            'type' => 1
        ];
        $re = $staffOperLogService->readCusInfo($where);
        return !empty($re) ? $re->toArray(): [];
    }

    //获取最新的五条数据
    public function getLogList($where = [], $skip = "", $perPage = "", $orderBy = "", $order = "")
    {
        $staffOperLogService = new StaffOperLogService();
        $list = $staffOperLogService->getListWithPage($where,$orderBy,$order,$perPage);
        return $list;
    }
    


}