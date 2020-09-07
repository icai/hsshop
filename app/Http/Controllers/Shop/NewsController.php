<?php

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\S\Wechat\WeixinMaterialAdvancedService;
use App\S\Wechat\WeixinMaterialWechatService;
use Illuminate\Http\Request;
use WeixinService;
use App\S\Weixin\ShopService;

/**
 * 图文
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年4月10日 10:45:49
 */
class NewsController extends Controller
{
    /**
     * 图文详情
     * 
     * @param  Request $request [http请求实例]
     * @param  string  $id      [图文id]
     * @return view
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function detail(Request $request, WeixinMaterialWechatService $weixinMaterialWechatService, WeixinMaterialAdvancedService $weixinMaterialAdvancedService, ShopService $shopService,$wid, $id = '')
    {
        // id解密
        $id = idencrypt($id, false);

        $wid = session('wid');

        // 查询图文详情
        $detail = $weixinMaterialWechatService->getRowById($id);
        empty($detail) && error('图文不存在或已删除');

        // 查询店铺信息
        //$weixinInfo = WeixinService::getStageShop($wid);
        $weixinInfo = $shopService->getRowById($wid);

        return view('shop.news.detail', [
            'title'      => $detail['title'] ?? '图文详情',
            'detail'     => $detail,
            'weixinInfo' => $weixinInfo,
        ]);
    }

     //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     public function Mdetail(Request $request, WeixinMaterialWechatService $weixinMaterialWechatService, WeixinMaterialAdvancedService $weixinMaterialAdvancedService,ShopService $shopService,$wid, $id = '')
    {
        $wid = session('wid');

        // 查询图文详情
        $detail = $weixinMaterialWechatService->getRowById($id);
        empty($detail) && error('图文不存在或已删除');
        // 查询店铺信息
        //$weixinInfo = WeixinService::getStageShop($wid);
        $weixinInfo = $shopService->getRowById($wid);
        return view('shop.news.Mdetail', [
            'title'      => $detail['title'] ?? '图文详情',
            'detail'     => $detail,
            'weixinInfo' => $weixinInfo,
        ]);
    }
    
    /**
     * 图文素材详情预览
     * @author wuxiaoping <2017.12.21>
     * @param  Request                       $request                       [description]
     * @param  WeixinMaterialWechatService   $weixinMaterialWechatService   [微信图文service]
     * @param  WeixinMaterialAdvancedService $weixinMaterialAdvancedService [高级图文service]
     * @param  [int]                         $type                          [图文类型  1-微信图文  2-高级图文]
     * @param  [int]                         $id                            [图文主键id]
     * @return [type]                                                       [description]
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function materialDetail(Request $request,WeixinMaterialWechatService $weixinMaterialWechatService, WeixinMaterialAdvancedService $weixinMaterialAdvancedService,ShopService $shopService,$wid,$type,$id)
    {
        $returnData = [];
        if ($type == 1) {
            $MaterialWechatList = $weixinMaterialWechatService->getRowById($id);
            if ($MaterialWechatList) {
                //微信单条图文
                if($MaterialWechatList['type'] == 1){
                    $returnData = $MaterialWechatList;
                }else{
                    $_childData = $weixinMaterialWechatService->getChildList($id);
                    $returnData = $MaterialWechatList;
                    foreach($_childData as $child){
                        $returnData['child'][] = $child;
                    }

                }
            }
        }else if ($type == 2) {
            $MaterialAdvancedList = $weixinMaterialAdvancedService->getRowById($id);
            if ($MaterialAdvancedList) {
                //高级单条图文
                if($MaterialAdvancedList['type'] ==1 ){
                    $returnData = $MaterialAdvancedList;
                }else{
                    $_childData = $weixinMaterialAdvancedService->getChildList($id);
                    $returnData = $MaterialAdvancedList;
                    foreach($_childData as $child){
                        $returnData['child'][] = $child;
                    }
                }
                $returnData['content_source_url'] = $returnData['href'];
            }
        }

        if (empty($returnData)) {
            error('操作异常，没有对应的图文数据');
        }
        //获取店铺信息
        /*$weixinData = [];
        $obj = WeixinService::init()->model->where(['id' => $wid])->first()->load('weixinConfigSub');
        if ($obj) {
            $weixinData = $obj->toArray();
        }*/
        $weixinData = $shopService->getRowById($wid,['weixinConfigSub']);
        if ($weixinData) {
            $weixinData['weixinConfigSub'] = json_decode($weixinData['weixinConfigSub'],true);
        }
    	return view('shop.news.materialDetail',[
            'title'      => $returnData['title'] ?? '图文详情',
            'type'       => $type,
            'returnData' => $returnData,
            'weixinData' => $weixinData
    	]);
    }

   
}
