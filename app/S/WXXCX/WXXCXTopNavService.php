<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/12/27
 * Time: 17:08
 */

namespace App\S\WXXCX;
use App\Lib\Redis\WXXCXTopNavRedis;
use App\S\S;

class WXXCXTopNavService extends S
{
    public function __construct()
    {
        parent::__construct('WXXCXTopNav');
    }

    /**
     * todo 添加小程序首部导航
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-12-27
     */
    public function insertData($data=[])
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        if(empty($data))
        {
            $returnData['errCode']=-201;
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
                    $returnData['errCode']=-202;
                    $returnData['errMsg']='数据格式不符合要求';
                    return $returnData;
                }
            }
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-203;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        $data['create_time']=time();
        $data['update_time']=time();
        $id=$this->model->insertGetId($data);
        if(!$id)
        {
            $returnData['errCode']=-204;
            $returnData['errMsg']='插入数据失败';
            return $returnData;
        }
        $returnData['data']=$id;
        return $returnData;
    }

    /**
     * todo 更改小程序首部导航
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
            $returnData['errCode']=-201;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        if(empty($data))
        {
            $returnData['errCode']=-202;
            $returnData['errMsg']='更新的数据为空';
            return $returnData;
        }
        $data['update_time']=time();
        $updateReturnValue=$this->model->where(['id'=>$id])->update($data);
        if(!$updateReturnValue)
        {
            $returnData['errCode']=-203;
            $returnData['errMsg']='更新数据失败';
            return $returnData;
        }
        $redis=new WXXCXTopNavRedis();
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
     * todo 获取小程序首部导航信息
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
            $returnData['errCode']=-201;
            $returnData['errMsg']='id为空';
            return $returnData;
        }
        $result=[];
        $redis = new WXXCXTopNavRedis();
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
                    $returnData['errCode'] = -202;
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
     * todo 通过店铺id来获取小程序首部导航
     * @param $wid
     * @return array
     * @author jonzhang
     * @date 2017-12-27
     */
    public function getRow($wid)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($wid))
        {
            $returnData['errCode'] = -201;
            $returnData['errMsg'] = '店铺id为null';
            return $returnData;
        }
        $result = $this->model->select(['id'])->where(['wid' => $wid])->first();
        if(empty($result))
        {
            if($result===false)
            {
                $returnData['errCode'] = -202;
                $returnData['errMsg'] = '查询数据出现错误';
                return $returnData;
            }
            return $returnData;
        }
        $result=$result->toArray();
        return $this->getRowById($result['id']);
    }


    /**
     * 获取顶部导航
     * @param $wid 店铺id
     * @return mixed|string 导航数据
     * @author 张永辉 2018年7月5日
     */
    public function getTopNav($wid)
    {
        $topNavData=$this->getRow($wid);
        $headerData = '';
        if($topNavData['errCode'] == 0 && !empty($topNavData['data'])) {
            if($topNavData['data']['is_on'] && !empty($topNavData['data']['template_data'])) {
                $headerData=json_decode($topNavData['data']['template_data'],true);
                $i=1;
                foreach($headerData as &$item) {
                    //$i==1表示店铺首页不需要重新获取数据
                    if($i>1) {
                        //效验数据 删除的数据不显示
                        $xcxPageData = (new WXXCXMicroPageService())->getRowById($item['pageId']);
                        if ($xcxPageData['errCode'] < 0 || ($xcxPageData['errCode'] == 0 && empty($xcxPageData['data']))) {
                            unset($item);
                        }
                    }
                    $i++;
                }
                $headerData =json_encode($headerData);
            }
        }
        return $headerData;
    }


}