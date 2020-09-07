<?php

namespace App\Jobs;

use App\Model\Groups;
use App\Model\GroupsRule;
use App\Model\Member;
use App\Model\Order;
use App\Model\Product;
use App\S\WXXCX\SubscribeMessagePushService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
/**
 * @desc 小程序订阅模板消息发送提醒
 * @author 吴晓平[wuxiaoping1559@dingtalk.com] 2019年12月05日 19:58:31
 */
class SubMsgPushJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $mid 发送模板类型
     */
    private $type;

    /**
     * @var $mid 店铺id
     */
    private $wid;

    /**
     * @var $data 模板发送的相关数据
     */
    private $data;

    /**
     * @var 对应跳转路径的参数
     */
    private $param;

    /**
     * 构造函数
     * @param $type 发送消息模板类型
     * @param $mid  店铺id
     * @param $data 发送消息模板的数据
     * @param $param 对应跳转路径的参数
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 21:00:55
     */
    public function __construct($type, $wid, $data, $param)
    {
        $this->type = $type;
        $this->wid = $wid;
        $this->data = $data;
        $this->param = $param;
        $this->queue = 'subscribe_message_push';
    }

    /**
     * @description：发送执行
     * @return bool
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月05日 21:00:55
     */
    public function handle()
    {
        // 参数异常，直接返回
        if (empty($this->data) ||
            empty($this->data['keys']) ||
            empty($this->data['page'])) {
            return false;
        }

        if ($this->attempts() > 3) {
            \Log::info(__FILE__ . '文件,队列报错次数超限');
            return true;
        }
        switch ($this->type) {
            // 会员卡开通提醒
            case 1:
                $this->openCardNotify();
                break;
            // 团购结果通知
            case 2:
                $this->groupOrderMsg();
                break;
            // 审核结果通知
            case 3:
                $this->applyForCash();
                break;
            // 预售结果通知
            case 4:
                $this->advanceResultNotify();
                break;
            // 积分变更通知
            case 5:
                $this->pointConsume();
                break;
            // 收益到账通知
            case 6:
                $this->incomeReqNotify();
                break;
            // 发货提醒通知
            case 7:
                $this->orderDeliveryNotify();
                break;
            default:
                break;
        }
        app(SubscribeMessagePushService::class)->messageSend($this->wid, $this->data);
        \Log::info('发送订阅模板消息队列完成');
        return true;
    }

    /**
     * @description：获取会员卡开通提醒的发送内容
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 15:56:41
     */
    public function openCardNotify()
    {
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid']);
        if (empty($member)) {
            return false;
        }
        $this->data['touser'] = $member->xcx_openid;
        // 定义未读留言发送的内容信息
        $content = [
            // 发送人名称限制10个字以内
            ['value' => str_limit($this->param['title'], 20, '')],
            ['value' => $this->param['number']],
            ['value' => Carbon::now()->toDateTimeString()],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }

    /**
     * @description：获取团购结果通知发送内容
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 16:09:37
     */
    public function groupOrderMsg()
    {
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid']);
        if (empty($member)) {
            return false;
        }
        // 设置用户的openid
        $this->data['touser'] = $member->xcx_openid;

        // 获取对应的拼团订单信息
        $order = Order::query()->find($this->param['oid'], ['id', 'products_price']);
        if (empty($order)) {
            return false;
        }

        // 如果传递的参数是拼团id,则获取对应的拼团标题，拼团人数
        if (!empty($this->param['groups_id'])) {
            $groups = Groups::query()->find($this->param['groups_id'], ['id', 'rule_id']);
            if (empty($groups)) {
                return false;
            }
            $groupsRule = GroupsRule::query()->find($groups->rule_id, ['id', 'title', 'groups_num']);
            if (empty($groupsRule)) {
                return false;
            }
            $this->param['title'] = $groupsRule->title;
            $this->param['groups_num'] = $groupsRule->groups_num;
        }

        // 定义发送内容
        $content = [
            ['value' => str_limit($this->param['title'], 20, '')],
            ['value' => $order->products_price],
            ['value' => $this->param['groups_num']],
            ['value' => $this->param['notice']],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }

    /**
     * @description：审核结果通知发送内容
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 16:08:22
     */
    public function applyForCash()
    {
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid', 'nickname', 'is_distribute']);
        if (empty($member)) {
            return false;
        }
        $this->data['touser'] = $member->xcx_openid;
        // 审核不通过时，跳转到小程序首页
        if (!empty($this->param['status']) && $this->param['status'] == 2) {
            $this->data['page'] = 'pages/index/index';
        }
        // 定义提现审核发送的内容信息
        $content = [
            ['value' => '申请成为推广者'],
            ['value' => str_limit($member->nickname, 20, '')],
            ['value' => $member->is_distribute == 1 ? "通过" : "未通过"],
            ['value' => Carbon::now()->toDateTimeString()],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }

    /**
     * @description：获取预售结果通知的发送内容
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 15:57:14
     */
    public function advanceResultNotify()
    {
        // 用户信息
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid', 'nickname', 'is_distribute']);
        if (empty($member)) {
            return false;
        }
        $this->data['touser'] = $member->xcx_openid;

        // 商品信息
        $product = Product::query()->find($this->param['product_id'], ['id', 'title']);
        if (empty($product)) {
            return false;
        }

        // 定义申请发送的内容信息
        $content = [
            ['value' => str_limit($product->title, 20, '')],
            ['value' => $this->param['time'] ?? Carbon::now()->toDateTimeString()],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }

    /**
     * @description：积分变更发送内容（这里主要是针对购物消费积分的情况，其他增加积分没有触发弹窗的事件）
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 16:09:11
     */
    public function pointConsume()
    {
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid', 'score']);
        if (empty($member)) {
            return false;
        }
        // 设置用户的openid
        $this->data['touser'] = $member->xcx_openid;
        // 赠送积分类型
        switch ($this->param['point_type']) {
            case 2:
                $msg = '签到送积分';
                break;
            case 3:
                $msg = '分享送积分';
                break;
            case 4:
                $msg = '购物抵扣';
                break;
            case 7:
                $msg = '领取会员卡送积分';
                break;
            case 8:
                $msg = '大转盘送积分';
                break;
            case 9:
                $msg = '砸金蛋送积分';
                break;
            case 10:
                $msg = '充值送积分';
                break;
            case 11:
                $msg = '刮刮卡送积分';
                break;
            default:
                $msg = '';
                break;
        }
        if (empty($msg)) {
            return false;
        }
        // 定义提现审核发送的内容信息
        $content = [
            ['value' => $this->param['score']],
            ['value' => $member->score],
            ['value' => $msg],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }

    /**
     * @description：收益到帐提醒发送内容
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 16:12:13
     */
    public function incomeReqNotify()
    {
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid']);
        if (empty($member)) {
            return false;
        }
        $this->data['touser'] = $member->xcx_openid;
        // 定义表单提交发送的内容信息
        $content = [
            ['value' => $this->param['money']],
            ['value' => $this->param['time']],
            ['value' => '推广收益'],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }

    /**
     * @description: 获取发货通知发送内容
     *
     * @return bool
     *
     * @author: 吴晓平[wuxiaoping1559@dingtalk.com] at 2019年12月12日 16:06:32
     */
    public function orderDeliveryNotify()
    {
        $member = Member::query()->find($this->param['mid'], ['id', 'xcx_openid']);
        if (empty($member)) {
            return false;
        }
        $this->data['touser'] = $member->xcx_openid;
        // 定义申请发送的内容信息
        $content = [
            ['value' => $this->param['order_num']],
            ['value' => $this->param['ship_time']],
            ['value' => $this->param['express_company']],
            ['value' => $this->param['express_no']],
            ['value' => '您的订单已发货'],
        ];
        // 组装成data数据
        $this->data['data'] = collect($this->data['keys'])->combine($content)->all();
        unset($this->data['keys']);
    }


}
