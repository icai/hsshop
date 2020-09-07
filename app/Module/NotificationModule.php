<?php
/**
 * @author mafanding
 */
namespace App\Module;

use App\S\NotificationService;
use App\Lib\Redis\NotificationRedis;
use App\S\Order\OrderService;
use App\Services\WeixinService;
use Illuminate\Support\Facades\DB;
use Redisx;
use App\Services\Order\OrderDetailService;
use App\S\Weixin\ShopService;

class NotificationModule
{

	/**
	 * @var App\S\NotificationService
	 */
	protected $notificationService;

	public function __construct()
	{
		$this->notificationService = new NotificationService();
	}

	/**
	 * 检测是否订阅了制定类型的通知
	 *
	 * @param int $notificationType
	 * @param int $subscribeId
	 * @param int $subscribeIdType
	 * @return bool
	 */
	public function checkIfSubscribed($notificationType, $subscriberId, $subscriberIdType = 1)
	{
		return $this->notificationService->checkIfSubscribed($notificationType, $subscriberId, $subscriberIdType);
	}

    /**
     * 订阅退款主题
     *
     * @param int $subscribeId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 何书哲 2018年7月22日 买家发起退款默认粉丝和后台都开启
     * @update 梅杰 2018年10月22日 买家发起退款默认后台开启
     */
    public function subscribeRefundRequestTopic($subscriberId)
    {
        $createData['subscriber_id'] = $subscriberId;
        $createData['subscriber_id_type'] = 1;
        $createData['notification_type'] = 1;
        return $this->notificationService->createNotificationSubscribeModel($createData);
    }

    /**
     * 订阅新付款订单主题
     *
     * @param int $subscribeId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 何书哲 2018年7月22日 新订单提醒默认粉丝和后台都开启
     * @update 梅杰 2018年10月22日 买家发起退款默认后台开启
     */
    public function subscribeNewPaidOrderTopic($subscriberId)
    {
        $createData['subscriber_id'] = $subscriberId;
        $createData['subscriber_id_type'] = 1;
        $createData['notification_type'] = 2;
        return $this->notificationService->createNotificationSubscribeModel($createData);
    }

    /**
     * 订阅已退货主题
     *
     * @param int $subscribeId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 何书哲 2018年7月22日 买家已退货提醒默认粉丝和后台都开启
     * @update 梅杰 2018年10月22日 买家发起退款默认后台开启
     */
    public function subscribeReturnGoodsTopic($subscriberId)
    {
        $createData['subscriber_id'] = $subscriberId;
        $createData['subscriber_id_type'] = 1;
        $createData['notification_type'] = 3;
        return $this->notificationService->createNotificationSubscribeModel($createData);
    }

    /**
     * 订阅退款请求临期主题
     *
     * @param int $subscribeId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 何书哲 2018年7月22日 退款临近超时提醒默认粉丝和后台都开启
     * @update 梅杰 2018年10月22日 买家发起退款默认后台开启
     */
    public function subscribeRefundRequestExpirationTopic($subscriberId)
    {
        $createData['subscriber_id'] = $subscriberId;
        $createData['subscriber_id_type'] = 1;
        $createData['notification_type'] = 4;
        return $this->notificationService->createNotificationSubscribeModel($createData);
    }

    /**
     * 订阅发货提醒
     * @param $subscriberId
     * @return static
     */
	public function subscribeDeliverGoodsTopic($subscriberId)
    {
        $createData['subscriber_id'] = $subscriberId;
        $createData['subscriber_id_type'] = 0;// 发货提醒只有粉丝 由1->0 hsz
        $createData['notification_type'] = 5;
        return $this->notificationService->createNotificationSubscribeModel($createData);
    }

    /**
     * 买家支付成功提醒
     * @param $subscriberId
     * @return static
     */
    public function subscribePaySuccessTopic($subscriberId)
    {
        $createData['subscriber_id'] = $subscriberId;
        $createData['subscriber_id_type'] = 0;//支付成功只有粉丝 由1->0 hsz
        $createData['notification_type'] = 6;
        return $this->notificationService->createNotificationSubscribeModel($createData);
    }

