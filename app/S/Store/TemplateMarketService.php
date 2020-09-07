<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/7/6
 * Time: 10:40
 */

namespace App\S\Store;
use App\Lib\Redis\TemplateMarketRedis;
use App\S\S;

class TemplateMarketService extends S
{
    public function __construct()
    {
        parent::__construct('TemplateMarket');
    }
    /**
     * todo 查询店铺模块信息[分页]
     * @param $data
     * @return array
     * @author jonzhang
     * @date 2017-07-06
     */
    public function getListByConditionWithPage($data=[],$orderBy='',$order='',$pageSize=0)
    {
        /* 查询条件数组 */
        $where = [];

        /* 参数转换为查询条件数组 */
        if ($data) {
            foreach ($data as $key => $value) {
                switch ( $key ) {
                    case 'use_case':
                        $where['use_case'] = $value;
                        break;
                    // 状态
                    case 'current_status':
                        $where['current_status'] = $value;
                        break;
                    case 'id':
                        $where['id'] = $value;
                        break;
                    case 'type':
                        $where['type'] = $value;
                        break;
                    case 'source':
                        $where['title'] = ['in', ['自定义模版', '美妆配饰模板04']];
                        break;
                }
            }
        }
        return $this->getListWithPage($where,$orderBy,$order,$pageSize);
    }

    /**
     * todo 涉及到分页此方法必须有，基类调用了此方法
     * todo 通过数组id来查询模板信息
     * @param array $idArr
     * @return array
     * @author jonzhang
     * @date 2017-07-06
     */
    public function getListById($idArr=[])
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);

        $redis = new TemplateMarketRedis();

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

    /**
     * todo 获取微页面模板信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-07-07
     */
    public function getRowById($id,$isCache=true)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $result=[];
        $redis = new TemplateMarketRedis();
        //使用缓存
        if($isCache)
        {
            $result = $redis->getOne($id);
        }
        if(empty($result))
        {
            $result = $this->model->where(['id' => $id])->first();
            if (empty($result))
            {
                if($result===false)
                {
                    $returnData['errCode'] = -2;
                    $returnData['errMsg'] = '查询数据出现错误';
                    return $returnData;
                }
                return $returnData;
            }
            $result=$result->toArray();
            $redis->addArr($result);
        }
        $returnData['data']=$result;
        return $returnData;
    }

    /**
     * todo 添加微页面模板数据信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
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
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改微页面模板数据信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-07-03
     */
    public function updateData($id=0,$data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>''];
        if(empty($id))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if($updateReturnValue===false)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new TemplateMarketRedis();
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
}