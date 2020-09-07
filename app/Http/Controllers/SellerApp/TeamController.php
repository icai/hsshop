<?php

/**
 * Created by phpstorm.
 * User: hsz
 * Date: 2018/3/16
 * Time: 2:10
 */

namespace App\Http\Controllers\SellerApp;

use App\Http\Controllers\Controller;
use App\Jobs\SendJPushMsg;
use App\Module\JPushModule;
use App\S\JPushService;
use App\Services\WeixinService;
use App\Services\Permission\WeixinUserService;
use App\Module\StoreModule;
use App\Module\OrderModule;
use Illuminate\Http\Request;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\DB;
use Validator;
use App\S\Weixin\ShopService;



class TeamController extends Controller
{
    /**
     * @auth hsz
     * @date 2018/3/16 14:46
     * @desc 店铺列表
     * @param Request $request
     */
    public function index(Request $request){
        $tokenData = $request->input('_tokenData');
        $parameter = $request->input('parameter');
        $weixinUserService = new WeixinUserService();
        $request->offsetSet('page',$parameter['page']??1);
        $weixinUserData = $weixinUserService->init()->with([])
            ->where(['uid'=>$tokenData['userInfo']['id']])
            ->select(['wid'])
            ->getList(false)[0]['data'];
        $weixinService = new WeixinService();
        list($list) = $weixinService->init()->with(['weixinConfigSub']) //关联关系：微信是否开通
            ->where(['id'=>['in', array_unique(array_column($weixinUserData, 'wid'))]])
            ->select(['id', 'shop_name', 'logo'])
            ->order('created_at DESC')
            ->getList();
        if($list['data']){
            (new StoreModule())->dealListShop($list['data']);
        }
        appsuccess('店铺列表获取成功', [
            'is_last' => $list['current_page'] < $list['last_page'] ? 0 : 1,
            'shop_list' => $list['data']
        ]);
    }

    /**
     * @desc 推送列表
     * @param Request $request
     * @param JPushService $jpushService
     */
    public function jpushList(Request $request, JPushService $jpushService){
        $tokenData = $request->input('_tokenData');
        $input = $request->input('parameter');
        if (!isset($tokenData['wid'])) {
            apperror('无权限访问');
        }
        $wid = $tokenData['wid'];
        $orderBy = 'id';
        $order = 'DESC';
        $relation_type = isset($input['relation_type']) ? $input['relation_type'] : 0;
        if (!in_array($relation_type, [0,1,2])) {
            apperror('类型参数非法');
        }
        list($list) = $jpushService->getlistPage(['wid'=>$wid], $orderBy, $order);
        $jpush_type = config('sellerapp.jpush_type');
        $jpush_pic = config('sellerapp.jpush_pic');
        foreach ($list['data'] as $key => &$val) {
            $val['title'] = $jpush_type[$val['type']];
            if ($val['type'] == 0) {
                $val['img'] = imgUrl().$jpush_pic['xiaotongzhi'];
            } elseif (in_array($val['type'], [1,2,3,4])) {
                $val['img'] = imgUrl().$jpush_pic['xiaodingdan'];
            }
        }
        appsuccess('推送列表获取成功', ['jpush_list'=>$list['data']]);
    }

    /**
     * @desc 标记消息为已读
     * @param Request $request
     * @param JPushService $jpushService
     */
    public function markJpushRead(Request $request, JPushService $jpushService){
        $tokenData = $request->input('_tokenData');
        if (!isset($tokenData['wid'])) {
            apperror('无权限访问');
        }
        $wid = $tokenData['wid'];
        $input = $request->input('parameter');
        $rules = array(
            'type' => 'required|integer',
        );
        $messages = array(
            'type.required' => '推送类型不能为空',
            'type.integer' => '推送类型必须是整数',
        );
        if ($input['type'] == 1) { //单个修改为已读
            $rules['push_id'] = 'required|integer';
            $messages['push_id.required'] = '推送id不能为空';
            $messages['push_id.integer'] = '推送id必须是整数';
        }
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $relation_type = 0;
        if (!in_array($relation_type, [0,1,2])) {
            apperror('类型参数非法');
        }
        if ($input['type'] == 1) {//单个
            $count = $jpushService->model->where(['wid'=>$wid, 'id'=>$input['push_id']])->count();
            if (!$count) {
                apperror('推送id不存在');
            }
            $jpushService->update($input['push_id'], ['is_read'=>1]);
        } elseif ($input['type'] == 2) {//批量
            $ids = $jpushService->model->where(['wid'=>$wid, 'is_read'=>0])
                ->where( function ($query) use ($relation_type) {

                })
                ->pluck('id')->toArray();
            foreach ($ids as $v) {
                $jpushService->update($v, ['is_read'=>1]);
            }
        }
        appsuccess('标记已读成功');
    }

    public function delPushMsg(Request $request, JPushService $jpushService){
        $tokenData = $request->input('_tokenData');
        if (!isset($tokenData['wid'])) {
            apperror('无权限访问');
        }
        $wid = $tokenData['wid'];
        $input = $request->input('parameter');
        $rules = array(
            'type' => 'required|integer',
        );
        $messages = array(
            'type.required' => '推送类型不能为空',
            'type.integer' => '推送类型必须是整数',
        );
        if ($input['type'] == 1) { //单个删除
            $rules['push_id'] = 'required|integer';
            $messages['push_id.required'] = '推送id不能为空';
            $messages['push_id.integer'] = '推送id必须是整数';
        }
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            apperror($validator->errors()->first());
        }
        $relation_type = 0;
        if (!in_array($relation_type, [0,1,2])) {
            apperror('类型参数非法');
        }
        if ($input['type'] == 1) {//单个
            $count = $jpushService->model->where(['wid'=>$wid, 'id'=>$input['push_id']])->count();
            if (!$count) {
                apperror('推送id不存在');
            }
            $jpushService->del($input['push_id']);
        } elseif ($input['type'] == 2) {//批量
            $ids = $jpushService->model->where(['wid'=>$wid, 'deleted_at'=>['<>', null]])
                ->where( function ($query) use ($relation_type) {

                })
                ->pluck('id')->toArray();
            foreach ($ids as $v) {
                $jpushService->del($v);
            }
        }
        appsuccess('删除成功');
    }

    public function test(Request $request){

    }


}

