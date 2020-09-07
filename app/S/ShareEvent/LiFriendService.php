<?php
namespace App\S\ShareEvent;
use App\S\S;
use App\Lib\Redis\LiFriendRedis;

//1、访问自己页面，其他人页面，须 初始化自己的享立减 即查询自己的享立减
//2、分享后, 查询数据，未分享，则调用接口
//3、有人帮我立减之后，记录立减时间，更新立减人数，传递是否已经满

//5、领取成功，修改我的享立减
class LiFriendService extends S
{
	public function __construct()
    {
        parent::__construct('LiFriend');
    }

    private function __return($err_code = 0, $msg = 'success',$data = [])
    {
        $return = [
            'msg'       => $msg,
            'err_code'  => $err_code,
            'data'      => $data
        ];
        return  $return;
    }

    //获取我的好友列表
    public function getMyFriendList($mid, $event_id)
    {
        return $this->getListByKey($mid,$event_id);
    }

    /**
     * 是否互为好友
     * @return [type] [description]
     */
    public function checkFriendIstrue($mid,$mmid,$event_id)
    {
        $result = [];
        $redis = new LiFriendRedis();
        $where['mid'] = $mid;
        $where['mmid'] = $mmid;
        $where['event_id'] = $event_id;
        $result = $redis->getRowStrByWhere($where);
        if (!$result) {
            $obj = $this->model->wheres($where)->first();
            if ($obj) {
                $result = $obj->toArray();
                $redis->setLiFriendToStr('mid:mmid:event_id',$result);
            }
        }else {
            $result = json_decode($result,true);
        }
        return $this->__return(0,'',$result);
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     */
    public function getListByKey($mid,$event_id)
    {

        $mmids = [];
        $redis = new LiFriendRedis();
        $redisKey = $mid.":".$event_id;
        $result = $redis->getSmembers($redisKey);
        if (!$result) {
            $where['mid'] = $mid;
            $where['event_id'] = $event_id;
            $obj = $this->model->wheres($where)->get();
            if ($obj) {
                $result = $obj->toArray();
                foreach ($result as $key => $value) {
                    $mmids[] = $value['mmid'];
                }
                $redis->setAdd($redisKey,$mmids);
            }
        }else{
            $mmids = $result;
        }
        return $mmids;
    }

    /**
     * 创建记录
     * @param  [array] $data [要插入的数组数据]
     * $data 组成必须包含 mid,mmid,event_id
     * @return [type]       [description]
     */
    //创建记录
    public function createLiFriend($data)
    {
        if (!isset($data['mid']) && !isset($data['mmid']) && !isset($data['event_id'])) {
            return $this->__return(3,'信息不完整');
        }
        $result = $this->checkFriendIstrue($data['mid'],$data['mmid'],$data['event_id']);
        if ($result['data']) {
            return $this->__return(2,'已互为好友');
        }
        $insertData[0]['mid'] = $data['mid'];
        $insertData[0]['mmid'] = $data['mmid'];
        $insertData[0]['event_id'] = $data['event_id'];
    	$insertData[0]['create_time'] = time();
        $insertData[1]['mid'] = $data['mmid'];
        $insertData[1]['mmid'] = $data['mid'];
        $insertData[1]['event_id'] = $data['event_id'];
        $insertData[1]['create_time'] = time();
        foreach ($insertData as $key => $value) {
            $obj = $this->model->insertGetId($value);
        }
        if(!$obj)
        {
            return $this->__return(1,'数据库添加失败');
        }
        /**
         * 存放到redis集合中
         */
        $redis = new LiFriendRedis();
        $redis->setAdd($data['mid'].":".$data['event_id'],[$data['mmid']]);
        $redis->setAdd($data['mmid'].":".$data['event_id'],[$data['mid']]);
        return $this->__return();
        
    }

}
