<?php
namespace App\S\User;
use App\S\S;
use App\Lib\Redis\UserRedis;
use App\Services\WeixinBusinessService;
use App\S\Foundation\RegionService;
use App\Model\Weixin;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends S
{
	public function __construct()
    {
    	$this->request = app('request');
    	$this->redis = new UserRedis();
        parent::__construct('User');
    } 

    /**
     * 分页获取后台登录帐号列表
     * @author 吴晓平 <2018年09月18日>
     * @param  array   $where    [数组条件]
     * @param  integer $pageSize [每页显示数]
     * @return [type]            [description]
     */
    public function getAllList($whereData = [], $orderBy ='',$order='desc',$pageSize=15)
    {
    	$where = [];
    	if ($whereData) {
    		foreach ($whereData as $key => $value) {
    			switch ($key) {
    				case 'mphone':
    					$where['mphone'] = ['like','%'.$value.'%'];
    					break;
    				case 'name':
    					$where['name'] = ['like','%'.$value.'%'];
    					break;
    				default:
    					# code...
    					break;
    			}
    		}
    	}
    	$orderBy = $orderBy ?? 'created_at';
    	$order = $order ?? 'desc';
    	return $this->getListWithPage($where,$orderBy,$order,$pageSize);
    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-08-21
     */
    public function getListById($idArr = [])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
       
        $result = $this->redis->getArr($idArr);

        //判断是否已存在redis数据，没有则设置redis的数据结构
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }

        //以hash类型保存到redis中
        if (!empty($redisId)) {
            $mysqlData = $this->model->with(['weixin' => function($query){
            	$query->withCount('product');
            }])->whereIn('id',$redisId)->orderBy('logins','desc')->get()->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            foreach ($mysqlData as $key => &$value) {
            	$value['weixin'] = json_encode($value['weixin']);
            }
            $this->redis->setArr($mysqlData);
        }

        return sortArr($idArr, ($redisData + $mysqlData) );
    }

}