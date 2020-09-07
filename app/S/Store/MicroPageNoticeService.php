<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/6/28
 * Time: 15:11
 */

namespace App\S\Store;
use App\Lib\Redis\MicroPageNoticeRedis;
use App\S\S;
use MallModule as NoticeStoreService;

class MicroPageNoticeService extends S
{
    public function __construct()
    {
        parent::__construct('MicroPageNotice');
    }

    /**
     * todo 添加公共广告信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-28
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
        if(is_null($data['is_used']))
        {
            $errMsg.='is_used为null';
        }
        if(empty($data['position']))
        {
            $errMsg.='position为null';
        }
        if(empty($data['apply_location'])||$data['apply_location']=='[]')
        {
            $data['apply_location']=null;
        }
        else
        {
            if(is_array($data['apply_location']))
            {
                $data['apply_location']=json_encode($data['apply_location']);
            }
            else
            {
                $validateData=json_decode($data['apply_location'],true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-2;
                    $returnData['errMsg']='出现的页面数据格式不符合要求';
                    return $returnData;
                }
            }
        }
        if(empty($data['notice_template_info'])||$data['notice_template_info']=='[]')
        {
            $data['notice_template_info']=null;
        }
        else
        {
            if(is_array($data['notice_template_info']))
            {
                $data['notice_template_info'] = json_encode($data['notice_template_info']);
            }
            else
            {
                $validateData=json_decode($data['notice_template_info'],true);
                if(empty($validateData))
                {
                    $returnData['errCode']=-3;
                    $returnData['errMsg']='模板数据格式不符合要求';
                    return $returnData;
                }
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-4;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-5;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改公告信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-06-28
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
        $redis=new MicroPageNoticeRedis();
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

    /**
     * todo 获取公告信息
     * @param $id
     * @param bool $isCache
     * @return array
     * @author jonzhang
     * @date2017-06-28
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
        $redis = new MicroPageNoticeRedis();
        //使用缓存
        if($isCache)
        {
            $result = $redis->getOne($id);
        }
        if(empty($result))
        {
            $result = $this->model->where(['id' => $id])->first();
            //有数据时，把数据插入reids
            if(empty($result))
            {
                if ($result===false)
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
     * 通过店铺id来获取该店铺的公告信息  
     * @param $wid
     * @return array
     * @author jonzhang
     * todo  [无需这么繁琐的判断，有数据就返回，没数据就不返回]————cwh 2018.7.25
     * @date 2017-06-28
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

    /**
    *todo 获取公告的应用范围
    *@author jonzhang
    *@date   2017-07-24
    **/
    public  function getNoticeApplication($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        $wid=$data['wid']??'';
        $applyId=$data['apply_id']??3;
        if(empty($wid))
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']='店铺id为null';
            return $returnData;
        }
        $result=$this->getRow($wid);
        if($result['errCode']==0&&!empty($result['data']))
        {
            $isOn=$result['data']['is_used'];
            if(!$isOn)
            {
                $returnData['errCode']=1;
                $returnData['errMsg']='公共广告未开启';
                return $returnData;
            }
            $location=$result['data']['apply_location'];
            if(empty($location)||$location=='[]')
            {
                $returnData['errCode']=2;
                $returnData['errMsg']='公共广告没有被应用';
                return $returnData;
            }
            $location=json_decode($location,true);
            if(empty($location))
            {
                $returnData['errCode']=-2;
                $returnData['errMsg']='数据转化出现问题';
                return $returnData;
            }
            if(in_array($applyId,$location))
            {
                $returnData['data']['position']=$result['data']['position'];
                $returnData['data']['noticeTemplateData']=$result['data']['notice_template_info'];
                if(!empty($returnData['data']['noticeTemplateData']))
                {
                    if($returnData['data']['noticeTemplateData']=='[]')
                    {
                        $returnData['data']['noticeTemplateData']=null;
                    }
                    else
                    {
                        $returnData['data']['noticeTemplateData']= NoticeStoreService::processTemplateData($wid, $returnData['data']['noticeTemplateData']);
                    }
                }
                return $returnData;
            }
            else
            {
                $returnData['errCode']=3;
                $returnData['errMsg']='该店铺下的公共广告没有被商品应用';
                return $returnData;
            }
        }
        else
        {
            return $result;
        }
        return  $returnData;
    }
}