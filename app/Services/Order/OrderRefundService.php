<?php

namespace App\Services\Order;

use App\Model\OrderRefund;
use App\Services\Service;
use OrderService as OService;
use OrderLogService;

class OrderRefundService extends Service
{
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new OrderRefund(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    public function getStatusString($status)
    {
        $statusStr = '';
        switch ($status) {
            case 1:
                $statusStr = '买家发起维权';
                break;
            case 2:
                $statusStr = '商家拒绝维权';
                break;
            case 3:
                $statusStr = '维权处理中';
                break;
            case 4:
                $statusStr = '维权结束';
                break;
            case 5:
                $statusStr = '买家取消退款';
                break;
            case 6:
                $statusStr = '商家同意退货';
                break;
            case 7:
                $statusStr = '买家退款发货';
                break;
            case 8:
                $statusStr = '退款完成并到账';
                break;
            case 9:
                $statusStr = '退款申请关闭';
                break;
        }
        return $statusStr;
    }

    public function getReasonString($reason)
    {
        $reasonStr = '';
        switch ($reason) {
            case 0:
                $reasonStr = '其他';
                break;
            case 1:
                $reasonStr = '配送信息错误';
                break;
            case 2:
                $reasonStr = '买错商品';
                break;
            case 3:
                $reasonStr = '不想买了';
                break;
            case 4:
                $reasonStr = '未按承诺时间发货';
                break;
            case 5:
                $reasonStr = '快递无跟踪记录';
                break;
            case 6:
                $reasonStr = '空包裹';
                break;
            case 7:
                $reasonStr = '快递一直未送达';
                break;
            case 8:
                $reasonStr = '缺货';
                break;
        }
        return $reasonStr;
    }

}