	/**
	 * 取消订阅指定主题
	 *
	 * @param int $topicId
	 * @return bool
	 */
	public function unsubscribeTopic($notificationType, $subscriberId)
	{
		$wheres['subscriber_id'] = $subscriberId;
		//注释掉subscriberIdType=1条件 hsz
		//$wheres['subscriber_id_type'] = $subscriberIdType;
		$wheres['notification_type'] = $notificationType;
		$ids = $this->notificationService->getCollectionByConditions('subscribe', ['id'], $wheres)->toArray();
		if ($this->notificationService->deleteNotificationSubscribeByConditions($wheres)) {
			$notificationRedis = new notificationRedis($this->notificationService->getModel()->getRedisKey());
			$notificationRedis->deleteArr(array_column($ids, 'id'));
			return true;
		}
		return false;
	}

	/**
	 * 推送退款请求消息
	 *
	 * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年10月22日 买家发起付款修改发送条件
	 */
	public function publishRefundRequestNotification($orderId)
	{
		$orderService = new OrderService();
		$orderInfo = $orderService->find($orderId);
		if (!is_null($orderInfo)) {
			$mid = $orderInfo->mid;
			$wid = $orderInfo->wid;
			//1=>买家发起退款 发送给商家 更改推送消息发送条件 subscriberIdType=1
			if ($this->checkIfSubscribed(1, $wid, 1)) {
				//$weixinService = new WeixinService();
				//$storeInfo = $weixinService->getStoreInfo($wid);
				$shopService = new ShopService();
				$storeInfo = $shopService->getRowById($wid);
				if (isset($storeInfo['shop_name'])) {
					$createData['notification_type'] = 1;
					$createData['notification_content'] = sprintf("您的买家发起退款，订单编号<%s>，请您在<%s>天内处理，过时系统将自动退款。请尽快登录<%s>商家后台操作。", $orderInfo->oid, 10, $storeInfo['shop_name']);
					$createData['relate_order_id'] = $orderId;
					$createData['send_id'] = $mid;
					$createData['send_id_type'] = 0;
					$createData['recv_id'] = $wid;
					$createData['recv_id_type'] = 1;
					$model = $this->notificationService->createNotificationModel($createData);
					if (!is_null($model)) {
						$notificationRedis = new notificationRedis($this->notificationService->getModel()->getRedisKey());
						$notificationRedis->publishNotification($wid, $model->toArray());
					}
					return $model;
				}
			}
		}
		return null;
	}

	/**
	 * 推送新付款订单消息
	 *
	 * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年10月22日 新订单提醒更改推送消息发送条件
	 */
	public function publishNewPaidOrderNotification($orderId)
	{
		$orderService = new OrderService();
		$orderInfo = $orderService->find($orderId);
		if (!is_null($orderInfo)) {
			$mid = $orderInfo->mid;
			$wid = $orderInfo->wid;
            //update 何书哲 2018年10月22日 新订单提醒 发送给商家 更改推送消息发送条件 subscriberIdType=1
			if ($this->checkIfSubscribed(2, $wid, 1)) {
				//$weixinService = new WeixinService();
				//$storeInfo = $weixinService->getStoreInfo($wid);
				$shopService = new ShopService();
				$storeInfo = $shopService->getRowById($wid);
				if (isset($storeInfo['shop_name'])) {
					$createData['notification_type'] = 2;
					$createData['notification_content'] = sprintf("您有新的订单，订单编号<%s>，请尽快登录<%s>商家后台操作。", $orderInfo->oid, $storeInfo['shop_name']);
					$createData['relate_order_id'] = $orderId;
					$createData['send_id'] = $mid;
					$createData['send_id_type'] = 0;
					$createData['recv_id'] = $wid;
					$createData['recv_id_type'] = 1;
					$model = $this->notificationService->createNotificationModel($createData);
					if (!is_null($model)) {
						$notificationRedis = new notificationRedis($this->notificationService->getModel()->getRedisKey());
						$notificationRedis->publishNotification($wid, $model->toArray());
					}
					return $model;
				}
			}
		}
		return null;
	}

