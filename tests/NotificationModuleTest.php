<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Module\NotificationModule;
use App\Model\NotificationSubscribe;
use App\Model\Notification;

class NotificationModuleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

	public function testCheckIfSubscribed()
	{
		$result = (new NotificationModule())->checkIfSubscribed(1, 42, 1);
		$this->assertContains($result, [true, false]);
	}

	public function testSubscribeRefundRequestTopic()
	{
		$result = (new NotificationModule())->subscribeRefundRequestTopic(1);
		$this->assertInstanceOf(NotificationSubscribe::class, $result);
	}

	public function testSubscribeNewPaidOrderTopic()
	{
		$result = (new NotificationModule())->subscribeNewPaidOrderTopic(1);
		$this->assertInstanceOf(NotificationSubscribe::class, $result);
	}

	public function testSubscribeReturnGoodsTopic()
	{
		$result = (new NotificationModule())->subscribeReturnGoodsTopic(1);
		$this->assertInstanceOf(NotificationSubscribe::class, $result);
	}

	public function testSubscribeRefundRequestExpirationTopic()
	{
		$result = (new NotificationModule())->subscribeRefundRequestExpirationTopic(1);
		$this->assertInstanceOf(NotificationSubscribe::class, $result);
	}

	public function testUnsubscribeTopic()
	{
		$result = (new NotificationModule())->unsubscribeTopic(1, 1, 1);
		$this->assertTrue($result);
	}

	public function testPublishRefundRequestNotification()
	{
		$result = (new NotificationModule())->publishRefundRequestNotification(1);
		$this->assertInstanceOf(Notification::class, $result);
	}

	public function testpublishNewPaidOrderNotification()
	{
		$result = (new NotificationModule())->publishNewPaidOrderNotification(1);
		$this->assertInstanceOf(Notification::class, $result);
	}

	public function testPublishReturnGoodsNotification()
	{
		$result = (new NotificationModule())->publishReturnGoodsNotification(1);
		$this->assertInstanceOf(Notification::class, $result);
	}

	public function testpublishRefundRequestExpriationNotification()
	{
		$result = (new NotificationModule())->publishRefundRequestExpriationNotification(1);
		$this->assertInstanceOf(Notification::class, $result);
	}

	public function testGetNotificationList()
	{
		$result = (new NotificationModule())->getNotificationList([]);
		$this->assertInternalType('array', $result);
	}

	public function testGetPureNotificationList()
	{
		$result = (new NotificationModule())->getPureNotificationList([]);
		$this->assertInternalType('array', $result);
	}

	public function testGetRightNavNotificationListByWid()
	{
		$result = (new NotificationModule())->getRightNavNotificationListByWid(1);
		$this->assertInternalType('array', $result);
	}

	public function testGetNotificationCount()
	{
		$result = (new NotificationModule())->getNotificationCount([]);
		$this->assertInternalType('integer', $result);
	}

	public function testGetNotificationSubscribeList()
	{
		$result = (new NotificationModule())->getNotificationSubscribeList([]);
		$this->assertInternalType('array', $result);
	}

	public function testGetNotificationDetail()
	{
		$result = (new NotificationModule())->getNotificationDetail(1);
		$this->assertInternalType('array', $result);
	}

	public function testSetReadNotification()
	{
		$result = (new NotificationModule())->setReadNotification(11);
		$this->assertInstanceOf(Notification::class, $result);
	}

	public function testDeleteNotification()
	{
		$result = (new NotificationModule())->deleteNotification(11);
		$this->assertTrue($result);
	}
}
