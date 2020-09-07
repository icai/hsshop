<?php

namespace App\S\Market;

use App\Lib\Redis\CouponRedis;
use App\S\S;
use Validator;

/**
 * 营销活动-优惠券
 * @author 许立 2018年09月11日
 */
class CouponService extends S
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年09月11日
     */
    public function __construct()
    {
        parent::__construct('MarketingActivityRuleCoupon');
    }

    /**
     * 获取非分页列表
     * @param array $where 查询条件
     * @param array|string $orderBy 排序字段
     * @param string $order 顺序 ASC: 顺序, DESC: 倒序
     * @return array
     * @author 许立 2018年09月11日
     */
    public function listWithoutPage($where = [], $orderBy = '', $order = '')
    {
        return [
            [
                'total' => $this->count($where),
                'data' => $this->getList($where, '', '', $orderBy, $order)
            ]
        ];
    }

    /**
     * 获取带分页列表
     * @param array $where 查询条件
     * @param array|string $orderBy 排序字段
     * @param string $order 顺序 ASC: 顺序, DESC: 倒序
     * @param int $pageSize 每页数量
     * @return array
     * @author 许立 2018年09月11日
     */
    public function listWithPage($where = [], $orderBy = '', $order = '', $pageSize = 15)
    {
        return $this->getListWithPage($where, $orderBy, $order, $pageSize);
    }

    /**
     * 根据id数组获取列表
     * @param array $idArr 主键id
     * @return array
     * @author 许立 2018年09月11日
     */
    public function getListById($idArr)
    {
        $redisData = $mysqlData = $redisId = [];
        $redis = new CouponRedis();
        $idArr = array_values($idArr);
        // 优先获取redis
        $result = $redis->getArr($idArr);
        foreach ($idArr as $key => $value) {
            if (empty($result[$key])) {
                // redis中不存在的数据 之后从数据库获取
                $redisId[] = $value;
            } else {
                $redisData[$value] = $result[$key];
            }
        }
        if ($redisId) {
            // 获取数据库数据并保存到redis
            $mysqlData = $this->model->whereIn('id', $redisId)->get()->toArray();
            $mysqlData = array_column($mysqlData, null, 'id');
            $redis->setArr($mysqlData);
        }
        return sortArr($idArr, ($redisData + $mysqlData));
    }

    /**
     * 根据id获取详情
     * @param int $id 主键id
     * @return array
     * @author 许立 2018年09月11日
     */
    public function getDetail($id)
    {
        $redis = new CouponRedis();
        $row = $redis->getRow($id);
        if (empty($row)) {
            // redis不存在 取数据库
            $row = $this->model->where('id', $id)->first();
            if (empty($row)) {
                return [];
            }
            $row = $row->toArray();
            // 保存redis
            $redis->add($row);
        }
        return $row;
    }

    /**
     * 表单字段验证
     * @param array $input 传递进来的表单字段数组
     * @param array $coupon 优惠券信息
     * @return array
     * @author 许立 2018年09月11日
     */
    public function verify($input, $coupon)
    {
        if ($this->request->isMethod('post')) {
            $rules = [];
            $messages = [];
            foreach ($input as $key => $value) {
                switch ($key) {
                    case 'title':
                        $rules['title'] = 'required_with:title|max:10';
                        $messages['title.required_with'] = '请填写标题';
                        $messages['title.max'] = '标题最多填写10个字';
                        break;
                    case 'amount':
                        $rules['amount'] = 'required';
                        $messages['amount.required'] = '面值不能为空';
                        break;
                    case 'total':
                        $rules['total'] = 'required|min:1';
                        $messages['total.required'] = '总数不能为空';
                        $messages['total.min'] = '总数最小为1';
                        break;
                    //checkbox赋值
                    case 'is_random':
                    case 'is_sync_weixin':
                    case 'expire_remind':
                    case 'is_share':
                    case 'only_original_price':
                        $input[$value] = $input[$value] ?? 0;
                        break;
                    default:
                        # code...
                        break;
                }
            }

            // 调用验证器执行验证方法
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            // checkbox赋值
            $input['is_random'] = $input['is_random'] ?? 0;
            $input['is_sync_weixin'] = $input['is_sync_weixin'] ?? 0;
            $input['expire_remind'] = $input['expire_remind'] ?? 0;
            $input['is_share'] = $input['is_share'] ?? 0;
            $input['only_original_price'] = $input['only_original_price'] ?? 0;

            // 如果设置指定商品
            $input['range_type'] == 1 && empty($input['range_value']) && error('请指定商品');

            // 选择具体时间 才需要判断时间必填
            if (empty($input['expire_type'])) {
                if (empty($input['start_at']) || $input['start_at'] == '0000-00-00 00:00:00' || empty($input['end_at']) || $input['end_at'] == '0000-00-00 00:00:00') {
                    error('请设置生效时间');
                }
            } else {
                $input['start_at'] = '0000-00-00 00:00:00';
                $input['end_at'] = '0000-00-00 00:00:00';
            }

            // 生效天数默认为1 前端可能不传
            empty($input['expire_days']) && $input['expire_days'] = 1;
            $input['expire_days'] = abs(intval($input['expire_days']));

            // 商品跳转
            $input['link_type'] == 1 && empty($input['link_id']) && error('请指定要跳转到的商品');

            // 设置跳转微页面
            $input['link_type'] == 3 && empty($input['link_id']) && error('请指定要跳转到的微页面');

            //编辑优惠券 满减不限制 需要把限制金额设置为0 Herry
            if ($input['is_limited']) {
                if ($input['limit_amount'] <= 0) {
                    // 设置成满减 满金额必须大于0
                    error('满金额必须大于0');
                }
            } else {
                $input['limit_amount'] = 0;
            }

            //剩余张数默认为总张数
            $input['left'] = $input['total'];
            if (!empty($input['id'])) {
                // 编辑优惠券
                // 修改总张数
                $alreadyReceived = $coupon['total'] - $coupon['left'];
                if ($input['total'] < $alreadyReceived) {
                    error('总张数不能小于已经领取张数');
                } elseif ($input['total'] == $alreadyReceived) {
                    $input['left'] = 0;
                } else {
                    $input['left'] = $input['total'] - $alreadyReceived;
                }
                unset($input['created_at'], $input['deleted_at']);
                $input['updated_at'] = date('Y-m-d H:i:s');
            }

            if (empty($input['range_type'])) {
                $input['range_value'] = '';
            } else {
                $input['range_value'] = trim($input['range_value'], ', ');
            }
        }

        return $input;
    }

    /**
     * 获取固定数据数组
     * @return array
     * @author 许立 2018年09月11日
     */
    public function getStaticList()
    {
        return [
            [
                'all' => '所有优惠券', 'future' => '未开始', 'on' => '进行中', 'end' => '已结束'
            ],
            ['用优惠券, 遇见更有趣的自己', '不去购物, 抢啥优惠券?', '世界一直在变, 扫货的心不变', '城里人好会玩, 用优惠券扫货', '一旦被宠爱, 必然会沦陷']
        ];
    }

    /**
     * 更新数据库中的优惠券库存信息
     * @param array $data 会员卡中的优惠券配置信息
     * @return bool
     * @author 许立 2018年09月11日
     * @update 何书哲 2019年06月20日 将参数转整型，避免报错
     */
    public function updateCoupon($data)
    {
        foreach ($data as $v) {
            //update 何书哲 2019年06月20日 将参数转整型，避免报错
            $this->increment(intval($v['coupon_id']), 'left', -intval($v['num']));
        }
        return true;
    }

    /**
     * 新增优惠券
     * @param array $input 新增数据
     * @return int
     * @author 许立 2018年09月12日
     */
    public function create($input)
    {
        $data = $this->_handleDbData($input);
        $data['wid'] = $input['wid'];
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->model->insertGetId($data);
    }

    /**
     * 更新数据库和redis
     * @param int $id 主键id
     * @param array $input 要更新的数据
     * @param bool $handleInput 是否处理传入的数据 许立 2018年09月14日
     * @return bool
     * @author 许立 2018年09月11日
     */
    public function update($id, $input, $handleInput = false)
    {
        $data = $handleInput ? $this->_handleDbData($input) : $input;
        $this->model->where('id', $id)->update($data);
        $data['id'] = $id;
        (new CouponRedis())->updateRow($data);
        return true;
    }

    /**
     * 优惠券字段自增
     * @param int $id 主键
     * @param string $field 字段
     * @param int $num 自增数量
     * @return bool
     * @author 许立 2018年09月11日
     */
    public function increment($id, $field, $num)
    {
        $this->model->wheres(['id' => $id])->increment($field, $num);
        (new CouponRedis())->incr($id, $field, $num);
        return true;
    }

    /**
     * 处理入库数据
     * @param array $input 前端传递的数据
     * @return array
     * @author 许立 2018年09月12日
     */
    private function _handleDbData($input)
    {
        return [
            'title' => $input['title'],
            'is_random' => $input['is_random'],
            'amount' => $input['amount'],
            'amount_random_max' => $input['amount_random_max'],
            'is_limited' => $input['is_limited'],
            'limit_amount' => $input['limit_amount'],
            'total' => $input['total'],
            'left' => $input['left'],
            'is_sync_weixin' => $input['is_sync_weixin'],
            'member_card_id' => $input['member_card_id'],
            'quota' => $input['quota'],
            'expire_remind' => $input['expire_remind'],
            'is_share' => $input['is_share'],
            'range_type' => $input['range_type'],
            'range_value' => $input['range_value'],
            'only_original_price' => $input['only_original_price'],
            'description' => $input['description'],
            'start_at' => $input['start_at'],
            'end_at' => $input['end_at'],
            'updated_at' => date('Y-m-d H:i:s'),
            'link_type' => $input['link_type'],
            'link_id' => $input['link_id'],
            'expire_type' => $input['expire_type'],
            'expire_days' => $input['expire_days'],
            'share_title' => $input['share_title'],
            'share_desc' => $input['share_desc'],
            'share_img' => $input['share_img']
        ];
    }
}