	/**
	 * 推送已退货消息
	 *
	 * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年10月22日 买家以退货提醒更改推送消息发送条件
     */
	public function publishReturnGoodsNotification($orderId)
	{
		$orderService = new OrderService();
		$orderInfo = $orderService->find($orderId);
		if (!is_null($orderInfo)) {
			$mid = $orderInfo->mid;
			$wid = $orderInfo->wid;
            //update 何书哲 2018年10月22日 买家以退货提醒更改推送消息发送条件 subscriberIdType=1
			if ($this->checkIfSubscribed(3, $wid, 1)) {
				//$weixinService = new WeixinService();
				//$storeInfo = $weixinService->getStoreInfo($wid);
				$shopService = new ShopService();
				$storeInfo = $shopService->getRowById($wid);
				if (isset($storeInfo['shop_name'])) {
					$createData['notification_type'] = 3;
					$createData['notification_content'] = sprintf("您的买家已退货，订单编号<%s>，请您在七天内核实处理，逾期未处理，系统将操作退款买家。请尽快登录<%s>商家后台操作。", $orderInfo->oid, $storeInfo['shop_name']);
					$createData['relate_order_id'] = $orderId;
					$createData['send_id'] = $mid;
					$createData['send_id_type'] = 0;
					$createData['recv_id'] = $wid;
					$createData['recv_id_type'] = 1;
					$model = $this->notificationService->createNotificationModel($createData);
					if (!is_null($model)) {
						$notificationRedis = new notificationRedis($this->notificationService->getModel()->getRedisKey());
						$notificationRedis->publishNotification($wid, $model->toArray());
					}
					return $model;
				}
			}
		}
		return null;
	}

	/**
	 * 推送退款请求临期消息
	 *
	 * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Model|null
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 何书哲 2018年10月22日 退款临近超时提醒更改推送消息发送条件
	 */
	public function publishRefundRequestExpriationNotification($orderId)
	{
		$orderService = new OrderService();
		$orderInfo = $orderService->find($orderId);
		if (!is_null($orderInfo)) {
			$mid = $orderInfo->mid;
			$wid = $orderInfo->wid;
            //update 何书哲 2018年10月22日 退款临近超时提醒 发送给商家 更改推送消息发送条件 subscriberIdType=1
			if ($this->checkIfSubscribed(4, $wid, 1)) {
				//$weixinService = new WeixinService();
				//$storeInfo = $weixinService->getStoreInfo($wid);
				$shopService = new ShopService();
				$storeInfo = $shopService->getRowById($wid);
				if (isset($storeInfo['shop_name'])) {
					$createData['notification_type'] = 4;
					$createData['notification_content'] = sprintf("订单编号<%s>，因您长时间未处理此笔订单，系统将于72小时后退款给买家。请尽快登录<%s>商家后台操作。", $orderInfo->oid, $storeInfo['shop_name']);
					$createData['relate_order_id'] = $orderId;
					$createData['send_id'] = $mid;
					$createData['send_id_type'] = 0;
					$createData['recv_id'] = $wid;
					$createData['recv_id_type'] = 1;
					$model = $this->notificationService->createNotificationModel($createData);
					if (!is_null($model)) {
						$notificationRedis = new notificationRedis($this->notificationService->getModel()->getRedisKey());
						$notificationRedis->publishNotification($wid, $model->toArray());
					}
					return $model;
				}
			}
		}
		return null;
	}

