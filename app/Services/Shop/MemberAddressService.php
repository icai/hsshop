<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/29
 * Time: 9:37
 */

namespace App\Services\Shop;

use App\Model\MemberAddress;
use App\S\Member\MemberService;
use App\Services\Service;

class MemberAddressService extends Service
{
    /**
     *
     * FileSystemService constructor.
     * @desc 构造方法
     */
    public function __construct()
    {
        /* http请求类 */
        $this->request = app('request');

        /* 设置所有字段名称 */
        $this->field = ['id','mid','title','province_id','city_id','area_id','address','phone','type','created_at','updated_at','zip_code'];
        $this->with = $this->withAll = ['province','city','area'];


    }
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new MemberAddress(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    public function getAll($mid)
    {
        list($memberAddressData) = MemberAddressService::init('mid',$mid)->getList(false);
        return $memberAddressData;
    }

    /**
     * todo 获取用户的收货地址信息
     * @param $mid
     * @return array
     * @author jonzhang
     * @date 2017-08-17
     */
    public function getUserAddress($mid)
    {
        //存放用户收货地址
        $userAddressInfo=['default'=>[],'all'=>[]];
        if(empty($mid))
        {
            return $userAddressInfo;
        }
        //账号打通地址合并处理，显示不同店铺的地址 add by zhangyh 20171218
        $memberData = (new MemberService())->getRowById($mid);
        if ($memberData['umid'] != 0){
            $userAddressList=MemberAddressService::init()->where(['umid'=>$memberData['umid']])->getList(false);
        }else{
            //用户收货地址信息
            $userAddressList=MemberAddressService::init()->where(['mid'=>$mid])->getList(false);
        }
        //dd($userAddressList);
        foreach($userAddressList[0]['data'] as $item)
        {
            $data=[];
            $data['detail_address']=$item['province']['title'];
            $data['detail_address'].=$item['city']['title'];
            $data['detail_address'].=$item['area']['title'];
            $data['detail_address'].=$item['address'];
            $data['address']=$item['address'];
            $data['phone']=$item['phone'];
            $data['name']=$item['title'];
            $data['id']=$item['id'];
            $data['province_id']=$item['province_id'];
            $data['city_id']=$item['city_id'];
            $data['area_id']=$item['area_id'];
            $data['code']=$item['zip_code'];
            $data['type']=$item['type'];

            //小程序普通订单下单 保存省市区 Herry 20171110
            $data['province'] = $item['province'];
            $data['city'] = $item['city'];
            $data['area'] = $item['area'];

            //默认收货地址
            if($item['type']==1)
            {
                $userAddressInfo['default'][]=$data;
            }
            //所有的收货地址
            $userAddressInfo['all'][]=$data;
        }
        return $userAddressInfo;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171013
     * @desc 根据地址id获取收货地址
     * @param $id
     */
    public function getAddressById($id)
    {
//        $res = $this->init()->getInfo($id);
        $res = $this->init()->model->find($id);
        if (!$res){
            return [];
        }
        $res = $res->load('province')->load('city')->load('area')->toArray();

        $res['detail'] = $res['province']['title'].$res['city']['title'].$res['area']['title'].$res['address'];
        return $res;
    }

    /**
     * 获取用户收货地址，如果有默认地址则返回默认地址，否则返回最新地址
     * @param $mid
     * @return array|string
     * @author: 梅杰 2018年7月27日
     * @update 梅杰 2018年7月30日 地址返回信息数据格式
     */
    public function getAddressByMid($mid)
    {
        $res = $this->init()->model->where('mid',$mid)->orderBy('type','desc')->first();
        if (!$res){
            return [];
        }
        $res = $res->load('province')->load('city')->load('area')->toArray();
        $address['address'] = ($res['province']['title'] ?? '').($res['city']['title'] ?? '').($res['area']['title'] ?? '') .$res['address'] ;
        $address['title'] = $res['title'];
        $address['phone'] = $res['phone'];
        return $address;
    }


}