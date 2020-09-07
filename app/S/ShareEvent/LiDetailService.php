<?php
namespace App\S\ShareEvent;
use App\Jobs\SendTplMsg;
use App\S\S;
use App\Lib\Redis\LiDetail;

//1、访问自己页面，其他人页面，须 初始化自己的集赞 即查询自己的享立减
//2、分享后, 查询数据，未分享，则调用接口 改为已分享
//3、有人帮我赞后，记录立减时间，更新集赞人数，传递是否已经满
//4、领取成功，修改我的集赞
class LiDetailService extends S
{
	public function __construct()
    {
        parent::__construct('LiDetail');
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

    //获取我的享立减数据
    public function getRowByMidAndEventId($mid,$event_id)
    {
        $result = [];
        $redis = new LiDetail($event_id);
        $result = $redis->getRow($mid);

        if (empty($result)) {
            $result = $this->model->wheres(['mid' => $mid, 'event_id' => $event_id])->first();
            if (!$result) {
            	$data['mid'] 	  = $mid;
            	$data['event_id'] = $event_id;
                $this->createLiDeatil($data);

                $result = [
                	'mid' 			   => $mid,
                	'event_id' 		   => $event_id,
                	'is_share' 		   => 0,
                	'is_full'  		   => 0,
                	'is_buy'   		   => 0,
                	'last_reduce_time' => 0,
                	'reduce_num'       => 0,
                	'create_time'      => time(),
                    //是否弹出温馨提示 第一次弹出
                    'is_first'         =>1,
                    'rank'             => 0
                ];
            } else {
	            $result = $result->toArray();
	            $redis->addLiDetail($mid, $result);            	
            }
        }
        return $result;
    }

    //创建记录
    private function createLiDeatil($data)
    {
    	$data['create_time'] = time();
        $obj = $this->model->insertGetId($data);
        if(!$obj)
        {
            return $this->__return(1,'数据库添加失败');
        }
        return $this->__return();
    }

    //分享成功 修改我的享立减数据
    public function updateShareLiDetail($mid, $event_id)
    {
        $shareDetail = $this->getRowByMidAndEventId($mid,$event_id);
        $check_time = date('Ymd');
        $shareDetail['share_time'] = $shareDetail['share_time'] > 0 ? date('Ymd', $shareDetail['share_time']) : 0;
        if ($check_time != $shareDetail['share_time']) {
            $data['is_share'] = 1;
        } else {
            $data['is_share'] = $shareDetail['is_share'] + 1;
        }
        $data['share_time'] = time();
    	
    	$updateReturnValue = $this->model->where(['mid'=>$mid,'event_id'=>$event_id])->update($data);

        $redis = new LiDetail($event_id);
        $redis->setShareData($mid, $data);
        
        return $this->__return(0,'success',$data);
    }

    //领取成功 修改我的享立减
    public function updateFullLiDetail($mid, $event_id)
    {
    	$data['is_buy'] = 1;
    	$data['is_full'] = 1;
    	//$data['rank'] = $this->getCount(['event_id'=>$event_id,'is_full'=>1])+1;
    	$updateReturnValue = $this->model->where(['mid'=>$mid,'event_id'=>$event_id])->update($data);

        $redis = new LiDetail($event_id);
        $redis->setShareData($mid, $data);
        
        return $this->__return();
    }

    //有人帮我享立减
    public function updateEventLiDetail($mid, $event_id, $full_num)
    {
    	$result = $this->model->wheres(['mid' => $mid, 'event_id' => $event_id])->first();
        if ($result['is_full'] == 1  ) {
            return $this->__return();
        }
        $this->model->wheres(['mid' => $mid, 'event_id' => $event_id])->increment('reduce_num',1);
        $result['reduce_num'] = $result['reduce_num'] + 1;

    	$data['last_reduce_time'] = -1 * (time() -  $result['create_time']);
    	if ($result['reduce_num'] == $full_num) {
    	    $job = new SendTplMsg(['mid'=>$mid,'flag' => 1 ,'full'=>$full_num,'num' => $result['reduce_num'],'event_id'=>$event_id ],13);
    	    dispatch($job->onQueue('SendTplMsg'));
        }
        if ($result['reduce_num'] == $full_num/2) {
            $job = new SendTplMsg(['mid'=>$mid,'flag' => 0 ,'full'=>$full_num,'num' => $result['reduce_num'],'event_id'=>$event_id ],13);
            dispatch($job->onQueue('SendTplMsg'));
        }
    	if ($result['reduce_num'] >= $full_num) {
    		$data['is_full'] = 1;
            $data['rank'] = $this->getCount(['event_id'=>$event_id,'is_full'=>1])+1;
    	}
        $data['is_share'] = 1;
    	$data['reduce_num'] = $result['reduce_num'];

    	$updateReturnValue = $this->model->where(['mid'=>$mid,'event_id'=>$event_id])->update($data);

        $redis = new LiDetail($event_id);
        $redis->setShareData($mid, $data);
        
        return $this->__return();
    }

    //获取我的好友信息
    public function getMyFriendList($mids, $event_id)
    {
       $where['mid'] = ['in', $mids];
       $where['event_id'] = $event_id;
       $this->setParameter('all');
       return  $this->getListWithPage($where, ['reduce_num', 'last_reduce_time', 'id'], 'desc', 10);
    }

    public function getCount($where=[])
    {
        return $this->count($where);
    }


}
