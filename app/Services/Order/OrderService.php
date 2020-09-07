<?php

namespace App\Services\Order;

use App\Lib\Redis\RedisClient;
use App\Model\DistributeTemplate;
use App\Model\Income;
use App\Model\Member;
use App\Model\MemberAddress;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderLog;
use App\Model\OrderRefund;
use App\Module\MessagePushModule;
use App\Module\OrderLogisticsModule;
use App\Module\OrderModule;
use App\Module\ProductModule;
use App\S\Foundation\RegionService;
use App\S\Market\CouponLogService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Product\ProductSkuService;
use App\Services\FreightService;
use App\Services\IncomeService;
use App\Services\Lib\Service;
use App\Services\Shop\CartService;
use App\Services\WeixinService;
use DB;
use Log;
use MemberCardRecordService;
use MemberCardService;
use OrderDetailService as newOrderDetailService;
use PHPExcel;
use ProductService;
use Validator;
use OrderLogService as OrLogService;
use OrderDetailService;
use App\S\Weixin\ShopService;

/**
 * 订单
 */
class OrderService extends Service
{
    /**
     * 所有关联关系
     *
     * @var array
     */
    public $withAll = ['orderDetail', 'orderLog'];

    /**
     * 构造方法
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月14日 10:32:35
     *
     * @return void
     */
    public function __construct()
    {
        // http请求类
        $this->request = app('request');
    }

    /**
     * 初始化 设置唯一标识和redis键名
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年1月16日 14:32:03
     *
     * @param  array $unique [唯一标识数组，例如：['wid', 3] ]
     * 商家后台 - 获取店铺订单数据则传店铺id[wid]
     * 微商城   - 获取会员订单数据则传会员id[mid]
     *
     * @return this
     */
    public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
    {

        $this->initialize(new Order(), $uniqueKey, $uniqueValue, $idKey);
        return $this;
    }

    /**
     * 获取固定数据数组
     * @return OrderService
     * @author 黄东 2017年1月14日
     * @update 许立 2018年6月26日 微信审核通过状态(4)算退款完成
     * @update 张永辉 2018年7月5日 添加根绝昵称搜索字段
     * @update 何书哲 2018年6月29日 订单状态数组添加已导入、已打单
     * @update 许立 2018年08月29日 支付方式增加优惠兑换
     * @update 何书哲 2020年01月03日 添加用户id搜索
     */
    public function getStaticList()
    {
        return [
            /* 模糊查询字段数组 */
            ['oid' => '订单号', 'address_name' => '收货人姓名', 'address_phone' => '收货人手机号', 'shopSearch' => '商品搜索', 'nickname' => '微信昵称', 'mid' => '用户id'],//add 'shopSearch'=>'商品搜索' fuguowei  20180105
            /* 订单类型数组  分销订单type=6 貌似没有使用*/
            //['0'=> '全部', '1'=>'普通订单', '2'=>'代付订单', '3'=>'多人拼团订单', '4'=>'积分兑换订单' ,'5'=>'积分抵现订单','6'=>'分销订单','7'=>'秒杀订单','8'=>'小程序订单','10'=>'享立减订单'],
            ['0' => '全部', '1' => '普通订单', '3' => '多人拼团订单', '4' => '积分兑换订单', '5' => '积分抵现订单', '6' => '分销订单', '7' => '秒杀订单', '8' => '小程序订单', '10' => '享立减订单'],
            /* 物流方式数组 */
            //[ '全部', '快递发货', '上门自提', '同城配送' ],
            ['全部', '快递发货', '上门自提'],
            //[ '0'=>'全部', '1'=>'微信支付', '2'=>'支付宝支付', '3'=>'储值余额支付', '4'=>'货到付款/到店付款', '5'=>'找人代付', '6'=>'领取赠品', '7'=>'优惠兑换', '8'=>'银行卡支付', '9'=>'会员卡支付', '10'=>'小程序支付' ],
            /* 付款方式数组 */
            ['0' => '全部', '1' => '微信支付', '2' => '支付宝支付', '3' => '储值余额支付', '7' => '优惠兑换', '10' => '小程序支付'],
            /* 订单状态数组  状态100是为统计销售金额添加的wuxiaoping*/
            //update 何书哲 2018年6月29日 订单状态数组添加已导入、已打单（这两个是快递打单相关的）
            ['0' => '全部', '1' => '待付款', '-1' => '待成团', '2' => '待发货', '3' => '已发货', '4' => '已完成', '5' => '已关闭', '6' => '退款中', '8' => '待抽奖', '100' => '销售金额', '9' => '已导入', '10' => '已打单'],
            /* 维权状态数组 */
            // 许立 2018年6月26日 微信审核通过状态(4)算退款完成
            ['0' => '全部', '1,2,3,6,7,10' => '退款处理中', '4,5,8,9' => '退款结束']
        ];
    }

    /**
     * 订单构建筛选、搜索查询条件数组
     * @param array $inputField [需要搜索或筛选的参数字段数组，默认读取所有参数]
     * @return OrderService
     * @author 黄东 2017年1月14日
     * @update 许立 2018年6月26日 微信审核通过状态(4)算退款完成
     * @update 张永辉 2018年7月5日 根绝微信昵称获取订单信息
     * @update 何书哲 2018年6月29日 全部订单或者快递100已打单筛选；快递100打单状态筛选
     * @update 何书哲 2018年8月1日 修改订单来源筛选
     * @update 何书哲 2020年01月03日 添加用户id搜索
     */
    public function buildWhere(array $inputField = [])
    {
        /* 接收参数 */
        if (empty($inputField)) {
            $input = app('request')->input();
        } else {
            $input = app('request')->only($inputField);
        }

        /* 查询条件数组 */
        $where = [];
        /* 参数转换为查询条件数组 */
        if ($input) {
            foreach ($input as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                switch ($key) {
                    /* 模糊查询 */
                    case 'search':
                        if ($input['field'] == 'shopSearch') {
                            $datawhere['title'] = ['like', '%' . $input['search'] . '%'];
                            list($orderDetails) = OrderDetailService::init()->where($datawhere)->getList(false);//dd($orderDetails);
                            if ($orderDetails['data']) {
                                foreach ($orderDetails['data'] as $v) {
                                    $searchData[] = $v['oid'];
                                }
                                $where['id'] = ['in', $searchData];
                            }

                        } elseif ($input['field'] == 'nickname') {
                            $mids = (new MemberService())->getMidsByNickname($value);
                            $where['mid'] = ['in', $mids];
                        } elseif ($input['field'] == 'mid') {
                            $where['mid'] = $value;
                        } else {
                            $where[$input['field']] = array('like', '%' . $value . '%');
                        }
//上面的搜索条件修改   付国维   20180105  添加商品搜索
                        break;
                    /* 订单类型 */
                    case 'order_type':
                        if ($value == 6) {
                            $where['distribute_type'] = 1;
                        } elseif ($value == 1) {
                            $where['type'] = $value;
                            $where['distribute_type'] = 0;
                        } else {
                            $where['type'] = $value;
                        }
                        break;
                    /* 维权状态 */
                    case 'refund_status':
                        $where['refund_status'] = array('in', explode(',', $value));
                        break;
                    /* 付款方式 */
                    case 'pay_way':
                        $where['pay_way'] = $value;
                        break;
                    /* 物流方式 */
                    case 'express_type':
                        $where['express_type'] = $value;
                        break;
                    /* 订单状态 */
                    case 'status':
                        if ($value) {
                            if ($value == 6) {
                                //退款中订单 Herry 20180314
                                // 许立 2018年6月26日 微信审核通过状态(4)算退款完成
                                $where['refund_status'] = ['in', OrderRefund::REFUNDING_STATUS_ARRAY];
                            } elseif ($value == -1) {

                            } elseif ($value == 100) {
                                $where['status'] = ['in', [1, 2, 3]];
                            } elseif ($value == 9) {
                                //何书哲 2018年6月29日 快递100已导入筛选
                                $where['is_import'] = 1;
                            } elseif ($value == 10) {
                                //何书哲 2018年6月29日 快递100已打印快递单筛选
                                $where['id'] = ['in', (new OrderLogisticsModule())->getFieldByWhere([], 'oid')];
                            } else {
                                $where['status'] = $value - 1;
                            }
                        }
                        break;
                    case 'start_time':
                        if ($value && isset($input['end_time']) && $input['end_time'] >= $value) {
                            $where['created_at'] = array('between', [$value, $input['end_time']]);
                        }
                        break;
                    case 'distribute_type':
                        if ($value) {
                            $where['distribute_type'] = $value;
                        }
                        break;
                    //何书哲 2018年8月1日 修改订单来源筛选
                    case 'order_source' :
                        if ($value == 1) { //微商城订单
                            $where['source'] = 0;
                        } elseif ($value == 2) { //微信小程序订单
                            $where['source'] = 1;
                        } elseif ($value == 3) { //支付宝小程序订单
                            $where['source'] = 2;
                        }
                        break;
                    //何书哲 2018年6月29日 快递100打单状态
                    case 'logistics_status' :
                        if ($value == 1) {//已导入
                            $where['is_import'] = 1;
                        } elseif ($value == 2) {//已打单
                            $where['id'] = ['in', (new OrderLogisticsModule())->getFieldByWhere([], 'oid')];
                        }
                    default:
                        // to do somethings
                        break;
                }
            }
        }
        // 添加查询条件
        $this->whereAdd($where);

        return $this;
    }

    /**
     * 订单字段验证
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月7日 15:44:15
     *
     * @param  array $verifyField [需要验证的字段数组，默认只验证主键]
     *
     * @return array 请求参数
     */
    public function verify($verifyField = ['id'])
    {
        /* 接收数据 */
        $input = app('request')->only($verifyField);

        /* 获取验证数据和提示消息 */
        $rules = [];
        $messages = [];
        foreach ($verifyField as $value) {
            switch ($value) {
                /* 订单号 */
                case 'id':
                    $rules['id'] = 'required';
                    $messages['id.required'] = '订单不存在';
                    break;
                /* 备注 */
                case 'seller_remark':
                    $rules['seller_remark'] = 'required|max:200';
                    $messages['seller_remark.required'] = '备注不能为空';
                    $messages['seller_remark.max'] = '备注最多可输入200位字符';
                    break;
                /* 星级 */
                case 'star_level':
                    $rules['star_level'] = 'required|in:0,1,2,3,4,5';
                    $messages['star_level.required'] = '参数错误';
                    $messages['star_level.in'] = '参数异常';
                    break;
                default:
                    # code...
                    break;
            }
        }

        /* 调用验证器执行验证方法 */
        $validator = Validator::make($input, $rules, $messages);

        /* 验证不通过则提示错误信息 */
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        return $input;
    }

