<?php

namespace App\Services;

use App\Model\OrderRefundMessage;

class OrderRefundMessageService extends Service
{
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {
        $this->initialize(new OrderRefundMessage(), $uniqueKey, $uniqueValue, $idKey);

        return $this;
    }

    /**
     * 新增一条协商记录
     */
    public function addMessage($data)
    {
        return $this->init('wid', $data['wid'])->add($data, false);
    }

    public function getStatusString($status)
    {
        $statusStr = '';
        switch ($status) {
            case 0:
                $statusStr = '普通留言';
                break;
            case 1:
                $statusStr = '拒绝退款';
                break;
            case 2:
                $statusStr = '同意退款';
                break;
            case 3:
                $statusStr = '买家取消退款';
                break;
            case 4:
                $statusStr = '买家修改退款申请';
                break;
            case 5:
                $statusStr = '卖家同意退货';
                break;
            case 6:
                $statusStr = '买家退货';
                break;
            case 7:
                $statusStr = '退款完成';
                break;
            case 8:
                $statusStr = '卖家处理逾期自动同意退款';
                break;
            case 9:
                $statusStr = '买家发货逾期退款失败';
                break;
            case 10:
                $statusStr = '卖家拒绝后买家处理逾期退款失败';
                break;
        }
        return $statusStr;
    }


}