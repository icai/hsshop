<?php
/**
 * 消息
 *
 * @author mfd
 */

namespace App\Http\Controllers\Merchants;

use App\S\NotificationService;
use App\S\Wechat\WeChatShopConfService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Module\NotificationModule;
use Validator;

class NotificationController extends Controller
{

	public function notificationList(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'is_read' => 'numeric',
			'notification_type' => 'numeric',
		], [
			'is_read.numeric' => 'is_read必须是数字',
			'notification_type.numeric' => 'notification_type必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$wheres['recv_id'] = $wid;
		$wheres['recv_id_type'] = 1;
		$request->has('is_read') && $wheres['is_read'] = $inputs['is_read'];
		$request->has('notification_type') && $wheres['notification_type'] = $inputs['notification_type'];
		$page = $request->input('page', 1);
		$notificationList = (new NotificationModule())->getNotificationList($wheres, $page); 
		success('', '', ['notificationList' => $notificationList]);
	}

	public function notificationCount(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'is_read' => 'numeric',
			'notification_type' => 'numeric',
		], [
			'is_read' => 'is_read必须是数字',
			'notification_type' => 'notification_type必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$wheres['recv_id'] = $wid;
		$wheres['recv_id_type'] = 1;
		$request->has('is_read') && $wheres['is_read'] = $inputs['is_read'];
		$request->has('notification_type') && $wheres['notification_type'] = $inputs['notification_type'];
		$notificationCount = (new NotificationModule())->getNotificationCount($wheres);
		success('', '', ['notificationCount' => $notificationCount]);
	}

	public function notificationDetail(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'notification_id' => 'required|numeric',
		], [
			'notification_id.required' => 'notification_id不能为空',
			'notification_id.numeric' => 'notification_id必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$notificationDetail = (new NotificationModule())->setReadNotification($inputs['notification_id']);
		success('', '', ['notificationDetail' => $notificationDetail]);
	}

	public function getRightNavNotificationList(Request $request)
	{
		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$notificationList = (new NotificationModule())->getRightNavNotificationListByWid($wid);
		success('', '', ['notificationList' => $notificationList]);
	}

	public function deleteNotification(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'notification_id' => 'required|numeric',
		], [
			'notification_id.required' => 'notification_id不能为空',
			'notification_id.numeric' => 'notification_id必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$result = (new NotificationModule())->deleteNotification($inputs['notification_id']);
		if ($result) {
			success('删除成功');
		}
		error('删除失败');
	}

    /**
     * 消息提醒页面渲染
     * @param Request $request
     * @return json
     * @author hsz
     * @since 2018/6/25
     */
    public function settingViewList(Request $request)
    {
        $inputs = $request->all();
        $validator = Validator::make($inputs, [
            'need_count' => 'boolean',
        ], [
            'need_count.boolean' => 'need_count必须是布尔',
        ]);
        if ($validator->fails()) {
            error($validator->messages()->first());
        }
        $wid = session('wid');
        if (!$wid) {
            error('店铺信息有误');
        }
        $wheres['subscriber_id'] = $wid;
        $subscribeList = (new NotificationModule())->getNotificationSubscribeList($wheres);
        success('', '', ['subscribeList' => $subscribeList]);
    }

	public function settingList(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'need_count' => 'boolean',
		], [
			'need_count.boolean' => 'need_count必须是布尔',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$wheres['subscriber_id'] = $wid;
		//筛选后台消息通知为1,2
		$wheres['subscriber_id_type'] = ['in', [1,2]];
		$needCount = ($request->has('need_count') && $inputs['need_count']) ? true : false;
		$subscribeList = (new NotificationModule())->getNotificationSubscribeList($wheres, $needCount);
		success('', '', ['subscribeList' => $subscribeList]);
	}

	public function settingDetail(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'notification_type' => 'required|numeric',
		], [
			'notification_type.required' => 'notification_type不能为空',
			'notification_type.numeric' => 'notification_type必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		$isSubscribed = (new NotificationModule())->checkIfSubscribed($inputs['notification_type'], $wid);
		success('', '', ['isSubscribed' => $isSubscribed]);
	}

	public function notificationListView(Request $request)
	{
        return view('merchants.marketing.notification.notificationList', [
            'title' => '消息列表',
            'leftNav' => 'marketing',
            'slidebar' => 'index',
        ]);
	}

