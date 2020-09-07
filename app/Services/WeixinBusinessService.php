<?php 

namespace App\Services;

use App\Model\WeixinBusiness;
use App\S\Weixin\ShopService;

/**
 * 商品类目
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月2日 09:03:51
 */
class WeixinBusinessService extends Service {
    /**
     * 初始化 设置唯一标识和redis键名
     * 
     * @param  array  $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     * 
     * @return this
     */
    public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {

        $this->initialize(new WeixinBusiness(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141523
     * @desc 获取二级分类
     * @param null $pid
     */
    public function getCategory($pid=null,$page=true)
    {
        $where = Array();
        if (!is_null($pid)){
            $where['pid'] = $pid;
        }else{
            $where[1]=1;
        }
        $weixinBusinessData = $this->init()->where($where)->order('sort desc,id desc')->getList($page);
        $this->dealCategory($weixinBusinessData[0]['data']);
        return $weixinBusinessData;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703141524
     * @desc 处理二级分类
     * @param $categoryData
     */
    public function dealCategory(&$categoryData)
    {
        $pCategoryData = $this->init()->where(['pid'=>0])->getList(false)[0]['data'];
        $pCategoryTmp = Array();
        if ($pCategoryData){
            foreach ($pCategoryData as $key=>$val)
            {
                $pCategoryTmp[$val['id']] = $val['title'];
            }
        }
        foreach ($categoryData as &$val)
        {
            $val['p_name'] = $val['pid']?$pCategoryTmp[$val['pid']]:'';
            if ($val['pid']){
                $val['p_name'] = $pCategoryTmp[$val['pid']];
            }else{
                $val['p_name'] = $pCategoryTmp[$val['id']];
                $val['title'] = '';
            }
        }

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703151009
     * @desc 删除分类
     * @param $id
     * @return array
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function delWeixinBusiness($id)
    {
        $result = ['success'=>0,'message'=>''];
        /*判断该分类是否是一级分类*/
        $weixinBusinessData = $this->init()->getInfo($id);
        if (!$weixinBusinessData){
            $result['message'] = '该分类不存在';
            return $result;
        }
        $businessId = [$id];
        if ($weixinBusinessData['pid'] == 0){
            $secBusinessData = $this->init()->where(['pid'=>$id])->getList(false)[0]['data'];
            foreach ($secBusinessData as $val){
                $businessId[] = $val['id'];
            }
        }
        /*判断能否删除该分类*/
        //$weixinService = new WeixinService();
        $where = [
            'business_id' => ['in',$businessId]
        ];
        //$weixinData = $weixinService->init()->where($where)->getList()[0]['data'];
        $weixinData = (new ShopService())->getAllList($where);
        if (isset($weixinData[0]['data']) && $weixinData[0]['data']){
            $result['message'] = '已存在该类型店铺！该分类不能删除';
            return $result;
        }

        foreach ($businessId as $val){
            $this->init()->where(['id' => $val])->delete($val,false);
        }
        $result['success'] = 1;
        return $result;
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170320
     * @desc 获取所有分类
     */
    public function getAllCategory()
    {
        $this->with(['category']);
        $res = $this->init()->where(['pid'=>0])->getList(false)[0]['data'];
        return $res;
    }

    /**
     * @auth fuguowei
     * @param $id  weixin表中的business_id
     * @date 20170320
     * @desc 获取创建店铺选择的类目
     */
    public function getWidShop($id)
    {
        if(empty($id))
        {
            return '店铺没有选择分类';
        }
        $res = $this->init()->where(['id'=>$id])->getList(false)[0]['data'];
        $title = $res[0]['title'];
        return $title;
    }

}