    /**
     * 统计订单各种状态下的订单数
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年2月9日 10:01:41
     *
     * @param  array $status [要统计的订单状态，传空数组代表查询所有状态]
     * 订单状态：-1删除；0待付款；1待发货；2已发货（待收货）；3已完成；4已关闭；5退款中
     * 维权状态（退款状态）：0非退款状态；1申请退款中；2申请退款被拒；3退款中（商家同意退款）；4退款完成
     *
     * @return array [要统计的订单状态]
     *
     */
    public function statistical()
    {
        /* 查询订单数据 */
        $list = Order::select(['pay_price', 'status', 'refund_status', 'is_hexiao', 'groups_status'])->wheres($this->where)->get()->toArray();
        /* 数据处理 */
        $return = [];
        $hexiaoOrderNum = $groupOrderNum = 0;
        foreach ($list as $key => $value) {
            /* 普通状态 支付总额和数量总计 */
            $return['status'][$value['status']]['price'] = isset($return['status'][$value['status']]['price']) ? $return['status'][$value['status']]['price'] + $value['pay_price'] : floatval($value['pay_price']);
            $return['status'][$value['status']]['count'] = isset($return['status'][$value['status']]['count']) ? $return['status'][$value['status']]['count'] + 1 : 1;

            //待发货订单过滤核销与拼团不成功订单 add wuxiaoping
            if ($value['status'] == 1) {
                if ($value['is_hexiao'] == 1) {
                    $hexiaoOrderNum += 1;
                }
                if ($value['groups_status'] == 1 || $value['groups_status'] == 3) {
                    $groupOrderNum += 1;
                }

            }
            /* 维权状态 支付总额和数量总计 */
            //退款状态不为0的才是退款订单 Herry
            if ($value['refund_status']) {
                $return['refundStatus'][$value['refund_status']]['price'] = isset($return['refundStatus'][$value['refund_status']]['price']) ? $return['refundStatus'][$value['refund_status']]['price'] + $value['pay_price'] : floatval($value['pay_price']);
                $return['refundStatus'][$value['refund_status']]['count'] = isset($return['refundStatus'][$value['refund_status']]['count']) ? $return['refundStatus'][$value['refund_status']]['count'] + 1 : 1;
            }
        }
        // 待发货订单统计数量排除核销订单(不需要发货) add wuxiaoping
        if ($return) {
            foreach ($return['status'] as $item => &$items) {
                if ($item == 1) {
                    $items['count'] -= $hexiaoOrderNum;
                    $items['count'] -= $groupOrderNum;
                }
            }
        }

        return $return;
    }

