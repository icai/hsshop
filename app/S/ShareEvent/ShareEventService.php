<?php
namespace App\S\ShareEvent;
use App\Lib\Redis\ShareEventRedis;
use App\S\S;

class ShareEventService extends S
{
    public function __construct()
    {
        parent::__construct('ShareEvent');
    }

    public function create($data)
    {
    	$data['created_time'] = time();
    	//add by jonzhang 元转化为分
        if(isset($data['lower_price']))
        {
            $data['lower_price']=$data['lower_price']*100;
        }
        if(isset($data['unit_amount']))
        {
            $data['unit_amount']=$data['unit_amount']*100;
        }
        return $this->model->insertGetId($data);
    }

    public function update($where, $data)
    {
    	$data['updated_time'] = time();
    	//add by jonzhang 把元转化为分
    	if(isset($data['lower_price']))
        {
            $data['lower_price']=$data['lower_price']*100;
        }
        if(isset($data['unit_amount']))
        {
            $data['unit_amount']=$data['unit_amount']*100;
        }
        //todo 这里必须要有 id
        $id = $where['id'];
        $res = $this->model->wheres($where)->update($data);
        if (!$res){
            return false;
        }
        $redis = new ShareEventRedis();
        return $redis->updateOne($id,$data);
    }

    public function list($input = [],$orderBy = '', $order = '',$pageSize=0)
    {
        /* 查询条件数组 */
        $where = ['1'=>1];
        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                switch ( $key ) {
                    case 'wid':
                        $where['wid'] = $value;
                        break;
                    case 'productId':
                        $where['product_id'] = $value;
                        break;
                    case 'type':
                        $where['type'] = $value;
                        break;
                    case 'status':
                        $where['status'] = $value;
                        break;
                    case 'id':
                        $where['id'] = $value;
                        break;
                    case 'title':
                        $where['title'] = $value;
                        break;
                        //add by jonzhang
                    case 'endTime':
                        $where['end_time']= $value;
                        break;
                        //add by jonzhang
                    case 'startTime':
                        $where['start_time']= $value;
                        break;
                        //add by jonzhang
                    case 'product_name':
                        $where['product_name']= ['like', '%' . $value . '%'];
                        break;
                    case 'activityStatus':
                        if($value==1)
                        {
                            $where['start_time'] = ['<=', time()];
                            $where['end_time'] = ['>=', time()];
                        }
                        else if($value==0)
                        {
                            $where['start_time']=['>',time()];
                        }
                        else if($value==-1)
                        {
                            $where['end_time']=['<',time()];
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $this->getListWithPage($where,$orderBy , $order ,$pageSize);
    }

    public function getListById(array $idArr)
    {
        $result = [];
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
        $redis = new ShareEventRedis();
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

    public function getOne($id, $wid)
    {
    	$result = [];
        $redis = new ShareEventRedis();
        $result = $redis->getRow($id);

        if (empty($result)) {
            $result = $this->model->wheres(['id' => $id])->first();
            if (!$result) {
                return [];
            }
            $result = $result->toArray();
            $redis->add($result);
        }
        if (isset($result['wid']) && $result['wid'] != $wid) {
        	return false;
        }
        return $result;
    }

    /***
     * @param array $input
     * @return array
     * @author jonzhang
     * @date 2017-12-13
     */
    public function getRow($input=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $where=[];
        if ( $input ) {
            foreach ($input as $key => $value) {
                switch ( $key )
                {
                    case 'type':
                        $where['type'] = $value;
                        break;
                    case 'status':
                        $where['status'] = $value;
                        break;
                    case 'id':
                        $where['id'] = $value;
                        break;
                    case 'productId':
                        $where['product_id'] = $value;
                        break;
                }
            }
        }
        else
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为空';
            return $returnData;
        }
        $result = $this->model->where($where)->first();
        if(empty($result))
        {
            if($result===false)
            {
                $returnData['errCode'] = -2;
                $returnData['errMsg'] = '查询数据出现错误';
                return $returnData;
            }
            return $returnData;
        }
        $returnData['data']=$result->toArray();
        return  $returnData;
    }

    /**
     * 添加助减数量
     * add wuxiaoping 2018.01.10
     */
    public function incrementReduceTotal($id)
    {
        $this->model->wheres(['id' => $id])->increment('reduce_total');
        $redis = new ShareEventRedis();
        $redis->increment($id,'reduce_total');
    }

    /***
     * todo 获取享立减信息
     * @param array $input
     * @return mixed
     * @author jonzhang
     * @date 2018-04-25
     */
    public function getListWithCondition($input = [])
    {
        /* 查询条件数组 */
        $where = ['1'=>1];
        /* 参数转换为查询条件数组 */
        if ( $input ) {
            foreach ($input as $key => $value) {
                switch ( $key ) {
                    case 'wid':
                        $where['wid'] = $value;
                        break;
                    case 'product_id':
                        $where['product_id'] = $value;
                        break;
                    case 'type':
                        $where['type'] = $value;
                        break;
                    case 'status':
                        $where['status'] = $value;
                        break;
                    case 'id':
                        $where['id'] = $value;
                        break;
                    case 'title':
                        $where['title'] = $value;
                        break;
                    //add by jonzhang
                    case 'endTime':
                        $where['end_time']= $value;
                        break;
                    //add by jonzhang
                    case 'startTime':
                        $where['start_time']= $value;
                        break;
                    //add by jonzhang
                    case 'product_name':
                        $where['product_name']= ['like', '%' . $value . '%'];
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $this->getList($where);
    }

    /**
     * 判断该商品是否正在参与享立减活动
     * @param $productId
     * @return mixed
     * @author: 梅杰 20180717
     */
    public function isShareProduct($productId = [])
    {
        return $this->model->where(['type' => 0, 'status' => 0])->where('end_time','>=',time())->whereIn('product_id',$productId)->count();
    }


}