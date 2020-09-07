<?php
/**
 * Created by PhpStorm.
 * User: zgj
 * Date: 2017/8/9
 * Time: 17:43
 */

namespace App\S\WXXCX;
use App\S\S;

class WXXCXConfigService extends S
{
    const STATUS_STRING_MAP = [
        '0' => '未上传',
        '1' => '已上传代码',
        '2' => '审核中',
        '3' => '审核被拒',
        '4' => '审核成功',
        '5' => '已发布',
        '6' => '取消审核',
        '7'=>'已作废',
        '8'=>'已下架'
    ];

    public function __construct()
    {
        parent::__construct('WXXCXConfig');
    }
    /**
     * todo 添加微信小程序配置信息
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-09
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
        if(empty($data['app_id']))
        {
            $errMsg.='appid为空';
        }
        if(strlen($errMsg)>0)
        {
            $returnData['errCode']=-2;
            $returnData['errMsg']=$errMsg;
            return $returnData;
        }
        //add by jonzhang 2017-11-29
        $data['status_time']=time();
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
     * todo 更改微信小程序中配置信息
     * @param int $id
     * @param array $data
     * @return array
     * @author jonzhang
     * @date 2017-08-09
     * @update 梅杰 20180712 修改返回信息
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
        $returnData['errCode'] = 0;
        $returnData['errMsg'] = '更新数据成功';
        return $returnData;
//        else if($updateReturnValue>0)
//        {
//            $returnData['errCode'] = 0;
//            $returnData['errMsg'] = '更新数据成功';
//            return $returnData;
//        }
//        $returnData['errCode']=1;
//        $returnData['errMsg']='没有要更改的数据';
//        return  $returnData;
    }

    /**
     * todo 通过wid来获取小程序配置信息
     * @param $wid
     * @return array
     * @author add by jonzhang
     * @date 2017-08-09
     * @update 张永辉 2018年7月9日 读取数据按照id正序排序
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
        //current_status 此处必须传0 否则调用该方法的地方 都会有问题
        $result = $this->model->where(['wid' => $wid,'current_status'=>0])->orderBy('id','ASC')->first();
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
                // app_id
                case 'app_id':
                    $where['app_id'] =$value;
                    break;
                //状态 0表示正常 -1表示删除
                case 'current_status':
                    $where['current_status'] =$value;
                    break;
                    //小程序状态
                case 'status':
                    $where['status']=$value;
                    break;
            }
        }
        //查询数据
        $select= $this->model->where($where);
        if(!empty($orderBy))
        {
            $select=$select->orderBy($orderBy,$order??'desc');
        }
        else
        {
            $select=$select->orderBy('id','desc');
        }
        //查询出id,此处为数组
        if(!empty($pageSize))
        {
            //$select->paginate($pageSize)返回的是:LengthAwarePaginator
            //$select->paginate($pageSize)->toArray() 返回的是数组 "total" => 7, "per_page" => 20,"current_page" => 1
            $select=$select->paginate($pageSize)->toArray();
            $idAttr=$select['data'];
            $returnData['total']=$select['total'];
            $returnData['currentPage']=$select['current_page'];
            $returnData['pageSize']=$select['per_page'];
        }
        else
        {
            //$select->get()返回的是集合
            $idAttr=$select->get()->toArray();
        }
        $returnData['data'] = $idAttr;
        return $returnData;
    }

    /**
     * 获取带分页列表
     * @param array $where
     * @param string $orderBy
     * @param string $order
     * @return array
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     *获取全部列表信息（不带分页）
     */
    public function getListAll($where)
    {
        return $this->getList($where);
    }

    public function getListById(array $idArr)
    {
        $list = $this->model->whereIn('id', $idArr)->get()->toArray();
        $list = array_column($list, null,'id');
        $result = [];
        foreach ($idArr as $k => $v) {
            $result[$k] = $list[$v];
        }
        return $result;
    }

    /**
     * 获取所有域名
     */
    public function getAllDomains()
    {
        return $this->model->select('request_domain')->distinct()->get()->toArray();
    }

