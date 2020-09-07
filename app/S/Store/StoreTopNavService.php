<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2018/5/17
 * Time: 9:31
 */

namespace App\S\Store;
use App\Lib\Redis\StoreTopNavRedis;
use App\S\S;

class StoreTopNavService extends S
{
    public function __construct()
    {
        parent::__construct('StoreTopNav');
    }

    /**
     * todo 添加微商城首部导航
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2018-05-17
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
            $errMsg.='店铺id为空';
        }
        if(empty($data['template_data'])||$data['template_data']=='[]')
        {
            $data['template_data']=null;
        }
        else
        {
            if(is_array($data['template_data']))
            {
                $data['template_data'] = json_encode($data['template_data']);
            }
            else
            {
                //验证数据是否为标准的json字符串
                $validateData=json_decode($data['template_data'],true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['create_time']=time();
        $data['update_time']=time();
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
     * todo 更改微商城首部导航
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-12-27
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
        $data['update_time']=time();
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-3;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new StoreTopNavRedis();
        $updateData=$data;
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
                $returnData['errCode']=-204;
                $returnData['errMsg']='处理缓存失败';
                return $returnData;
            }
        }
        return $returnData;
    }

    /**
     * todo 获取微商城首部导航信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-12-27
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
        $redis = new StoreTopNavRedis();
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
     * todo 通过店铺id来获取微商城首部导航
     * @param $wid
     * @return array
     * @author jonzhang
     * @date 2018-05-17
     */
    public function getRow($wid)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($wid))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id为null';
            return $returnData;
        }
        $result = $this->model->select(['id'])->where(['wid' => $wid])->first();
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
        $result=$result->toArray();
        return $this->getRowById($result['id']);
    }
}