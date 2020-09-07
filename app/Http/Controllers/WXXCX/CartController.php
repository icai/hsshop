<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/8/11
 * Time: 14:27
 */

namespace App\Http\Controllers\WXXCX;


use App\Http\Controllers\Controller;
use App\Module\DiscountModule;
use App\Module\ProductModule;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductService;
use App\S\Product\RemarkService;
use App\Services\Shop\CartService;
use App\Services\WeixinService;
use Illuminate\Http\Request;
use App\Module\AddCartModule;
use  Validator;
use App\S\Weixin\ShopService;

class CartController extends Controller
{

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170811
     * @desc 购物车列表
     * @param Request $request
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function index(Request $request,ShopService $shopService)
    {
        $memberId = $request->input('mid');
        $wid = $request->input('wid');
        $cartService = new CartService();
        list($cartData) = $cartService->getCart($memberId,$wid);
        foreach ($cartData['data'] as &$val){
            $val['img'] = config('app.url').$val['img'];
        }
        /*$shopData = (new WeixinService())->init()->model->find($wid);
        if ($shopData){
            $shopData = $shopData->toArray();
        }*/
        $shopData = $shopService->getRowById($wid);
        xcxsuccess('操作成功',[$cartData['data'],$shopData]);
    }

    public function add(Request $request)
    {
        $input = $request->input();
        $mid   = $input['mid'];
        $wid   = $input['wid'];

        $test = request()->input('test');
        $rule = Array(
            'id'               => 'required',
            'num'              => 'required'
        );
        $message = Array(
            'id.required'            => '请选择商品',
            'num.required'           => '数量不能为空'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails())
            xcxerror($validator->errors()->first());
        $res = (new AddCartModule())->addCart($mid, $wid);
        if ($res['success'] != 1)
            xcxerror($res['message']);

        xcxsuccess('添加购物车成功',$res['data']);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170823
     * @desc 删除购物车
     * @param Request $request
     * @param CartService $cartService
     */
    public function del(Request $request,CartService $cartService)
    {
        $input = $request->input();
        $rule = Array(
            'ids'               => 'required',
        );
        $message = Array(
            'ids.required'     => '请选择删除的商品',
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            xcxerror($validator->errors()->first());
        }

        $input['ids'] = json_decode($input['ids'],true);
        if (!is_array($input['ids'])){
            xcxerror('服务器偷懒去了……');
        }
        $mid = $request->input('mid');
        foreach ($input['ids'] as $val){
            $cartService->init('mid',$mid)->where(['id'=>$val])->delete($val,false);
        }
        xcxsuccess();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date20170823${DATE}
     * @desc
     * @param Request $request
     * @param CartService $cartService
     * @param ProductService $productService
     */
    public function edit(Request $request,CartService $cartService,ProductService $productService)
    {
        $input = $request->input();
        $mid = $request->input('mid');
        $rule = Array(
            'id'               => 'required',
            'num'              => 'required'
        );
        $message = Array(
            'id.required'            => '请选择商品',
            'num.required'           => '数量不能为空'
        );
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            xcxerror($validator->errors()->first());
        }
        $cartData = $cartService->init('mid',$mid)->getInfo($input['id']);
        if (!$cartData){
            xcxerror();
        }
        $productData = $productService->getDetail($cartData['product_id']);
        if (!$productData){
            xcxerror();
        }
        //查看数量是否超过最大购买数量
        if ($productData['quota'] != 0 && $productData['quota']<$input['num']){
            xcxerror('超过最大购买数量');
        }
        $res = $cartService->init('mid',$mid)->where(['id'=>$input['id']])->update(['num'=>$input['num']],false);
        xcxsuccess();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170118
     * @desc 保存留言
     */
    public function saveRemark(Request $request)
    {
        $input = $request->input();
        $noteList = (new ProductMsgService())->getListByProduct($input['pid']);
        if (!$noteList){
            xcxerror('该商品不需要留言');
        }
        $remarkService = new RemarkService();
        $remarkNo = (new ProductModule())->getIdentifier();
        foreach ($noteList as $key=>$val){
            $content = $input['name_'.$val['id']]??'';
            if ($val['required'] && !$content){
                xcxerror('请填写'.$val['title']);
            }
            $temp = [
                'remark_no' => $remarkNo,
                'title'     => $val['title'],
                'type'      => $val['type'],
                'content'   => $content,
            ];
            $remarkService->add($temp);
        }
        xcxsuccess('操作成功',$remarkNo);
    }

    /**
     *  满减活动计算价格
     * @param Request $request
     * @author 张永辉 2018年8月27日
     */
    public function discount(Request $request,DiscountModule $discountModule)
    {
        $data = $request->input('data');
        $data = json_decode($data,true);
        foreach ($data as $key=>&$val){
            $val['price'] = $val['price']*$val['num'];
        }
        $res = $discountModule->getDiscountByPids($data,$request->input('wid'));
        xcxsuccess('操作成功',$res);
    }



}