    /**
     * 导出excel表格
     * @param array $data 导出的列表
     * @param string $type 导出类型
     * @author 许立 2017年03月06日
     * @update 许立 2018年07月13日 店铺导出增加到期时间
     * @update 何书哲 2019年07月18日 店铺会员数据导出数据字段不存在处理
     * @update 何书哲 2019年10月11日 修改订单批量导出只在订单第一件商品显示
     */
    public function exportExcel($data = [], $type = 'order')
    {
        $title = '订单报表';
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("导出订单")
            ->setSubject("导出订单")
            ->setDescription("导出订单")
            ->setKeywords("导出订单")
            ->setCategory("result file");
        //设置单元格宽度
        if ($type == 'order') {
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('M')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('O')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('P')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('Q')->setWidth(10);

            //标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '订单号')
                ->setCellValue('B1', '支付流水号')
                ->setCellValue('C1', '商品图片')
                ->setCellValue('D1', '下单时间')
                ->setCellValue('E1', '售后')
                ->setCellValue('F1', '邮费')
                ->setCellValue('G1', '订单状态')
                ->setCellValue('H1', '商品标题')
                ->setCellValue('I1', '商品sku')
                ->setCellValue('J1', '数量')
                ->setCellValue('K1', '商品原价')
                ->setCellValue('L1', '实付订单金额')
                ->setCellValue('M1', '姓名')
                ->setCellValue('N1', '联系方式')
                ->setCellValue('O1', '发货日期')
                ->setCellValue('P1', '地址')
                ->setCellValue('Q1', '成本价');
            $result = [];
            foreach ($data as $k => $v) {
                $orderNum = count($v['orderDetail']);
                //merge_insert 插入
                //merge  合并
                //merge_add 连接

                foreach ($v['orderDetail'] as $key => $val) {
                    $val['oid'] = $v['oid'];
                    $val['trade_id'] = $v['trade_id'];
                    $val['created_at_add'] = $v['created_at'];
                    $val['refund_status'] = $v['refund_status'];
                    $val['freight_price'] = $v['freight_price'];
                    $val['status'] = $v['status'];
                    $val['pay_price'] = $v['pay_price'];
                    $val['address_name'] = $v['address_name'];
                    $val['address_phone'] = $v['address_phone'];
                    $val['address_detail'] = $v['address_detail'];
                    $val['groups_id'] = $v['groups_id'];
                    $val['groups_status'] = $v['groups_status'];

                    $val['merge_insert'] = $val['merge'] = $val['merge_add'] = 0;
                    if ($orderNum > 1 && ($key == 0 || $orderNum == ($key + 1))) {
                        $val['merge_add'] = 1;
                    }
                    if ($key == 0) {
                        $val['merge_insert'] = 1;
                    }
                    if ($key > 0 && $orderNum == ($key + 1)) {
                        $val['merge'] = 1;
                    }
                    //订单中该商品最终分配到的金额 20180510 Herry
                    //$val['final_price'] = $this->_getFinalPrice($v['orderDetail'], $val, $v);

                    $result[] = $val;
                }
            }
            $merge_add = '';
            //对一个订单多条数据分行显示
            foreach ($result as $k => $v) {
                $num = $k + 2;
                //Excel数据填充
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $v['oid'] ?? '')
                    ->setCellValue('B' . $num, $v['trade_id'] ?? '')
                    ->setCellValue('C' . $num, $v['img'] ?? '')
                    ->setCellValue('D' . $num, $v['created_at_add'] ?? '')
                    ->setCellValue('E' . $num, empty($v['refund_status']) ? '非退款' : (in_array($v['refund_status'], [1, 3]) ? '退款处理中' : '退款完成'))
                    ->setCellValue('F' . $num, $v['freight_price'] ?? '');
                //如果订单是拼团订单，则显示相应的拼团订单状态；如果不是，则显示其他订单的状态
                //add by wuxiaoping 加$v['status'] <> 4 把订单状态为4的显示为已关闭
                if ($v['groups_id'] != 0 && $v['groups_status'] == 1 && $v['status'] != 0 && $v['status'] <> 4) {
                    $excelObj->setActiveSheetIndex()
                        ->setCellValue('G' . $num, '待成团');
                } else {
                    $excelObj->setActiveSheetIndex()
                        ->setCellValue('G' . $num, Order::ORDER_STATUS_MAP[$v['status']] ?? '其他');
                }
                //对数据库的发货时间（时间戳）进行转化
                if ($v['delivery_time']) {
                    $delivery_time = date('Y-m-d H:i:s', $v['delivery_time']);
                } else {
                    $delivery_time = '';
                }

                $excelObj->setActiveSheetIndex()
                    ->setCellValue('H' . $num, $v['title'] ?? '')
                    ->setCellValue('I' . $num, $v['spec'] ?? '')//拼接
                    ->setCellValue('J' . $num, $v['num'] ?? '')
                    ->setCellValue('K' . $num, $v['price'] ?? '');
                if ($v['merge_insert'] == 1) {
                    $excelObj->setActiveSheetIndex()->setCellValue('L' . $num, $v['pay_price'] ?? '');
                }
                if ($v['merge_add'] == 1) {
                    if (empty($merge_add)) {
                        $merge_add = 'L' . $num;
                    } else {
                        $merge_add .= ':L' . $num;
                    }
                }
                if ($v['merge'] == 1) {
                    $excelObj->setActiveSheetIndex()->mergeCells($merge_add);
                    $merge_add = '';
                }

                $excelObj->setActiveSheetIndex()
                    ->setCellValue('M' . $num, $v['address_name'] ?? '')
                    ->setCellValue('N' . $num, $v['address_phone'] ?? '')
                    ->setCellValue('O' . $num, $delivery_time)
                    ->setCellValue('P' . $num, $v['address_detail'] ?? '')
                    ->setCellValue('Q' . $num, $v['cost_price'] ?? '');
            }

        } else if ($type == 'otherBill' || $type == 'bill') {
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(30);
            //标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '结算时间')
                ->setCellValue('B1', '类型')
                ->setCellValue('C1', '金额')
                ->setCellValue('D1', '支付渠道')
                ->setCellValue('E1', '交易单号');
            foreach ($data as $k => $v) {
                $num = $k + 2;
                //Excel数据填充
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $v['created_at'])
                    ->setCellValue('B' . $num, '订单收入')
                    ->setCellValue('C' . $num, $v['pay_price'])
                    ->setCellValue('D' . $num, Order::ORDER_PAY_WAY_MAP[$v['pay_way']] ?? '其他')
                    ->setCellValue('E' . $num, $v['trade_id']);
            }
        }//add by wuxiaoping 2017.09.19 核销订单导出
        else if ($type == 'hexiaoOrder') {
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(10);

            for ($i = 1; $i <= 11; $i++) {
                $excelObj->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
            }

            //标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '订单号')
                ->setCellValue('B1', '核销号')
                ->setCellValue('C1', '支付流水号')
                ->setCellValue('D1', '商品标题/属性')
                ->setCellValue('E1', '商品图片')
                ->setCellValue('F1', '单价/数量')
                ->setCellValue('G1', '售后')
                ->setCellValue('H1', '买家信息')
                ->setCellValue('I1', '下单时间')
                ->setCellValue('J1', '订单状态')
                ->setCellValue('K1', '实付金额');

            for ($i = 'A'; $i <= 'K'; $i++) {
                for ($j = 1; $j <= 16; $j++) {
                    //设置水平，垂直居中
                    $excelObj->getActiveSheet()->getStyle($i . $j)->getAlignment()->setHorizontal('center');
                    $excelObj->getActiveSheet()->getStyle($i . $j)->getAlignment()->setVertical('center');
                }
            }
            foreach ($data as $k => $v) {
                $num = $k + 2;
                //$excelObj->getActiveSheet()->getStyle('A'.$num)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                //$excelObj->getActiveSheet()->getStyle('A'.$num)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                //Excel数据填充
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $v['oid'])
                    ->setCellValue('B' . $num, $v['hexiao_code'])
                    ->setCellValue('C' . $num, $v['serial_id'])
                    ->setCellValue('D' . $num, $v['pro_info'])
                    ->setCellValue('E' . $num, $v['img'])
                    ->setCellValue('F' . $num, $v['price-num'])
                    ->setCellValue('G' . $num, '')
                    ->setCellValue('H' . $num, $v['address_name'] . '/' . $v['address_phone'])
                    ->setCellValue('I' . $num, $v['created_at'])
                    ->setCellValue('J' . $num, $v['status'])
                    ->setCellValue('K' . $num, $v['pay_price']);
            }
        } else if ($type == 'sendProduct') {

            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('N')->setWidth(20);

            // 标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '订单编号')
                ->setCellValue('B1', '收件人姓名')
                ->setCellValue('C1', '收件人手机')
                ->setCellValue('D1', '收件省')
                ->setCellValue('E1', '收件市')
                ->setCellValue('F1', '收件区/县')
                ->setCellValue('G1', '收件人地址')
                ->setCellValue('H1', '品名')
                ->setCellValue('I1', '数量')
                ->setCellValue('J1', '备注')
                ->setCellValue('K1', '微信昵称')
                ->setCellValue('L1', '下单时间')
                ->setCellValue('M1', '订单状态')
                ->setCellValue('N1', '订单总额');

            $result = [];
            $n = 2;
            foreach ($data as $k => $v) {
                foreach ($v['orderDetail'] as $kk => $val) {
                    $title = $val['title'];
                    // 字符串替换的原因是怕数据库字段有逗号,影响了输出的结果,每一个逗号输出占用一个格
                    $spec = str_replace(',', '', $val['spec']);
                    $titleSpec = $title . " " . $spec . "    ";

                    if ($v['address_id'] == 0) {
                        $v['address_name'] = '无需物流';
                        $v['address_phone'] = '无需物流';
                        $v['address_province'] = '无需物流';
                        $v['address_area'] = '无需物流';
                        $v['address_detail'] = '无需物流';
                    }
                    $excelObj->setActiveSheetIndex()
                        ->setCellValue('A' . $n, $kk == 0 ? $v['oid'] : '')
                        ->setCellValue('B' . $n, $kk == 0 ? $v['address_name'] : '')
                        ->setCellValue('C' . $n, $kk == 0 ? $v['address_phone'] : '')
                        ->setCellValue('D' . $n, $kk == 0 ? $v['address_province'] : '')
                        ->setCellValue('E' . $n, $kk == 0 ? $v['address_city'] : '')
                        ->setCellValue('F' . $n, $kk == 0 ? str_replace(',', ' ', $v['address_area']) : '')
                        ->setCellValue('G' . $n, $kk == 0 ? str_replace(',', ' ', $v['address_detail']) : '')
                        ->setCellValue('H' . $n, $titleSpec)
                        ->setCellValue('I' . $n, $val['num'])
                        ->setCellValue('J' . $n, $kk == 0 ? $v['buy_remark'] : '')
                        ->setCellValue('K' . $n, $kk == 0 ? (!empty($v['member']) ? $v['member']['nickname'] : '') : '')
                        ->setCellValue('L' . $n, $kk == 0 ? (!empty($v['orderLog']) ? $v['orderLog'][0]['created_at'] : '') : '')
                        ->setCellValue('M' . $n, $kk == 0 ? (Order::ORDER_STATUS_MAP[$v['status']] ?? '') : '')
                        ->setCellValue('N' . $n, $kk == 0 ? $v['pay_price'] : '');
                    $n++;
                }
            }
        } else if ($type == 'shopExport') {
            //店铺导出
            $title = '店铺统计';
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(25);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('K')->setWidth(50);
            $excelObj->getActiveSheet()->getColumnDimension('L')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('M')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            //标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '店铺名')
                ->setCellValue('B1', '店铺类型')
                ->setCellValue('C1', '销售额')
                ->setCellValue('D1', '付款订单数')
                ->setCellValue('E1', '会员数')
                ->setCellValue('F1', '登录手机号')
                ->setCellValue('G1', '公司名称')
                ->setCellValue('H1', '创建时间')
                ->setCellValue('I1', '修改时间')
                ->setCellValue('J1', '修改创建天数差')
                ->setCellValue('K1', '地址')
                ->setCellValue('L1', '是否收费')
                ->setCellValue('M1', '到期时间')
                ->setCellValue('N1', '业务员')
                ->setCellValue('O1', '用户名字');
            foreach ($data as $k => $v) {
                $num = $k + 2;
                //Excel数据填充
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, $v['shop_name'])
                    ->setCellValue('B' . $num, $v['role'])
                    ->setCellValue('C' . $num, $v['sale_sum'])
                    ->setCellValue('D' . $num, $v['paid_order_count'])
                    ->setCellValue('E' . $num, $v['member_sum'])
                    ->setCellValue('F' . $num, $v['user']['mphone'] ?? '')
                    ->setCellValue('G' . $num, $v['company_name'])
                    ->setCellValue('H' . $num, $v['created_at'])
                    ->setCellValue('I' . $num, $v['updated_at'])
                    ->setCellValue('J' . $num, ceil((strtotime($v['updated_at']) - strtotime($v['created_at'])) / 86400))
                    ->setCellValue('K' . $num, $v['province_name'] . $v['city_name'] . $v['area_name'] . $v['address'])
                    ->setCellValue('L' . $num, $v['is_fee'] ? '收费' : '免费')
                    ->setCellValue('M' . $num, $v['end_time'])
                    ->setCellValue('N' . $num, $v['user']['saleAchieve']['salesman'] ?? '')
                    ->setCellValue('O' . $num, $v['user']['name'] ?? '');
            }
        }
        //准备导出
        $excelObj->setActiveSheetIndex();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $title . '_' . time() . '.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * 商品详情
     * @param $wid 店铺id
     * @param $oid 订单id
     * @param bool $isExtra 是否额外加载 true:是 false：否
     * @return mixed
     * @update 何书哲[heshuzhe7066@dingtalk.com] at 2019年10月10日 19:36:47 导出订单添加额外关联加载
     */
    public function orderDetail($wid, $oid, $isExtra = false)
    {
        $orderDetail = $this->init('wid', $wid)->model->find($oid)->load('orderDetail');
        if ($isExtra) {
            $orderDetail = $orderDetail
                ->load(['member:id,nickname', 'orderLog' => function ($query) {
                    $query->where('action', 2)->select(['oid', 'created_at'])->orderBy('created_at', 'asc');
                }]);
        }
        $orderDetail = $orderDetail->toArray();
        return $orderDetail;
    }

    /***
     * todo 检查购物车中的商品是否存在异常
     * @param $data
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-11
     * @update 张永辉 2018年9月5日 整理代码修改简化代码
     * @update 陈文豪 2020年3月13日 会员卡打折，商品不支持，商品原价却也不能使用优惠券
     */
    public function processOrder($data, $umid)
    {
        $returnData = array('errCode' => 0, 'errMsg' => '', 'data' => []);
        if (empty($data['wid']) || empty($data['mid']) || empty($data['cart_id'])) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '店铺,用户,购物车,id不能为空';
            return $returnData;
        }
        $mid = $data['mid'];
        $wid = $data['wid'];
        $cartID = $data['cart_id'];
        $address_id = isset($data['address_id']) ? intval($data['address_id']) : 0;

        //用户购物车中要结算的商品信息
        $cartService = new CartService();
        $userCart = $cartService->init('mid', $mid)->where(['id' => ['in', $cartID]])->getList(false);
        //保存用户购物车中的商品ID
        $userCartProduct = array_column($userCart[0]['data'], 'product_id');
        //判断购物车中是否商品
        if (empty($userCartProduct)) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = '购物车中没有商品';
            return $returnData;
        }
        //查询购物车中的商品对应的商品明细
        $productList = ProductService::getProducts($userCartProduct);
        if (empty($productList['data'])) {
            $returnData['errCode'] = -4;
            $returnData['errMsg'] = '没有查询到商品信息';
            return $returnData;
        }

        //会员卡 可以免运费，打折扣  折扣比例
        $discount = 0;
        //会员卡拥有的特权
        $userCard = MemberCardRecordService::useCard($mid, $wid);
        if ($userCard['errCode'] == 0 && $userCard['data']['isOwn'] == 1) {
            if ($userCard['data']['info']['isDiscount'] == 1) {
                $discount = $userCard['data']['info']['discount'];
            }
            $card_id = $userCard['data']['info']['card_id'];
        }

        //设置默认批发价商品价等
        $is_wholesale = $total = $afterDiscountTotal = 0.00;
        $product = ['error' => [], 'correct' => []];//定义存放商品信息数组
        //获取购物车中的商品信息
        $cartProduct = $userCart[0]['data'];

        //检查商品是否小于最小购买量 陈文豪 20181211
        $checkNum = [];

        //购物车中的商品
        foreach ($cartProduct as $cartItem) {
            if ($cartItem['groups_id'] != 0 || $cartItem['seckill_id'] != 0) {
                $discount = 0;
            }
            $num = $cartItem['num'];//商品数量
            $productID = $cartItem['product_id'];//商品id
            $prop1 = $cartItem['prop1'];//商品属性1
            $propValue1 = $cartItem['prop_value1'];//商品属性值1
            $prop2 = $cartItem['prop2'];//商品属性2
            $propValue2 = $cartItem['prop_value2'];//商品属性值2
            $prop3 = $cartItem['prop3'];//商品属性3
            $propValue3 = $cartItem['prop_value3'];//商品属性值3
            $propID = $cartItem['prop_id'];//商品规格id

            $attr = '';
            if (!empty($propID)) {
                $attr = $prop1 . ':' . $propValue1;
                if (!empty($prop2))
                    $attr .= ',' . $prop2 . ':' . $propValue2;
                if (!empty($prop3))
                    $attr .= ',' . $prop3 . ':' . $propValue3;
            }
            /**
             * 商品错误码
             *   [-1表示下架 -2 购买数量超过库存 -3购买数量过多
             *   -4超区配送区域 -5定时开售商品还没有开售 -6非实物商品]
             */
            /** 异常商品详情
             * $errorDetails=
             * [
             * -1=>'商品下架',-2=>'购买数量超过库存',-3=>'购买数量超过限购数量',
             * -4=>'商品超过了配送区域',-5=>'定时开售商量还没有开售',-6=>'非实物商品'
             * ];
             */
            //在商品表中查询购物车中商品对应的价格,库存等信息。
            foreach ($productList['data'] as $item) {
                if ($item['id'] == $productID) {
                    $productData = [];
                    $stock = $weight = 0;   //默认库存，重量为0
                    $price = $oldPrice = $afterDiscountPrice = 0.00; //默认，价格，原价，折扣价
                    $status = $isAttr = $id = 0; //默认商品状态，下架，无规格，id为0
                    $errorProductDetails = [];   //定义商品

                    //Herry 检查每人购买商品数量限制
                    if (isset($checkNum[$item['id']])) {
                        $checkNum[$item['id']] += $cartItem['num'];
                    } else {
                        $checkNum[$item['id']] = $cartItem['num'];
                    }

                    if ($cartItem['seckill_id'] == 0 && $item['quota'] && $item['quota'] < newOrderDetailService::productBuyNum($mid, $item['id']) + $checkNum[$item['id']]) {
                        $errorProductDetails[] = ['errCode' => -9, 'errMsg' => $item['title'] . ' 超过限购数量', 'data' => 0];
                    }
                    //检查商品是否是价格面议 Herry 20180423
                    if ($cartItem['seckill_id'] == 0 && $item['is_price_negotiable']) {
                        $errorProductDetails[] = ['errCode' => -1, 'errMsg' => '订单中包含面议商品, 商品名: ' . $item['title'], 'data' => 0];
                    }
                    //检查商品是否小于最小购买量 Herry 20180502
                    if ($cartItem['seckill_id'] == 0 && $num < $item['buy_min']) {
                        $errorProductDetails[] = ['errCode' => -1, 'errMsg' => '小于最小购买量, 商品名: ' . $item['title'], 'data' => 0];
                    }

                    if ($item['sku_flag'] == 1 && !empty($propID)) {
                        $isFind = 0;
                        //如果不从缓存中查询 productProp不存在
                        if (empty($item['productProp'])) {
                            $returnData['errCode'] = -1;
                            $returnData['errMsg'] = $attr . '没有查询到对应的规格数据';
                            return $returnData;
                        }

                        foreach ($item['productProp'] as $prop) {
                            //有规格的商品，从规格表中取库存,价格
                            if ($prop['pid'] == $productID && $prop['id'] == $propID) {
                                //商品规格表中的id
                                $id = $prop['id'];
                                //占位符
                                $isAttr = 1;
                                $stock = $prop['stock_num'];
                                $price = $prop['price'];
                                $oldPrice = 0;
                                if ($cartItem['groups_id'] != 0 || $cartItem['seckill_id'] != 0) {
                                    $afterDiscountPrice = $cartItem['price'];
                                } else {
                                    if (!empty($discount)) {
                                        $afterDiscountPrice = $prop['price'] * $discount / 10;
                                        //格式化价格
                                        $afterDiscountPrice = sprintf('%.2f', $afterDiscountPrice);
                                    }
                                    /*****************************************/
                                    /**
                                     * add by meiJie  重构价格
                                     */
                                    if (isset($card_id)) {
                                        $reData = ProductService::reSetSkuPrice($prop, $mid, $wid);
                                        $afterDiscountPrice = $reData['price'];
                                        $reData['is_vip'] == 1 && $discountFlag = 1;
                                    }
                                }

                                /******************************************/
                                //此处重量读的是商品表
                                $weight = $item['weight'];
                                //此次上下架读的是商品表
                                $status = $item['status'];
                                $isFind = 1;
                                break;
                            }
                        }
                        //有规格的商品，在规格表中是否查询到数据
                        if (!$isFind && $cartItem['seckill_id'] == 0) {
                            $errorProductDetails[] = ['errCode' => -9, 'errMsg' => $item['title'] . ' ' . $attr . '已售罄', 'data' => 0];
                        }
                    } else {
                        //购物车中商品有规格，而商品表中规格已经关闭
                        if (!empty($propID) && $cartItem['seckill_id'] == 0) {
                            $errorProductDetails[] = ['errCode' => -8, 'errMsg' => $item['title'] . ' ' . $attr . '规格发生变化', 'data' => 0];
                        }
                        //无规格商品直接从商品表中取库存和价格
                        $stock = $item['stock'];
                        $price = $item['price'];
                        $status = $item['status'];
                        $weight = $item['weight'];
                        $oldPrice = $item['oprice'];
                        if ($cartItem['groups_id'] != 0 || $cartItem['seckill_id'] != 0) {
                            $afterDiscountPrice = $cartItem['price'];
                        } else {
                            if (!empty($discount)) {
                                $afterDiscountPrice = $item['price'] * $discount / 10;
                                $afterDiscountPrice = sprintf('%.2f', $afterDiscountPrice);
                            }
                            /**
                             * add by meiJie  重构价格
                             */
                            if (isset($card_id)) {
                                $reData = ProductService::reSetNoSkuPrice($item, $mid, $wid);
                                $afterDiscountPrice = $reData['price'];
                                $reData['is_vip'] == 1 && $discountFlag = 1;;
                            }
                        }
                    }

                    //商品已下架
                    if ($status == 0) {
                        $errorProductDetails[] = ['errCode' => -1, 'errMsg' => $item['title'] . ' ' . $attr . '商品下架', 'data' => 0];
                    }
                    //购买数量大于库存数量
                    if ($num > $stock && $cartItem['seckill_id'] == 0) {
                        //array_push($errProduct,-2);
                        $errorProductDetails[] = ['errCode' => -2, 'errMsg' => $item['title'] . ' ' . $attr . '购买数量超过库存，库存为:' . $stock, 'data' => $stock];
                    }
                    //限购数量 0表示不限购
                    //异常商品
                    if ($item['quota'] > 0 && $item['quota'] < $num && $cartItem['seckill_id'] == 0) {
                        //array_push($errProduct,-3);
                        $errorProductDetails[] = ['errCode' => -3, 'errMsg' => $item['title'] . ' ' . $attr . '购买数量超过限购数量，限购数为：' . $item['quota'], 'data' => $item['quota']];
                    }


                    // //运费存放到数组中
                    // array_push($productFreight, $freight);
                    //购买权限 0表示所有用户都可以购买 1制定用户可以购买
                    if ($item['buy_permissions_flag'] == 1) {
                        //取出当前用户拥有的会员卡
                        $membersData = MemberCardService::getCardList($mid, $wid);
                        //当前用户没有会员卡
                        if (empty($membersData['cards'])) {
                            $errorProductDetails[] = ['errCode' => -4, 'errMsg' => '当前会员没有会员卡', 'data' => 0];
                        } elseif (!empty($membersData['cards'])) {
                            //取出用户拥有的会员卡id
                            $userCard = [];
                            foreach ($membersData['cards'] as $everyItem) {
                                array_push($userCard, $everyItem['id']);
                            }
                            $buyPermission = $item['buy_permissions_level_id'];
                            //商品拥有的会员卡id
                            $productCard = explode(',', $buyPermission);
                            //当前用户拥有的会员卡是否在商品会员卡中
                            $section = array_intersect($userCard, $productCard);
                            //不存在交集
                            if (empty($section)) {
                                $errorProductDetails[] = ['errCode' => -5, 'errMsg' => '当前会员没有购买' . $item['title'] . ' ' . $attr . '的权限', 'data' => $buyPermission];
                            }
                        }
                    }
                    if ($item['sale_time_flag'] == 2) {
                        //定时开售商品还没有开售
                        if (!empty($item['sale_time']) && strtotime($item['sale_time']) > time() && $cartItem['seckill_id'] == 0) {
                            //array_push($errProduct,-5);
                            $errorProductDetails[] = ['errCode' => -6, 'errMsg' => $item['title'] . ' ' . $attr . '还没有开售', 'data' => $item['sale_time']];
                        }
                    }
                    if ($item['type'] != '1') {
                        //array_push($errProduct,-6);
                        $errorProductDetails[] = ['errCode' => -7, 'errMsg' => $item['title'] . '非实物商品', 'data' => $item['type']];
                    }
                    //购物车id 用于清空购物车
                    $productData['cart_id'] = $cartItem['id'];
                    //商品id
                    $productData['product_id'] = $item['id'];
                    //商品名称
                    $productData['product_name'] = $item['title'];
                    //商品购买数量
                    $productData['num'] = $num;
                    //商品规格
                    $productData['attr'] = $attr;
                    //图片的绝对路径
                    $productData['img_path'] = imgUrl($item['img']);
                    //图片的相对路径
                    $productData['img_url'] = $item['img'];
                    //是否为核销商品 add wuxiaoping 2017.09.11
                    $productData['is_hexiao'] = $item['is_hexiao'];
                    $productData['hexiao_start'] = $item['hexiao_start'] ?? '';
                    $productData['hexiao_end'] = $item['hexiao_end'] ?? '';
                    //商品是否可以使用积分 add by jonzhang 2017-11-23
                    $productData['is_point'] = $item['is_point'];

                    //todo 待验证 批发价商品 只使用批发价设置的价格 不使用优惠券 积分 会员卡
                    $productData['wholesale_flag'] = $item['wholesale_flag'];
                    if ($item['wholesale_flag']) {
                        $is_wholesale = 1;
                        $price = (new ProductModule())->getWholesalePrice($item['id'], $num);
                        $afterDiscountPrice = 0;
                        $productData['is_point'] = 0;
                    }

                    //商品价格
                    $productData['price'] = $price;
                    //商品原价
                    $productData['old_price'] = $oldPrice;
                    //商品折扣价
                    $productData['after_discount_price'] = $afterDiscountPrice;
                    //商品金额
                    $productData['product_amount'] = $price * $num;
                    //商品折扣金额
                    $productData['after_discount_product_amount'] = $afterDiscountPrice * $num;
                    //所有商品总金额
                    $total = $total + $productData['product_amount'];
                    //折扣后的总金额
                    $afterDiscountTotal = $afterDiscountTotal + $productData['after_discount_product_amount'];
                    //add by 张国军 2018年08月06日
                    $productData['cam_id'] = $item['cam_id'] ?? 0;
                    //$data['stock']=$stock;
                    //dd($productData);
                    if (empty($errorProductDetails)) {
                        //此处id,is_attr 更改库存时使用到
                        $productData['is_attr'] = $isAttr;
                        //商品规格表中的id
                        $productData['product_prop_id'] = $id;
                        $product['correct'][] = $productData;
                    } else {
                        //$productData['err_msg']=$errorDetails;
                        $productData['err_msg'] = $errorProductDetails;
                        $product['error'][] = $productData;
                    }
                    break;
                }
            }
        }
