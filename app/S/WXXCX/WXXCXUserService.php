<?php
namespace App\S\WXXCX;
use App\S\S;
use App\Lib\Redis\WXXCXUserRedis;
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/8
 * Time: 14:33
 */
class WXXCXUserService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXUser');
    }
    /**
     * todo 添加微信小程序中用户信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-08
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='插入的数据为空';
            return $returnData;
        }
        $errMsg='';
        if(empty($data['wid']))
        {
            $errMsg.='wid为空';
        }
        if(empty($data['open_id']))
        {
            $errMsg.='openid为空';
        }
        if(empty($data['avatar_url']))
        {
            $errMsg.='avatar_url为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改微信小程序中用户信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-08
     */
    public function updateData($id=0,$data=[])
    {
        $returnData = ['errCode' => 0, 'errMsg' => ''];
        if (empty($id))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }
        if (empty($data))
        {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '更新的数据为空';
            return $returnData;
        }
        $updateReturnValue = $this->model->where(['id' => $id])->update($data);
        if ($updateReturnValue === false)
        {
            $returnData['errCode'] = -3;
            $returnData['errMsg'] = '更新数据失败';
            return $returnData;
        }
        else if($updateReturnValue>0)
        {
            $redis=new WXXCXUserRedis();
            $updateData=$data;
            $updateData['updated_at']=date('Y-m-d H:i:s');
            $i=0;
            $status=false;
            while($i<3&&!$status)
            {
                if($redis->updateRedis($id,$updateData))
                {
                    $status=true;
                }
                $i++;
            }
            if(!$status)
            {
                $deleteReturnValue=$redis->deleteRedis($id);
                if(!$deleteReturnValue)
                {
                    $returnData['errCode']=-4;
                    $returnData['errMsg']='处理缓存失败';
                    return $returnData;
                }
            }
            return $returnData;
        }
        $returnData['errCode']=1;
        $returnData['errMsg']='没有要更改的数据';
        return  $returnData;
    }

    /**
     * todo 查询微页面类型信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-26
     */
    public function getListByCondition($data=[],$orderBy='',$order='',$pageSize=0)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($data))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '查询条件为null';
            return $returnData;
        }
        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        foreach ($data as $key => $value)
        {
            switch ( $key )
            {
                //店铺id
                case 'wid':
                    $where['wid'] = $value;
                    break;
                // id
                case 'id':
                    $where['id'] =$value;
                    break;
                // open_id
                case 'open_id':
                    $where['open_id'] =$value;
                    break;
            }
        }
        //查询数据
        $select= $this->model->select(['id'])->where($where);
        if(!empty($orderBy))
        {
            $select=$select->orderBy($orderBy,$order??'desc');
        }
        else
        {
            $select=$select->orderBy('id','desc');
        }
        //查询出id,此处为数组
        $idAttr=[];
        if(!empty($pageSize))
        {
            $select=$select->paginate($pageSize)->toArray();
            $idAttr=$select['data'];
        }
        else
        {
            //$select->get()返回的是集合
            $idAttr=$select->get()->toArray();
        }
        if(!empty($idAttr))
        {
            $ids=[];
            foreach ($idAttr as $item)
            {
                array_push($ids,$item['id']);
            }
            //通过id来查询对应的信息
            $returnData['data'] = $this->getListById($ids);
        }
        return $returnData;
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-07-26
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new WXXCXUserRedis();

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
}