	public function notificationDetailView(Request $request)
	{
        return view('merchants.marketing.notification.notificationDetail', [
            'title' => '消息详情',
            'leftNav' => 'marketing',
            'slidebar' => 'index',
        ]);
	}

	public function settingListView(Request $request)
	{
	    //判断是否绑定了微信公众号
        $conf = (new WeChatShopConfService())->getRowByWid(session('wid'));
        return view('merchants.marketing.notification.settingList', [
            'title' => '设置列表',
            'leftNav' => 'marketing',
            'slidebar' => 'index',
            'conf'     => $conf
        ]);
	}

    /**
     * 消息提醒设置详情页
     * @author hsz
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 梅杰2018年10月22日 api 弃用
     */
	public function settingDetailView(Request $request)
	{
        $inputs = $request->all();
        $wid = session('wid');
        if (!$wid) {
            error('店铺信息有误');
        }
        if (isset($inputs['id']) && $inputs['id']) { //修改消息提醒设置
            $validator = Validator::make($inputs, [
                'subscriber_id_type' => 'numeric'
            ], [
                'subscriber_id_type.numeric' => 'subscriber_id_type必须是数字'
            ]);
            if ($validator->fails()) {
                error($validator->messages()->first());
            }
            if ($inputs['subscriber_id_type'] == -1) {
                $result = (new NotificationService())->deleteNotificationSubscribeByPrimaryKeys($inputs['id']);
            } else {
                $result = (new NotificationService())->updateNotificactionByPrimaryKey('subscribe', $inputs['id'], ['subscriber_id_type' => $inputs['subscriber_id_type']]);
            }
            if ($result) {
                success('保存成功');
            }
            error('保存失败');
        } else { //获取消息提醒设置
            $validator = Validator::make($inputs, [
                'notification_type' => 'numeric'
            ], [
                'notification_type.numeric' => 'notification_type必须是数字'
            ]);
            if ($validator->fails()) {
                error($validator->messages()->first());
            }
            $where = [
                'subscriber_id' => $wid,
                'notification_type' => $inputs['notification_type']
            ];
            $notification = (new NotificationModule())->setNotificationSubscribeDetail($where);
            //判断是否绑定了微信公众号
            $conf = (new WeChatShopConfService())->getRowByWid($wid);
            return view('merchants.marketing.notification.settingDetail', [
                'title'        => '设置详情',
                'leftNav'      => 'marketing',
                'slidebar'     => 'index',
                'conf'         => $conf,
                'notification' => $notification[0],
            ]);
        }
	}

	public function notificationSubscribe(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'notification_type' => 'required|numeric',
		], [
			'notification_type.required' => 'notification_type不能为空',
			'notification_type.numeric' => 'notification_type必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}
		switch ($inputs['notification_type']) {
			case 1:
				$result = (new NotificationModule())->subscribeRefundRequestTopic($wid);
				break;
			case 2:
				$result = (new NotificationModule())->subscribeNewPaidOrderTopic($wid);
				break;
			case 3:
				$result = (new NotificationModule())->subscribeReturnGoodsTopic($wid);
				break;
			case 4:
				$result = (new NotificationModule())->subscribeRefundRequestExpirationTopic($wid);
				break;
            case 5:
                $result = (new NotificationModule())->subscribeDeliverGoodsTopic($wid);
                break;
            case 6:
                $result = (new NotificationModule())->subscribePaySuccessTopic($wid);
                break;
		}
		if ($result) {
			success('订阅成功');
		}
		error('订阅失败');
	}

	public function notificationUnsubscribe(Request $request)
	{
		$inputs = $request->all();
		$validator = Validator::make($inputs, [
			'notification_type' => 'required|numeric',
		], [
			'notification_type.required' => 'notification_type不能为空',
			'notification_type.numeric' => 'notification_type必须是数字',
		]);
		if ($validator->fails()) {
            error($validator->messages()->first());
		}

		$wid = session('wid');
		if (!$wid) {
			error('店铺信息有误');
		}

		$result = (new NotificationModule())->unsubscribeTopic($inputs['notification_type'], $wid);
		if ($result) {
			success('取消订阅成功');
		}
		error('取消订阅失败');
	}

    public function readAllNotification()
    {
        $wid = session('wid');
        if (!$wid) {
            error('店铺信息有误');
        }
        $result = (new NotificationModule())->allReadNotification($wid);
        if ($result) {
            success('全部已读设置成功');
        }
        error();
    }

}
