<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/23  10:49
 * DESC
 */

namespace App\Http\Controllers\shop;

use App\Http\Controllers\Controller;
use App\Module\DiscountModule;
use App\Module\ProductModule;
use App\S\Groups\GroupsDetailService;
use App\S\Groups\GroupsService;
use App\Module\GroupsRuleModule;
use App\Module\AddCartModule;
use App\S\Product\ProductMsgService;
use App\S\Product\RemarkService;
use App\Services\ProductPropService;
use App\S\Product\ProductService;
use App\Services\Shop\CartService;
use Illuminate\Http\Request;
use Validator;
use WeixinService;
use App\S\PublicShareService;

class CartController extends Controller
{
    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703231055
     * @desc 获取购物车信息
     * @param Request $request
     * @param TrolleyService $trolleyService
     */
    public function index(Request $request,CartService $cartService)
    {
        $input = $request->input();
        $memberId = $request->session()->get('mid');
        list($cartData) = $cartService->getCart($memberId,session('wid'));
        if (!isset($input['page']) || $input['page'] ==1)
        {
			//添加通用分享设
            $shareData = (new PublicShareService())->publicShareSet(session('wid'));
            $invalid = [];
            foreach ($cartData['data'] as $key=>$val)
            {
                if ($val['flag'] != 1){
                    $invalid[] = $val;
                    unset($cartData['data'][$key]);
                }
            }
            return view('shop.cart.index', [
                'title' => '购物车',
                'cartData' =>$cartData,
                'invalid' => $invalid,
                'shareData' => $shareData
            ]);
        }else{
            $cartData = $cartData['data'];
            success('','',$cartData);
        }

    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703241416
     * @desc 删除购物车
     * @param Request $request
     * @param TrolleyService $trolleyService
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
            error($validator->errors()->first());
        }

        if (!is_array($input['ids'])){
            error('服务器偷懒去了……');
        }

        foreach ($input['ids'] as $val){
            $res = $cartService->init('mid',session('mid'))->where(['id'=>$val])->delete($val,false);
            if (!$res){
                error();
            }
        }
        success();

    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703241513
     * @desc 加入购物车
     * @param Request $request
     * @param TrolleyService $trolleyService
     */
    public function add(Request $request)
    {
        $mid = session('mid');
        $wid = session('wid');
        
        $input = $request->input();
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
            error($validator->errors()->first());

        $res = (new AddCartModule())->addCart($mid, $wid);
        if ($res['success'] != 1)
            error($res['message']);

        success('添加购物车成功','',$res['data']);
    }


    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703241740
     * @desc 修改购物车目前仅修改数量
     * @param Request $request
     * @param TrolleyService $trolleyService
     */
    public function edit(Request $request,CartService $cartService,ProductService $productService)
    {
        $input = $request->input();
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
            error($validator->errors()->first());
        }
        $cartData = $cartService->init('mid',session('mid'))->getInfo($input['id']);
        if (!$cartData){
            error();
        }
        $productData = $productService->getDetail($cartData['product_id']);
        if (!$productData){
            error();
        }
        //查看数量是否超过最大购买数量
        if ($productData['quota'] != 0 && $productData['quota']<$input['num']){
            error('超过最大购买数量');
        }

        //查看是否小于最低购买数量
        //规格重构 没有最小购买限制字段
        $cartService->init('mid',session('mid'))->where(['id'=>$input['id']])->update(['num'=>$input['num']]);
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170613
     * @desc 获取购物车数量
     * @param CartService $cartService
     * @param $wid
     */
    public function getNumber(CartService $cartService,$wid)
    {
        $num = $cartService->cartNum(session('mid'),session('wid'));
        success('操作成功','',$num);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param Request $request
     */
    public function saveRemark(Request $request)
    {
        $input = $request->input();
        $noteList = (new ProductMsgService())->getListByProduct($input['pid']);
        if (!$noteList){
            error('该商品不需要留言');
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
        $res = $discountModule->getDiscountByPids($data,session('wid'));
        success('操作成功','',$res);
    }



}