//        dd($product);


        //运费信息 Herry 20171123
        $product['freight'] = (new OrderModule())->getFreightByCartIDArr($cartID, $wid, $mid, $umid, $address_id);
        //todo 目前此字段前端没有用到 先返回0 Herry 20171123
        $product['derate_freight'] = 0.00;

        //商品总金额
        $product['amount'] = $total;
        //商品是否折扣

        $product['is_discount'] = ($discount > 0 || isset($discountFlag)) ? 1 : 0;

        $is_wholesale && $product['is_discount'] = 0;

        //商品折扣的总金额
        $product['after_discount_amount'] = $afterDiscountTotal;

        // 会员折扣即使不能用，处理优惠券也不能使用的问题 2020年03月13日11:30 陈文豪
        if (empty(bccomp($afterDiscountTotal, $total, 2))) {
            $product['is_discount'] = 0;
        }

        //商品信息
        $returnData['data'] = $product;
        return $returnData;
    }

    /**
     * todo 查询用户订单的状态
     * @return mixed
     * @author
     * @date 2017-04-13
     */
    public function getOrderData($data)
    {
        //不返回退款完成的订单 Herry 20171226
        //全部商品退款完成会关闭订单（status=4）；订单中部分退款完成refund_status也会等于8，但是订单状态不变 20180124
        return OrderService::init()->model->select(DB::raw('count(*) as number,status,refund_status'))->where($data)->groupBy('status')->get()->toArray();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170505
     * @desc 更新订单日志附表
     * @param $oid
     */
    public function upOrderLog($oid, $wid)
    {
        $order = $this->init('wid', $wid)->model->find($oid)->load('orderLog')->toArray();
        $data['orderLog'] = $order['orderLog'];
        $res = $this->updateR($oid, $data, false);
        return $res;
    }

    /**
     * @author wuxiaoping
     * 2018.06.11
     * @desc 更新订单详情
     * @param $oid
     */
    public function upOrderDetail($oid, $wid)
    {
        $order = $this->init('wid', $wid)->model->find($oid)->load('orderDetail')->toArray();
        $data['orderDetail'] = $order['orderDetail'];
        $res = $this->updateR($oid, $data, false);
        return $res;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170601
     * @desc 分销订单进行佣金分配及其他的操作
     * @param $oid
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @udpate 何书哲 2018年11月05日 小程序佣金发放通知
     * @update 张永辉 2018年12月7ri 对订单分钱重新写
     * @梅杰 2019年09月29日 09:55:33 佣金消息
     */
    public function distribute($order, $type = '1')
    {
        $result = [
            'success' => 0,
            'message' => '',
        ];
        if ($order['pay_price'] <= 0) {
            $result['message'] = '订单金额为0';
            return $result;
        }
        //删除预计算佣金
        Income::where('oid', $order['id'])->where('status', '-3')->delete();
        //$shopData = (new WeixinService())->getStore($order['wid']);
        $shopService = new ShopService();
        $shopData = $shopService->getRowById($order['wid']);
        if (empty($shopData) || $shopData['is_distribute'] != 1) {
            $result['message'] = '店铺未开启分销';
            return $result;
        }

        $count = Income::where('oid', $order['id'])->where('status', '!=', '-3')->count();
        if ($count > 0) {
            $result['message'] = '该订单已分配过佣金';
            return $result;
        }
        $order['pay_price'] = bcsub($order['pay_price'], $order['freight_price'], 2);
        $orderDetail = OrderDetail::where('oid', $order['id'])->get()->load('product')->toArray();
        $tids = [];
        $allPrice = 0;
        foreach ($orderDetail as $key => $val) {
            $allPrice = $allPrice + $val['price'] * $val['num'];
            if ($val['product']['is_distribution'] == 1 && $val['product']['distribute_template_id']) {
                $tids[] = $val['product']['distribute_template_id'];
            } else {
                unset($orderDetail[$key]);
            }
        }
        if (!$tids) {
            $result['message'] = '该订单不存在分销商品';
            return $result;
        }

        $template = $this->getDistributeTemplate($tids);
        foreach ($orderDetail as $key => $val) {
            $orderDetail[$key]['template'] = $template[$val['product']['distribute_template_id']];
        }
        $data = [];
        foreach ($orderDetail as $value) {
            //计算发放佣金
            foreach ($value['template'] as $key => $item) {
                $remainder = ($value['price'] * $value['num'] / $allPrice) * $order['pay_price'] - ($value['price'] * $value['num'] * $item['cost'] / 100);
                $all = $item['zero'] + $item['one'] + $item['sec'] + $item['three'];
                if ($remainder > 0) {
                    $data[$key]['zero'] = ($data[$key]['zero'] ?? 0) + $remainder * $item['zero'] / $all;
                    $data[$key]['one'] = ($data[$key]['one'] ?? 0) + $remainder * $item['one'] / $all;
                    $data[$key]['two'] = ($data[$key]['two'] ?? 0) + $remainder * $item['sec'] / $all;
                    $data[$key]['three'] = ($data[$key]['three'] ?? 0) + $remainder * $item['three'] / $all;
                }

                $logData = [
                    'remainder' => $remainder,
                    'price' => $value['price'],
                    'num' => $value['num'],
                    'allPrice' => $allPrice,
                    'orderDetail' => $value,
                ];
            }
        }
        $mid = $order['mid'];
        $source = ['zero', 'one', 'two', 'three'];
        foreach ($source as $key => $val) {
            if ($mid == 0) {
                break;
            }
            $member = Member::select(['id', 'pid', 'distribute_grade_id', 'is_distribute'])->find($mid);
            if (!empty($shopData['distribute_grade']) && $shopData['distribute_grade'] == 1 && $key > 0 && $member->is_distribute == 0) {
                break;
            }
            if (!empty($data[$member->distribute_grade_id][$val]) && $data[$member->distribute_grade_id][$val] > 0.01) {
                $incomeData = [
                    'mid' => $mid,
                    'wid' => $order['wid'],
                    'omid' => $order['mid'],
                    'money' => $data[$member->distribute_grade_id][$val],
                    'oid' => $order['id'],
                    'level' => $key,
                ];
                if ($type == '2') {
                    $incomeData['status'] = '-3';
                }
                Income::insert($incomeData);

                if ($type == 1) {
                    // 何书哲 2018年11月05日 下级下单的佣金提醒
                    $commissionData = [
                        'mid' => $mid,
                        'oid' => $order['oid'],
                        'money' => $data[$member->distribute_grade_id][$val],
                        'commission_type' => 'commission_order'
                    ];

                    if ($order['source']) {
                        (new MessagePushModule($order['wid'], MessagesPushService::CommissionGrant, MessagePushModule::SEND_TARGET_WECHAT_XCX))
                            ->sendMsg($commissionData, $order['xcx_config_id']);
                    } else {
                        (new MessagePushModule($order['wid'], MessagesPushService::CommissionGrant))->sendMsg($commissionData);
                    }
                }
            }
            $mid = $member->pid;
        }
        (new OrderService())->init()->where(['id' => $order['id']])->update(['distribute_type' => 1], false);
        $result['success'] = 1;
        return $result;

    }


    /**
     * @desc 获取不同分销员的分销模板
     * @param $ids
     * @return array
     * @author 张永辉 2018年12月07日
     */
    public function getDistributeTemplate($ids)
    {
        $data = DistributeTemplate::whereIn('id', $ids)->get()->load('gradeTemplate')->toArray();
        $result = [];
        foreach ($data as $val) {
            $temp[0] = [
                'id' => $val['id'],
                'grade_id' => 0,
                'wid' => $val['wid'],
                'price' => $val['price'],
                'cost' => $val['cost'],
                'zero' => $val['zero'],
                'one' => $val['one'],
                'sec' => $val['sec'],
                'three' => $val['three'],
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at'],
            ];

            $result[$val['id']] = array_merge($temp, $val['gradeTemplate']);
            $result[$val['id']] = $this->handKey($result[$val['id']], 'grade_id');
        }
        return $result;

    }


    /**
     * @param $data
     * @param string $key
     * @return array
     * @author 张永辉 2018年12月07日处理分销
     */
    public function handKey($data, $key = 'id')
    {
        $result = [];
        foreach ($data as $val) {
            $result[$val[$key]] = $val;
        }
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170606
     * @desc 确认收货分销佣金到账
     * @param $id
     * @update 何书哲 2018年11月6日 佣金到账提醒
     */
    public function getMoney($id)
    {
        $result = [
            'success' => 0,
            'message' => '',
        ];
        $income = Income::where('oid', $id)->where('status', 0)->get()->toArray();
        if (!$income) {
            $result['success'] = 1;
            $result['message'] = '不存在分销数据';
            return $result;
        }
        $order = (new \App\S\Order\OrderService())->getRowByWhere(['id' => $id]);
        DB::beginTransaction();
        $memberService = new MemberService();
        foreach ($income as $val) {
            $res = $memberService->increment($val['mid'], 'cash', $val['money']);
            $memberService->increment($val['mid'], 'total_cash', $val['money']);
            if (!$res) {
                DB::rollBack();
                $result['message'] = '操作失败';
                return $result;
            }
            //todo 何书哲 2018年11月6日 佣金到账提醒
            if (empty($order)) {
                continue;
            }
            $commissionData = [
                'mid' => $val['mid'],
                'money' => $val['money'],
                'commission_type' => 'commission_account'
            ];
            $order['source'] == 0 && (new MessagePushModule($order['wid'], MessagesPushService::CommissionGrant))->sendMsg($commissionData);
            $order['source'] == 1 && (new MessagePushModule($order['wid'], MessagesPushService::CommissionGrant, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg($commissionData, $order['xcx_config_id']);
        }
        $res = Income::where('oid', $id)->where('status', 0)->update(['status' => 1]);
        if (!$res) {
            DB::rollBack();
            $result['message'] = '操作失败';
            return $result;
        }
        DB::commit();
        $result['success'] = 1;
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20140606
     * @desc 取消订单佣金流失
     * @param $id
     */
    public function lossIncome($id)
    {
        return Income::where('oid', $id)->where('status', 0)->update(['status' => -1]);
    }

    /**
     * 根据购物车获取运费
     * @param $cartID int 购物车ID
     * @return float 运费金额
     */
    public function getFreightByCartID($cartID, $wid, $mid, $umid, $address_id = 0)
    {
        //获取购物车信息
        $cartService = new CartService();
        $cart = $cartService->init('mid', $mid)->getInfo($cartID);
        if (empty($cart)) {
            return 0.00;
        }

        //商品
        $product = ProductService::getDetail($cart['product_id']);
        if (empty($product)) {
            return 0.00;
        }
        if ($product['freight_type'] == 1) {
            //统一运费
            return $product['freight_price'] > 0 ? $product['freight_price'] : 0.00;
        } elseif ($product['freight_type'] == 2) {
            //运费模板
            $freightTpl = (new FreightService())->init('wid', $wid)->getInfo($product['freight_id']);
            if (empty($freightTpl)) {
                return 0.00;
            }

            //获取当前收货地址
            if (empty($umid)) {
                //小程序
                $where['mid'] = $mid;
                if ($address_id > 0) {
                    $where['id'] = $address_id;
                } else {
                    $where['type'] = 1;
                }
            } else {
                //公众号
                $where['umid'] = session('umid');
                if ($address_id > 0) {
                    $where['id'] = $address_id;
                } else {
                    $where['type'] = 1;
                }
            }
            list($address) = MemberAddressService::init()->where($where)->getList(false);
            if (empty($address['data'])) {
                return 0.00;
            }

            //当前收货地区信息
            $provinceID = $address['data'][0]['province_id'];
            $cityID = $address['data'][0]['city_id'];
            $areaID = $address['data'][0]['area_id'];
            $rule = json_decode($freightTpl['delivery_rule'], true);

            //获取默认规则
            $defaultRule = [];
            foreach ($rule as $v) {
                if (count($v['regions']) == 1 && $v['regions'][0] == 0) {
                    //默认配置规则
                    $defaultRule = $v;
                    break;
                }
            }

            //获取当前收货地址所属规则
            $bestRule = [];
            foreach ($rule as $v) {
                //地区数组
                $isThisRule = false;
                //自定义省市区规则
                foreach ($v['regions'] as $id) {
                    if ($id) {
                        if (in_array($id, [$provinceID, $cityID, $areaID])) {
                            //如果该规则直接匹配到当前收货地址
                            $isThisRule = true;
                            break;
                        } elseif (in_array($id, $this->getAllRegionsByRegionID($id))) {
                            //如果该规则匹配到当前收货地址所有下级地址
                            $isThisRule = true;
                            break;
                        }
                    }
                }
                if ($isThisRule) {
                    //当前地址属于该规则
                    $bestRule = $v;
                    break;
                }
            }

            //最终规则 如果属于指定规则 则按照指定规则计算 否则按默认规则计算
            $finalRule = $bestRule ? $bestRule : $defaultRule;

            //计算具体运费
            $numOrWeight = 0;
            if ($freightTpl['billing_type'] == 0) {
                //按件
                $numOrWeight = $cart['num'];
            } else {
                //按重量
                //先取当前规格重量
                $propWeight = 0;
                if (!empty($product['productProp'])) {
                    foreach ($product['productProp'] as $prop) {
                        if ($prop['id'] == $cart['prop_id']) {
                            $propWeight = $prop['weight'];
                            break;
                        }
                    }
                }
                $numOrWeight = $propWeight;
            }

            //首件(重)续件(重) 如果没填 设置默认值为1
            $finalRule['first_amount'] = $finalRule['first_amount'] ?: 1;
            $finalRule['additional_amount'] = $finalRule['additional_amount'] ?: 1;

            //最终计算
            if ($numOrWeight <= $finalRule['first_amount']) {
                return $finalRule['first_fee'] > 0 ? $finalRule['first_fee'] : 0.00;
            } else {
                $freight = $finalRule['first_fee'] + (ceil(($cart['num'] - $finalRule['first_amount']) / $finalRule['additional_amount']) * $finalRule['additional_fee']);
                return $freight > 0 ? $freight : 0.00;
            }

        }

        return 0.00;
    }

    /**
     * 根据地区id获取所有下级地区数组
     * @param int $id 地区id
     * @return array
     */
    private function getAllRegionsByRegionID($id)
    {
        $arr = [];
        //获取当前地区信息
        $regionService = new RegionService();
        $region = $regionService->getRowById($id);
        if (empty($region)) {
            return $arr;
        }

        //不包括当前级
        //$arr[] = $id;

        //获取相关下级
        $regions = $regionService->getAll();
        foreach ($regions as $value) {
            $regionList[$value['pid']][] = $value;
        }

        if ($region['level'] == 0) {
            //省级 获取所有市级
            $cities = $regionList[$id];
            foreach ($cities as $city) {
                $arr[] = $city['id'];
                //市级 获取所有区级
                if (!isset($regionList[$city['id']])) continue;
                $areas = $regionList[$city['id']];
                foreach ($areas as $area) {
                    $arr[] = $area['id'];
                }
            }
        } elseif ($region['level'] == 1) {
            //市级 获取所有区级
            $areas = $regionList[$region['id']];
            foreach ($areas as $area) {
                $arr[] = $area['id'];
            }
        }

        return $arr;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170710
     * @desc 获取收货地址
     * @return \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @update 何书哲 2018年7月30日 获取mid，如果不存在，从session里面去取
     */
    public function getDeliveryAddress($umid, $addressId = 0)
    {
        $where = [];
        if ($addressId != 0) {
            $where['id'] = $addressId;
        } else {
            $where['type'] = 1;
        }
        if (empty($umid)) {
            //小程序
            $where['mid'] = app('request')->input('mid');
            if (empty($where['mid'])) {
                //何书哲 2018年7月30日 获取mid，如果不存在，从session里面去取
                $where['mid'] = session('mid');
            }
        } else {
            //公众号
            $where['umid'] = $umid;
        }
        $userAddress = MemberAddress::wheres($where)->first();
        if (!$userAddress) {
            return [];
        } else {
            $userAddress = $userAddress->load('province')->load('city')->load('area')->toArray();
            //收货地址详细信息
            $result['address'] = $userAddress['province']['title'];
            $result['address'] .= $userAddress['city']['title'];
            $result['address'] .= $userAddress['area']['title'];
            $result['address'] .= $userAddress['address'];
            $result['phone'] = $userAddress['phone'];
            $result['name'] = $userAddress['title'];
            $result['areaId'] = $userAddress['area_id'];

            //返回省市区名 Herry 20171107
            $result['province'] = $userAddress['province']['title'];
            $result['city'] = $userAddress['city']['title'];
            $result['area'] = $userAddress['area']['title'];
            $result['address_id'] = $userAddress['id'];
            return $result;
        }

    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170710
     * @desc
     */
    public function createOrderDetail($order, $cartData)
    {
        $orderDetailData['oid'] = $order['id'];
        $orderDetailData['product_id'] = $cartData['product_id'];
        $orderDetailData['title'] = $cartData['title'];
        $orderDetailData['img'] = $cartData['img'];
        $orderDetailData['oprice'] = $cartData['oprice'];
        $orderDetailData['price'] = $cartData['price'];
        $orderDetailData['num'] = $cartData['num'];
        $orderDetailData['after_discount_price'] = $order['products_price'] - $order['head_discount'];
        if ($cartData['is_prop']) {
//            $orderDetailData['spec'] = $cartData['prop1'].':'.$cartData['prop_value1'].','.$cartData['prop2'].":".$cartData['prop_value2'].','.$cartData['prop3'].":".$cartData['prop_value3'];
            $orderDetailData['spec'] = '';
            if ($cartData['prop1']) {
                $orderDetailData['spec'] = $cartData['prop1'] . ':' . $cartData['prop_value1'];
            }
            if ($cartData['prop2']) {
                $orderDetailData['spec'] = $orderDetailData['spec'] . ',' . $cartData['prop2'] . ":" . $cartData['prop_value2'];
            }
            if ($cartData['prop3']) {
                $orderDetailData['spec'] = $orderDetailData['spec'] . ',' . $cartData['prop3'] . ":" . $cartData['prop_value3'];
            }
            $orderDetailData['product_prop_id'] = $cartData['prop_id'];
        }
        $id = OrderDetail::insertGetId($orderDetailData);
        if ($id) {
            $orderDetailData = OrderDetail::find($id)->toArray();
            newOrderDetailService::init()->addR($orderDetailData, false);
        }
        return $orderDetailData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     */
    public function addOrderLog($id, $wid, $mid)
    {
        $orderLogData = [
            'oid' => $id,
            'wid' => $wid,
            'mid' => $mid,
            'action' => 1,
            'remark' => '创建订单',
        ];

        $id = OrderLog::insertGetId($orderLogData);
        $orderLogData['id'] = $id;
        OrLogService::init()->addR($orderLogData, false);
        return $orderLogData;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170808
     * @desc 根据订单id获取订单信息
     * @param $id
     * @return $result = [
     *  'success'   => 0 //错误返回0，正确返回1
     *  'message'   =>'错误信息'，
     *  'data'      =>[] //返回数据
     * ]
     * @update 梅杰 增加小程序配置ID返回
     * @update 梅杰 2018年8月10日 增加收货地址返回
     */
    public function getOrderInfo($id)
    {
        $result = ['success' => 0, 'message' => '', 'data' => []];
        $field = ['id', 'oid', 'mid', 'wid', 'xcx_config_id', 'trade_id', 'pay_price', 'products_price', 'freight_price', 'address_detail', 'created_at', 'prepay_id', 'type', 'form_id', 'status', 'groups_id', 'source', 'cash_fee', 'groups_status', 'serial_id', 'pay_way', 'address_detail', 'address_name', 'address_phone'];
        $orderData = $this->init()->model->select($field)->find($id);
        if (!$orderData) {
            $result['message'] = '订单id不存在';
            return $result;
        }
        $orderData = $orderData->toArray();
        $orderDetailData = newOrderDetailService::init()->model->where('oid', $orderData['id'])->get(['id', 'product_id', 'title', 'num', 'spec'])->toArray();
        if (!$orderDetailData) {
            $result['message'] = '订单数据错误';
            return $result;
        }
        $orderData['orderDetail'] = $orderDetailData;
        $memberService = new MemberService();
        $orderData['member'] = $memberService->getRowById($orderData['mid']);
        $result['success'] = 1;
        $result['data'] = $orderData;
        return $result;
    }

    /**
     * 创建核销码 wuxiaoping
     * 默认长度是前缀+8位，最长13位
     * @return [type] [description]
     */
    public function createCode($length = 8)
    {
        $code = uniqid();
        $code = str_shuffle($code);
        $code = 'hx' . substr($code, 0, $length);
        return $code;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171026
     * @desc 获取待成团订单数量
     */
    public function getStayGroupOrder($mid)
    {
        return $this->model->where('status', 1)->where('mid', $mid)->where('groups_status', 1)->count();
    }


    /**
     * 导出excel表格
     * @param $data array 导出的列表
     * @param $type string 导出类型
     * @author 付国维
     * @since 2017/03/06 14:30
     */
    public function exportExcel1($data = [], $type = 'order')
    {
        $excelObj = new PHPExcel();
        //设置基本信息
        $excelObj->getProperties()
            ->setCreator("hs")
            ->setLastModifiedBy("hs")
            ->setTitle("导出订单")
            ->setSubject("导出订单")
            ->setDescription("导出订单")
            ->setKeywords("导出订单")
            ->setCategory("result file");
        //设置单元格宽度
        if ($type == 'order') {
            $excelObj->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $excelObj->getActiveSheet()->getColumnDimension('F')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('G')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('H')->setWidth(10);
            $excelObj->getActiveSheet()->getColumnDimension('I')->setWidth(30);
            $excelObj->getActiveSheet()->getColumnDimension('J')->setWidth(10);

            //标题行
            $excelObj->setActiveSheetIndex()
                ->setCellValue('A1', '业务单号')
                ->setCellValue('B1', '收件人姓名')
                ->setCellValue('C1', '收件人手机')
                ->setCellValue('D1', '收件省')
                ->setCellValue('E1', '收件市')
                ->setCellValue('F1', '收件区/县')
                ->setCellValue('G1', '收件人地址')
                ->setCellValue('H1', '品名')
                ->setCellValue('I1', '数量')
                ->setCellValue('J1', '备注');
            foreach ($data as $k => $v) {
                //剔除虚拟订单
                if (isset($v['type']) && $v['type'] == 12) {
                    continue;
                }
                //dd($data);
                $num = $k + 2;
                //Excel数据填充
                $excelObj->setActiveSheetIndex()
                    ->setCellValue('A' . $num, '123452133')
                    ->setCellValue('B' . $num, $v['address_name'])
                    ->setCellValue('C' . $num, $v['address_phone'])
                    ->setCellValue('D' . $num, $v['address_province'])
                    ->setCellValue('E' . $num, $v['address_city'])
                    ->setCellValue('F' . $num, $v['address_area'])
                    ->setCellValue('G' . $num, $v['address_detail'])
                    ->setCellValue('J' . $num, $v['buy_remark']);
                //dd($v['orderDetail']);
                $title = '';
                foreach ($v['orderDetail'] as $key => $val) {

                    $title .= $val['title'] . "    " . $val['spec'] . "   ";

                    $excelObj->setActiveSheetIndex()
                        ->setCellValue('H' . $num, $title)
                        ->setCellValue('I' . $num, $val['num']);
                    //dd($val);
                }

            }
            //准备导出
            $excelObj->setActiveSheetIndex();
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="订单报表_' . time() . '.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($excelObj, 'Excel5');
            $objWriter->save('php://output');
        }
    }


    /**
     * @author fuguowei
     * @date 20171113
     * @desc .csv格式导出
     */
    public function export_csv($filename, $data)
    {
        //var_dump($filename);die;
        $data = mb_convert_encoding($data, 'gb2312', 'utf-8');
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=" . $filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $data;
        exit();
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171116
     * @desc 获取用户购买信息
     * @param $mid
     */
    public function getMemberBuyData($mid)
    {
        $sql = 'SELECT COUNT(id) as num,SUM(pay_price) as amount FROM ds_order WHERE mid=' . $mid . ' AND `status` in(1,2,3,7)';
        $res = DB::select($sql);
        $result = json_decode(json_encode($res), true);
        return current($result);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171130
     * @desc 分销订单确认收货15天后佣金到账
     */
    public function autoDistributeOrder()
    {
        $ids = (new IncomeService())->getOrderIds();
        if (!$ids) {
            return;
        }
        $resultids = $this->getVirtualOrder($ids);
        $ids = $this->getIncomeOrderId($ids);
        $ids = array_merge($resultids, $ids);
        if (!$ids) {
            return;
        }
        $ids = array_unique($ids);
        \Log::info('分销佣金到账订单号：');
        \Log::info($ids);
        foreach ($ids as $val) {
            $res = $this->getMoney($val);
            \Log::info('分销到账：');
            \Log::info([$val, $res]);
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date ${DATE}
     * @desc
     * @param $ids
     * @return array|string
     * @upate 张永辉 2018年10月8日 确认收货之后7天佣金到账
     */
    public function getIncomeOrderId($ids)
    {
        $ids = implode(',', $ids);
        $time = date('Y-m-d H:i:s', strtotime('-7 day'));
        $sql = 'SELECT o.id FROM ds_order as o LEFT JOIN ds_order_logs as ol ON o.id=ol.oid WHERE o.id in(' . $ids . ') AND o.refund_status=0 AND `status`=3 AND ol.created_at<"' . $time . '" AND ol.action in(4,11)';
        $res = DB::select($sql);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $ids = array_unique(array_map('array_shift', $res));
        } else {
            $ids = [];
        }
        return $ids;
    }


    /**
     * 获取虚拟订单，
     * @param $ids
     * @author 张永辉 2019年4月18日
     */
    public function getVirtualOrder($ids)
    {
        $data = Order::whereIn('id', $ids)->where('type', '12')->where('status', '3')->where('refund_status', '0')->get(['id'])->toArray();
        if (!$data) {
            return [];
        }
        $resultIds = array_column($data, 'id');
        return $resultIds;
    }


    /**
     * @author fuguowei
     * @desc   订单列表查看待评价状态数量
     */
    public function finishStatus($wid, $id)
    {
        $where['wid'] = $wid;
        $where['status'] = 3;
        $where['mid'] = $id;
        $count = 0;
        $where['ievaluate'] = 0;
        //全部商品退款完成会关闭订单（status=4）；订单中部分退款完成refund_status也会等于8，但是订单状态不变 20180124
        //$where['refund_status'] = ['not in',[4, 8]];
        $obj = OrderService::init()->model->wheres($where)->get();
        if ($obj) {
            $ob = $obj->toArray();
            $count = count($ob);
        }
        return $count;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171225
     * @desc 获取团规则已购买的数量
     */
    public function getGroupsNum($gids, $mid)
    {
        $sql = 'SELECT SUM(od.num) as num FROM ds_order_detail as od LEFT JOIN ds_order as o ON od.oid = o.id WHERE o.groups_id in(' . implode(',', $gids) . ') AND o.groups_status in(1,2) AND o.`status`<>4 AND o.mid=' . $mid;
        $res = DB::select($sql);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $res = current($res);
            return $res['num'] ?? 0;
        } else {
            $result = 0;
        }
        return $result;
    }

    /**
     * 检查发货状态
     * @param $oid
     * @param $wid
     * @return bool
     */
    public function checkDeliver($oid, $wid)
    {
        //订单中每个商品的退款状态 Herry
        $ids = [];
        $query = $this->init('wid', $wid)->model->find($oid);
        if (!$query) {
            return false;
        }
        $data['order'] = $query->load('orderDetail')->toArray();
        $refundService = new OrderRefundService();
        foreach ($data['order']['orderDetail'] as $k => $detail) {
            $refund = $refundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                $ids[] = $detail['id'];
            }
        }
        $condition = ['oid' => $oid];
        if ($ids) {
            $condition['id'] = ['not in', $ids];
        }
        $orderData = $this->init()->getInfo($oid);
        list($data) = OrderDetailService::init()->where($condition)->getList();
        if (!$data['data']) {
            return true;
        }
        $tempDetail = [];
        foreach ($data['data'] as $val) {
            if ($val['is_delivery'] == 0) {
                $tempDetail[] = $val;
            }
        }

        if (!$tempDetail) {
            $where['id'] = $oid;
            $where['wid'] = $wid;
            $this->init('wid', $wid)->where($where)->update(['status' => 2], false);
            $orderLog = [
                'oid' => $oid,
                'wid' => $wid,
                'mid' => $orderData['mid'],
                'action' => 3,
                'remark' => '商家发货',
            ];
            OrLogService::init()->add($orderLog, false);
            $this->upOrderLog($oid, $wid);
        }

        return true;
    }

    /**
     * 检查评价状态
     */
    public function checkEvaluate($oid, $wid)
    {
        $ids = [];
        $query = $this->init('wid', $wid)->model->find($oid);
        if (!$query) {
            return false;
        }
        $data['order'] = $query->load('orderDetail')->toArray();
        $refundService = new OrderRefundService();
        foreach ($data['order']['orderDetail'] as $k => $detail) {
            $refund = $refundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                $ids[] = $detail['id'];
            }
        }
        $condition = ['oid' => $oid];
        if ($ids) {
            $condition['id'] = ['not in', $ids];
        }
        list($data) = OrderDetailService::init()->where($condition)->getList();
        if (!$data['data']) {
            return true;
        }
        $tempDetail = [];
        foreach ($data['data'] as $val) {
            if ($val['is_evaluate'] == 0) {
                $tempDetail[] = $val;
            }
        }

        if (!$tempDetail) {
            $where['id'] = $oid;
            $where['wid'] = $wid;
            $this->init('wid', $wid)->where($where)->update(['ievaluate' => 1], false);
        }

        return true;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20180316
     * @desc 获取订单今天信息统计
     */
    public function getTodayOrderInfo($wid)
    {
        $now = date('Y-m-d', time());
        $start = $now . ' 00:00:00';
        $end = $now . ' 23:59:59';
        $sql = 'SELECT SUM(pay_price) as amount,COUNT(id) as num FROM ds_order WHERE status>0 AND status<=3 AND wid=' . $wid . ' AND created_at >\'' . $start . '\' AND created_at<\'' . $end . '\'';
        $res = DB::select($sql);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            $res = current($res);
            if (!$res['amount']) {
                $res['amount'] = 0;
            }
            return $res;
        } else {
            $result = ['amount' => 0, 'num' => 0];
            return $result;
        }
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180316
     * @desc根据店铺id统计当前店铺信息
     * @param $wid
     */
    public function getOrderInfoByWid($wid)
    {
        $key = $this->getOrderStatisticsKey($wid);
        $redisClient = (new RedisClient())->getRedisClient();
        $res = $redisClient->GET($key);
        if ($res) {
            return json_decode($res, true);
        }
        $timeout = 600;
        $sql = 'SELECT COUNT(*) as num FROM ds_order WHERE wid=' . $wid . ' AND pay_way<>0 AND `status`>0';
        $res = DB::select($sql);
        $result['orderNum'] = $res[0]->num;
        $sql = 'SELECT SUM(od.num) as num from ds_order_detail as od LEFT JOIN ds_order as o ON od.oid=o.id WHERE o.wid=' . $wid . ' AND o.pay_way<>0 AND o.`status`>0';
        $res = DB::select($sql);
        $result['productNum'] = empty($res[0]->num) ? 0 : $res[0]->num;
        $redisClient->SET($key, json_encode($result));
        $redisClient->EXPIRE($key, $timeout);
        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180319
     * @desc 获取订单统计缓存key
     * @param $wid
     * @return string
     */
    public function getOrderStatisticsKey($wid)
    {
        return 'sellerapp:statistics:' . $wid;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180319
     * @desc 订单状态统计
     */
    public function getOrderStatusData($wid)
    {
//        $where['wid'] = $wid;
//        $where['status'] = 1;
//        $where['groups_status'] = ['in',[0,2]];

        $res = ($this->init()->wheres(['wid' => $wid, 'status' => 1])->count()) - ($this->init()->wheres(['wid' => $wid, 'status' => 1, 'is_hexiao' => 1])->count()) - ($this->init()->wheres(['wid' => $wid, 'status' => 1, 'groups_status' => 1])->count()) - ($this->init()->wheres(['wid' => $wid, 'status' => 1, 'groups_status' => 3])->count());
        return $res;
    }

    /**
     * @author hsz
     * @date 20180321
     * @desc 获取订单信息
     */
    public function getOrderPriceInfo($oid, $wid)
    {
        $where['id'] = $oid;
        $where['wid'] = $wid;
        $res = $this->init()->model->where($where)->get(['id', 'wid', 'pay_price', 'freight_price', 'coupon_price', 'products_price', 'type'])->toArray();
        if ($res) {
            return $res[0];
        } else {
            return false;
        }
    }

    /**
     * @author hsz
     * @date 20180324
     * @desc 获取订单状态
     */
    public function getStatusString($status)
    {
        $statusStr = '';
        switch ($status) {
            case '0':
                $statusStr = '待付款';
                break;
            case '-1':
                $statusStr = '待成团';
                break;
            case '1':
                $statusStr = '待发货';
                break;
            case '5':
                $statusStr = '退款中';
                break;
            case '2':
                $statusStr = '已发货';
                break;
            case '3':
                $statusStr = '已完成';
                break;
            case '4':
                $statusStr = '已关闭';
                break;
        }
        return $statusStr;
    }

    /**
     * @author hsz
     * @date 20180327
     * @desc 获取店铺全部退货地址
     */
    public function getRefundAddress($wid)
    {
        $sql = 'SELECT id,name,mobile,province_id,city_id,area_id,address,is_default FROM ds_weixin_address WHERE wid=? AND (is_default=1 OR (is_default=0 AND type=0))';
        $res = DB::select($sql, [$wid]);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            return $res;
        } else {
            return [];
        }
    }

    /**
     * @author hsz
     * @date 20180327
     * @desc 获取店铺默认退货地址
     */
    public function getDefaultAddress($address_id = 0)
    {
        $sql = 'SELECT id,name,mobile,province_id,city_id,area_id,address FROM ds_weixin_address WHERE id=?';
        $res = DB::select($sql, [$address_id]);
        if ($res) {
            $res = json_decode(json_encode($res), true);
            return $res;
        } else {
            return [];
        }
    }

    /**
     * @author hsz
     * @date 20180329
     * @desc 地址id是否存在
     */
    public function isAddressExist($address_id)
    {
        $sql = 'SELECT * FROM ds_weixin_address WHERE id=?';
        $res = DB::select($sql, [$address_id]);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @author hsz
     * @date 20180329
     * @desc 获取退款流程的时间数组
     */
    public function getRefundMessageDate($refund_id)
    {
        $sql = 'SELECT * FROM ds_order_refund_message WHERE id=? AND status IN (2,5,6,7)';
        $res = DB::select($sql, [$refund_id]);
        return json_decode(json_encode($res), true);
    }

    /**
     * @author hsz
     * @date 20180329
     * @desc 选择订单是否可以发货
     */
    public function checkCanDelivery($oid, $wid, $oidids)
    {
        //订单中每个商品的退款状态
        $ids = [];
        $query = $this->init('wid', $wid)->model->find($oid);
        if (!$query) {
            return false;
        }
        $data['order'] = $query->load('orderDetail')->toArray();
        $refundService = new OrderRefundService();
        foreach ($data['order']['orderDetail'] as $k => $detail) {
            $refund = $refundService->init('oid', $oid)->where(['oid' => $oid, 'pid' => $detail['product_id'], 'prop_id' => $detail['product_prop_id']])->getInfo();
            if ($refund && ($refund['status'] == 4 || $refund['status'] == 8)) {
                $ids[] = $detail['id'];
            }
        }
        $data = OrderDetailService::init()->model->where(['oid' => $oid])->whereNotIn('id', $ids)->get(['id', 'is_delivery']);
        if (!$data) {
            return false;
        }
        $data = $data->toArray();
        $rids = [];
        foreach ($data as $val) {
            if ($val['is_delivery'] == 0) {
                $rids[] = $val['id'];
            }
        }
        return empty(array_diff($oidids, $rids));
    }

    /**
     * @author hsz
     * @date 20180329
     * @desc 获取收支明细
     */
    public function getIncomeAndRefund($wid, $beginTime, $endTime, $offset, $pagesize)
    {
        $sql = 'SELECT dol.oid,dol.action,dol.updated_at,o.pay_price,o.pay_way,dor.amount 
                FROM ds_order_logs dol 
                LEFT JOIN ds_order o ON o.id=dol.oid
                LEFT JOIN ds_order_refund dor ON dor.oid=dol.oid
                WHERE dol.wid=? AND dol.action IN (2,8) 
                AND dol.updated_at >= ? 
                AND dol.updated_at <= ?
                ORDER BY dol.updated_at DESC
                LIMIT ?,?';
        $res = DB::select($sql, [$wid, $beginTime, $endTime, $offset, $pagesize]);
        $data[0] = json_decode(json_encode($res), true);
        $countSql = 'SELECT COUNT(*) as count
                FROM ds_order_logs dol 
                LEFT JOIN ds_order o ON o.id=dol.oid
                LEFT JOIN ds_order_refund dor ON dor.oid=dol.oid
                WHERE dol.wid=? AND dol.action IN (2,8) 
                AND dol.updated_at >= ? 
                AND dol.updated_at <= ?';
        $res = DB::select($countSql, [$wid, $beginTime, $endTime]);
        $data[1] = json_decode(json_encode($res), true);
        return $data;
    }

    /**
     * @author hsz
     * @date 20180324
     * @desc 获取订单状态
     */
    public function getPayWayString($pay_way)
    {
        $str = '';
        switch ($pay_way) {
            case '0':
                $str = '';
                break;
            case '1':
                $str = '微信支付';
                break;
            case '2':
                $str = '支付宝支付';
                break;
            case '3':
                $str = '储值余额支付';
                break;
            case '4':
                $str = '货到付款/到店付款';
                break;
            case '5':
                $str = '找人代付';
                break;
            case '6':
                $str = '领取赠品';
                break;
            case '7':
                $str = '优惠兑换';
                break;
            case '8':
                $str = '银行卡支付';
                break;
            case '9':
                $str = '会员卡支付';
                break;
            case '10':
                $str = '小程序支付';
                break;
        }
        return $str;
    }

    /**
     * 计算订单中商品最终分配到的金额
     */
    private function _getFinalPrice($order_detail_array, $order_detail_current, $order)
    {
        $product_amount = 0.00;
        //商品总价
        $product_amount_total = 0.00;
        foreach ($order_detail_array as $detail) {
            $product_amount_total += ($detail['after_discount_price'] > 0 ? $detail['after_discount_price'] : $detail['price']) * $detail['num'];
        }

        //0元订单
        if ($product_amount_total <= 0) {
            return 0.00;
        }

        //当前商品总价
        $product_amount = ($order_detail_current['after_discount_price'] > 0 ? $order_detail_current['after_discount_price'] : $order_detail_current['price']) * $order_detail_current['num'];
        //商品在订单中的价格比例
        $ratio = $product_amount / $product_amount_total;
        if ($order['coupon_id']) {
            $coupon = (new CouponLogService())->getDetail($order['coupon_id']);
            if ($coupon && $coupon['range_type'] == 1) {
                //指定商品优惠券 只把优惠券金额均摊到指定商品
                //判断订单中哪些商品在优惠券指定商品中
                $coupon_product_amount = 0.00;
                $coupon_product_id_array = explode(',', $coupon['range_value']);
                foreach ($order_detail_array as $detail) {
                    if (in_array($detail['product_id'], $coupon_product_id_array)) {
                        $coupon_product_amount += ($detail['after_discount_price'] > 0 ? $detail['after_discount_price'] : $detail['price']) * $detail['num'];
                    }
                }
                //判断当前商品是否在优惠券指定商品中
                if (in_array($order_detail_current['product_id'], $coupon_product_id_array)) {
                    //在指定商品中 则最终商品金额需要减去分摊到的优惠金额
                    $product_amount -= $order['coupon_price'] * $product_amount / $coupon_product_amount;
                }
            } else {
                //全店铺优惠券
                $product_amount -= $order['coupon_price'] * $ratio;
            }
        }

        //减去积分抵现金额
        $product_amount -= $order['bonus_point_amount'] * $ratio;

        //单价
        $product_amount = sprintf('%.2f', $product_amount);

        return $product_amount;
    }
}