	/**
	 * 获取消息列表
	 *
	 * @param array $wheres
	 * @param int $page
	 * @param int $size
	 * @return array
	 */
	public function getNotificationList($wheres = [], $page = 1, $size = 15)
	{
		$returnData = $this->notificationService->getCollectionByConditionsWithPage('', ['id'], $wheres, "id desc", $page, $size);
		$idArr = array_column($returnData['data']->toArray(), 'id');
		$returnData['data'] = $this->getListById('', $idArr);
		$topicItems = $this->notificationService->getTopicItems();
		foreach ($returnData['data'] as &$value) {
			$value['from_content'] = "来自 " . $topicItems[$value['notification_type']]['title'] . "消息提醒";
			$orderDetail = (new OrderDetailService())->first(['oid' => $value['relate_order_id']], ['img']);
			$value['order_img'] = stripos($orderDetail->img, 'http') === false ? imgUrl() . $orderDetail->img : $orderDetail->img;
			$orderInfo = (new OrderService())->find($value['relate_order_id']);
			//跳转链接报错修改 Herry 20171113
            //添加路由参数，区分消息通知已读未读 hsz 2018/6/25
			$value['redirect_url'] = !empty($orderInfo->is_hexiao) ? "/merchants/order/stateMentDetail/" . $value['relate_order_id'].'/'.$value['id'] : "/merchants/order/orderDetail/" . $value['relate_order_id'].'/'.$value['id'];
		}
		return $returnData;
	}

	/**
	 * 获取纯粹消息列表
	 *
	 * @param array $wheres
	 * @param int $page
	 * @param int $size
	 * @return array
	 */
	public function getPureNotificationList($wheres = [], $page = 1, $size = 3)
	{
		$returnData = $this->notificationService->getCollectionByConditionsWithPage('', ['id'], $wheres, "id desc", $page, $size);
		$idArr = array_column($returnData['data']->toArray(), 'id');
		$returnData['data'] = $this->getListById('', $idArr);
		return $returnData;
	}

	/**
	 * 获取右侧消息列表消息列表
	 *
	 * @param int $wid
	 * @return array
	 */
	public function getRightNavNotificationListByWid($wid)
	{
		$returnData = [];
		$topicItems = $this->notificationService->getTopicItems();
		foreach ($topicItems as $key => $value) {
		    //update MayJay20180625
		    if ($key == 6) {
		        continue ;
            }
			$where['recv_id'] = $wid;
			$where['recv_id_type'] = 1;
			$where['notification_type'] = $key;
			$where['is_read'] = 0;
			$notificationList = $this->getNotificationList($where, 1, 3);
			$returnData[] = ['title' => $value['title'] . "消息提醒", 'notificationList' => $notificationList];
		}
		return $returnData;
	}

	/**
	 * 获取消息数量
	 *
	 * @param array $wheres
	 * @return int
	 */
	public function getNotificationCount($wheres = [])
	{
		return $this->notificationService->getCountByConditions('', $wheres);
	}

	/**
	 * 获取指定条件的订阅列表
	 *
	 * @param array $wheres
	 * @return array
	 */
	public function getNotificationSubscribeList($wheres = [], $needCount = false)
	{
		$subscribeArr = $this->notificationService->getCollectionByConditions('subscribe', ['notification_type'], $wheres)->toArray();
		$types = array_column($subscribeArr, 'notification_type');
		$topicItems = $this->notificationService->getTopicItems();
		foreach ($topicItems as $key => &$topicItem) {
			if (in_array($key, $types)) {
				$topicItem['isSubscribed'] = true;
			}
			if ($needCount) {
				$where = [];
				$where['recv_id'] = $wheres['subscriber_id'];
				$where['recv_id_type'] = $wheres['subscriber_id_type'];
				$where['notification_type'] = $key;
				$where['is_read'] = 0;
				$topicItem['notificationCount'] = $this->getNotificationCount($where);
			}
		}
		return $topicItems;
	}

	/**
	 * 获取消息详情
	 *
	 * @param int $notificationId
	 * @return array
	 */
	public function getNotificationDetail($notificationId)
	{
		$redis = new notificationRedis($this->notificationService->getInstance('')->getRedisKey());
		$notificationInfo = $redis->getRow($notificationId);
		if (empty($notificationInfo)) {
			$notificationInfo = [];
			$notificationModel = $this->notificationService->getModelByPrimaryKey('', $notificationId);
			if (!is_null($notificationModel)) {
				$notificationInfo = $notificationModel->toArray();
			}
			if (!empty($notificationInfo)) {
				$redis->add($notificationInfo);
			}
		}
		$this->setReadNotification($notificationId);
		return $notificationInfo;
	}