    /**
     * todo 权限集ID转换为权限集名称
     * @param string $jsonStr
     * @return array
     * @author jonzhang
     * @date 2017-09-25
     */
    public function processFuncInfo($jsonStr='')
    {
        $funcName='';
        if(empty($jsonStr)||$jsonStr=='[]')
        {
            return $funcName;
        }
        $jsonAttr=json_decode($jsonStr,true);
        foreach($jsonAttr as $item)
        {
            $id=$item['funcscope_category']['id'];
            switch($id)
            {
                case 1:
                    $name = '消息管理权限';
                    break;
                case 2:
                    $name = '用户管理权限';
                    break;
                case 3:
                    $name = '帐号服务权限';
                    break;
                case 4:
                    $name = '网页服务权限';
                    break;
                case 5:
                    $name = '微信小店权限';
                    break;
                case 6:
                    $name = '微信多客服权限';
                    break;
                case 7:
                    $name = '群发与通知权限';
                    break;
                case 8:
                    $name = '微信卡券权限';
                    break;
                case 9:
                    $name = '微信扫一扫权限';
                    break;
                case 10:
                    $name = '微信连WIFI权限';
                    break;
                case 11:
                    $name = '素材管理权限';
                    break;
                case 12:
                    $name = '微信摇周边权限';
                    break;
                case 13:
                    $name = '微信门店权限';
                    break;
                case 14:
                    $name = '微信支付权限';
                    break;
                case 15:
                    $name = '自定义菜单权限';
                    break;
                case 16:
                    $name = '未知';
                    break;
                case 17:
                    $name = '帐号管理权限';
                    break;
                case 18:
                    $name = '开发管理权限';
                    break;
                case 19:
                    $name = '客服消息管理权限';
                    break;
            }
            $funcName.=$name.',';
        }
        if(!empty($funcName))
        {
            $funcName = substr($funcName, 0,-1);
        }
        return $funcName;
    }

    /**
     * 通过小程序原始Id 获取店铺id
     * Author: MeiJay
     * @param $OriginId
     * @return mixed
     */
    public function getShopByOriginId($OriginId)
    {
        $data = $this->model->where(['user_name'=>$OriginId])->select('wid')->orderBy('id','desc')->first();
        if ($data) {
            $data = $data->toArray();
        }
        return $data;
    }


    /**
     * 根绝id查询小程序配置信息
     * @param $id 主键
     * @return array 小程序配置信息
     * @author 张永辉 2018年7月5日
     */
    public function getRowById($id)
    {
        $result = ['errCode'=>0,'errMsg'=>'','data'=>[]];
        $result['data'] = $this->model->find($id);
        if (!$result['data']){
           return $result;
        }
        $result['data'] = $result['data']->toArray();
        return $result;
	}

    /**
     * todo 通过id来获取小程序配置信息
     * @param $id
     * @return array
     * @author add by jonzhang
     * @date 20180705 梅杰
	 * @update 20180709 默认返回最新的一条配置信息
     */
    public function getRowByIdWid($wid,$id)
    {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>[]];
        if(empty($wid))
        {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺id为null';
            return $returnData;
        }
        //current_status 此处必须传0 否则调用该方法的地方 都会有问题
        if ($id) {
            $result = $this->model->where(['wid'=>$wid,'id' => $id,'current_status'=>0])->first();
        } else {
            $result = $this->model->where(['wid'=>$wid,'current_status'=>0])->orderBy('created_at','desc')->first();
        }
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
     * 根据条价更新数据
     * @param $where
     * @param $data
     * @return mixed
     * @author: 梅杰 20180712
     */
    public function updateByWid($wid,$data)
    {
        return $this->model->where(['wid' => $wid])->update($data);
    }


    /**
     *  统计数量
     * @param array $where
     * @return mixed
     * @author 张永辉 2018年7月13日
     */
    public function count($where = [])
    {
        return parent::count($where); // TODO: Change the autogenerated stub
    }

    /**
     * 根据id 获取appid
     * @param $id
     * @return array
     * @author: 梅杰 2018年10月24日
     */
    public function getAppId($id)
    {
        if ($model = $this->model->select('app_id')->find($id)) {
            return $model->app_id;
        }
        return [];
    }
}