	/**
	 * @param int $notificaitonId
     * @return \Illuminate\Database\Eloquent\Model|null
	 */
	public function setReadNotification($notificationId)
	{
		$redis = new notificationRedis($this->notificationService->getInstance('')->getRedisKey());
		$notificationInfo = $this->notificationService->getModelByPrimaryKey('', $notificationId);
		if ($notificationInfo) {
			$notificationInfo->is_read = 1;
			$notificationInfo->save();
			$redis->updateRow($notificationInfo->toArray());
		}
		return $notificationInfo;
	}

	/**
	 * @param int $notificaitonId
     * @return bool
	 */
	public function deleteNotification($notificationId)
	{
		$redis = new notificationRedis($this->notificationService->getInstance('')->getRedisKey());
		$result = $this->notificationService->deleteNotificationByPrimaryKeys($notificationId);
		if ($result) {
			$redis->delete($notificationId);
			return true;
		}
		return false;
	}

    protected function getListById($model, array $idArr)
    {
        $redisData = $mysqlData = [];
        $redisId = [];
        $idArr = array_values($idArr);
		$redis = new notificationRedis($this->notificationService->getInstance($model)->getRedisKey());
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key]))
                $redisId[] = $value;
            else
                $redisData[$value] = $result[$key];
        }
        if (!empty($redisId)) {
            $mysqlData = $this->notificationService->getCollectionByPrimaryKeys($model, $idArr)->toArray();
            $mysqlData = array_column($mysqlData, null,'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData) );
    }

    /**
     * 全部已读
     * Author: MeiJay
     * @param $wid
     * @param $type //指定消息类型
     * @return bool
     */
    public function allReadNotification($wid,$type = 0)
    {
        $where = [
            'recv_id' => $wid,
            'is_read' => 0,
        ];
        if ($type) {
            $where['notification_type'] = $type;
        }
        $noReadIds = $this->notificationService->getCollectionByConditions('', ['id'], $where)->toArray();
        $redis = new notificationRedis($this->notificationService->getInstance('')->getRedisKey());
        if($noReadIds){
            DB::beginTransaction();
            foreach ($noReadIds as $notificationId){
                $notificationInfo = $this->notificationService->getModelByPrimaryKey('', $notificationId['id']);
                if ($notificationInfo) {
                    $notificationInfo->is_read = 1;
                    if(!$notificationInfo->save()){
                        DB::rollback();
                        return false;
                    }
                    $redis->updateRow($notificationInfo->toArray());
                }
            }
            DB::commit();
        }
        return true;
    }

    /**
     * Author: MeiJay
     * @param $wid
     * @param int $page
     * @param int $size
     * @return array
     */
    public function getNewOrderNotification($wid,$page = 1, $size = 30)
    {
        $where = [
            'recv_id'           => $wid,
            'is_read'           => 0,
            'notification_type' => 2
        ];
        $returnData = $this->notificationService->getCollectionByConditionsWithPage('', ['id'], $where, "id desc", $page, $size);
        $idArr = array_column($returnData['data']->toArray(), 'id');
        $returnData['data'] = $this->getListById('', $idArr);
        $orderService = new OrderService();
        if ($returnData['data']) {
            foreach ($returnData['data'] as  &$v) {
                //获取每个订单的详情
                $orderInfo = $orderService->getOrderDetailByOid($v['relate_order_id'],['id','oid','cash_fee','created_at']);
                if($orderInfo) {
                    $v['oid']       = $orderInfo['oid'];
                    $v['created_at']   = $orderInfo['created_at'];
                    $v['order_pay'] = $orderInfo['cash_fee'];
                    $v['goods_num'] = count($orderInfo['orderDetail']);
                    $v['goods_name'] = $orderInfo['orderDetail'][0]['title'];
                    $v['goods_img']  = imgUrl($orderInfo['orderDetail'][0]['img']);
                }
            }
        }
        return $returnData;
    }

    /**
     * 设置消息提醒详情
     * @author hsz
     * @param array $wheres
     * @return mixed
     */
    public function setNotificationSubscribeDetail($wheres=[]) {
        $subscribeArr = $this->notificationService->getCollectionByConditions('subscribe', ['*'], $wheres)->toArray();
        return $subscribeArr;
    }

}
