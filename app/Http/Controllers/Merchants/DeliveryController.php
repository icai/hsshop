<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Module\StoreModule;
use App\Module\TakeAwayModule;
use App\S\Order\OrderService;
use App\S\Weixin\DeliveryConfigService;
use App\S\Weixin\DeliveryPrinterService;
use Illuminate\Http\Request;
use Validator;

/**
 * 外卖控制器
 * Class DeliveryController
 * @package App\Http\Controllers\Merchants
 * @author 何书哲 2018年11月14日
 */
class DeliveryController extends Controller
{
    /**
     * 构造函数
     * DeliveryController constructor.
     */
    public function __construct()
    {

    }

    /**
     * 小票打印机列表
     * @param DeliveryPrinterService $deliveryPrinterService 小票打印机service
     * @return view
     * @author 何书哲 2018年11月14日
     */
    public function printerList(DeliveryPrinterService $deliveryPrinterService)
    {
        $wid = session('wid');
        $printList = $deliveryPrinterService->getList(['wid' => $wid]);
        foreach ($printList as &$printer) {
            $printer['printer_status'] = '未连接';
            if ($printer['is_on'] == 1) {
                $printerStatus = json_decode((new TakeAwayModule($wid))->queryPrinterStatus(), true);
                $printer['printer_status'] = $printerStatus['msg'];
            }
        }
        return view('merchants.currency.printerList', [
            'title' => '外卖设置',
            'leftNav' => 'currency',
            'slidebar' => 'printerList',
            'printList' => json_encode($printList)
        ]);
    }

    /**
     * 添加/编辑小票打印机
     * @param Request $request 请求参数
     * @param DeliveryPrinterService $deliveryPrinterService 小票打印机service
     * @author 何书哲 2018年11月14日
     */
    public function addPrinter(Request $request, DeliveryPrinterService $deliveryPrinterService)
    {
        $wid = session('wid');
        $input = $request->only(['id', 'device_brand', 'device_name', 'device_no', 'key', 'times']);
        $input['device_brand'] = trim($input['device_brand']);
        $input['device_name'] = trim($input['device_name']);
        $input['device_no'] = trim($input['device_no']);
        $input['key'] = trim($input['key']);
        $rule = [
            'device_name' => 'required',
            'device_no' => 'required',
            'key' => 'required',
            'times' => 'required|integer'
        ];
        $message = [
            'device_name.required' => '请输入设备名称',
            'device_no.required' => '请输入设备号码',
            'key.required' => '请选择设备密钥',
            'times.required' => '请选择打印联数',
            'times.integer' => '打印联数必须是整数',
        ];
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        if ($id = $input['id']) {
            unset($input['id']);
            if ($deliveryPrinterService->update($id, $input)) {
                success('设置成功');
            }
            error('设置失败');
        } else {
            $input['wid'] = $wid;
            $input['is_on'] = $deliveryPrinterService->checkIfExists(['wid' => $wid, 'is_on' => 1]) ? 0 : 1;
            if ($deliveryPrinterService->add($input)) {
                success('添加成功');
            }
            error('添加失败');
        }

    }

    public function queryPrinter(Request $request, DeliveryPrinterService $deliveryPrinterService)
    {
        $printerData = $deliveryPrinterService->getRowById($request->input('id'));
        success('', '', $printerData);
    }

    /**
     * 连接/断开打印机
     * @param Request $request 请求参数
     * @param DeliveryPrinterService $deliveryPrinterService 小票打印机service
     * @author 何书哲 2018年11月14日
     */
    public function setPrinter(Request $request, DeliveryPrinterService $deliveryPrinterService)
    {
        $wid = session('wid');
        $input = $request->only(['type', 'printer_id']);
        $rule = [
            'type' => 'required',
            'printer_id' => 'required|integer',
        ];
        $message = [
            'type.required' => '请传入类型参数',
            'printer_id.required' => '请输入打印机id',
            'printer_id.integer' => '打印机id必须是整数',
        ];
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        $printerData = $deliveryPrinterService->getRowById($input['printer_id']);
        empty($printerData) && error('该打印机不存在');

        if ($input['type'] == 'on') {
            $printerData['is_on'] == 1 && error('打印机已选用');
            $deliveryPrinterService->checkIfExists(['wid' => $wid, 'is_on' => 1]) && error('其它打印机已选用，不能同时选用两台打印机');
            $deliveryPrinterService->update($input['printer_id'], ['is_on' => 1]) ? success('选用成功') : error('选用失败');
        } elseif ($input['type'] == 'off') {
            $printerData['is_on'] == 0 && error('打印机已弃用');
            $deliveryPrinterService->update($input['printer_id'], ['is_on' => 0]) ? success('弃用成功') : error('弃用失败');
        } else {
            error('类型参数错误');
        }

    }

    /**
     * 删除打印机
     * @param Request $request 请求参数
     * @param DeliveryPrinterService $deliveryPrinterService 小票打印机service
     * @author 何书哲 2018年11月14日
     */
    public function delPrinter(Request $request, DeliveryPrinterService $deliveryPrinterService)
    {
        $printer_id = $request->input('printer_id', 0);
        $printerData = $deliveryPrinterService->getRowById($printer_id);
        empty($printerData) && error('该打印机不存在');
        $deliveryPrinterService->del($printer_id) ? success('删除成功') : error('删除失败');
    }

    /**
     * 获取/添加外卖订单配置
     * @param Request $request 请求参数
     * @param DeliveryConfigService $deliveryConfigService 外卖订单配置service
     * @return view
     * @author 何书哲 2018年11月15日
     * @update 何书哲 2018年11月21日 若无添加打印机，无法保存
     */
    public function deliveryConfig(Request $request, DeliveryConfigService $deliveryConfigService, DeliveryPrinterService $deliveryPrinterService)
    {
        $wid = session('wid');
        $configData = (new StoreModule())->getDeliveryConfig(['wid' => $wid]);

        if ($request->isMethod('POST')) {
            //切换按钮若有待完成订单，无法保存
            $order = (new OrderService())->model->whereNotIn('status', [3, 4])->where('wid', $wid)->first();
            !empty($order) && error('存在未完成的订单，请及时处理对应订单后再设置');

            $input = $request->only(['work_days', 'delivery_times', 'is_on', 'unpay_min', 'delivery_hour']);
            $rule = [
                'is_on' => 'integer|size:1',
                'work_days' => 'required|array',
                'delivery_times' => 'required|array|max:3',
                'delivery_times.*.startTime' => 'required',
                'delivery_times.*.endTime' => 'required',
                'unpay_min' => 'required|integer',
                'delivery_hour' => 'required|integer'
            ];
            $message = [
                'is_on.integer' => '请选择是否开启开关',
                'is_on.size' => '请开启开关',
                'work_days.required' => '请选择工作日',
                'work_days.array' => '工作日必须是数组',
                'delivery_times.required' => '请添加外卖时间段',
                'delivery_times.array' => '外卖时间段必须是数组',
                'delivery_times.max' => '外卖时间段最多允许3个',
                'delivery_times.*.startTime.required' => '外卖时间段开始时间不能为空',
                'delivery_times.*.endTime.required' => '外卖时间段结束时间不能为空',
                'unpay_min.required' => '请填写未付款自动取消时间',
                'unpay_min.integer' => '未付款自动取消时间必须是整数',
                'delivery_hour.required' => '请填写发货后自动收货时间',
                'delivery_hour.integer' => '发货后自动收货时间必须是整数'
            ];
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }

            $input['wid'] = $wid;
            $input['work_days'] = implode(',', $input['work_days']);
            $input['delivery_times'] = json_encode($input['delivery_times'], JSON_UNESCAPED_UNICODE);

            !empty($configData['id']) && ($res = $deliveryConfigService->update($configData['id'], $input));
            empty($configData['id']) && ($res = $deliveryConfigService->add($input));
            $res ? success() : error();
        }

        return view('merchants.currency.deliveryConfig', [
            'title' => '外卖设置',
            'leftNav' => 'currency',
            'slidebar' => 'deliveryConfig',
            'configData' => $configData,
            'is_set' => $deliveryPrinterService->getRowByWhere(['wid' => $wid]) ? 1 : 0
        ]);
    }

    /**
     * 开启/关闭外卖订单配置按钮
     * @param Request $request 请求参数
     * @param DeliveryConfigService $deliveryConfigService 外卖订单配置service
     * @author 何书哲 2018年11月15日
     */
    public function changeConfigStatus(Request $request, DeliveryConfigService $deliveryConfigService)
    {
        $wid = session('wid');
        $configData = $deliveryConfigService->getRowByWhere(['wid' => $wid]);
        empty($configData) && error('店铺未配置外卖订单设置，如需要，请先配置');

        //如果要改变按钮状态，必须所有的订单的状态是已关闭或已完成
        $order = (new OrderService())->model->whereNotIn('status', [3, 4])->where('wid', $wid)->first();
        !empty($order) && error('存在未完成的订单，暂时无法切换按钮状态');

        $status = $configData['is_on'] == 1 ? 0 : 1;
        if ($deliveryConfigService->update($configData['id'], ['is_on' => $status])) {
            success();
        }
        error();
    }

    /**
     * 查询订单是否打印成功
     * @param Request $request 请求参数
     * @param $orderIndex 订单索引，由导入后取得
     * @author 何书哲 2018年11月19日
     */
    public function queryOrder(Request $request, $orderIndex)
    {
        empty($orderIndex) && error('请传递正确参数');
        $queryRes = (new TakeAwayModule(session('wid')))->queryOrder($orderIndex);
        success('', '', $queryRes);
    }

    /**
     * 查询打印机的状态
     * @param Request $request 请求参数
     * @author 何书哲 2018年11月19日
     */
    public function queryPrinterStatus(Request $request)
    {
        $queryRes = (new TakeAwayModule(session('wid')))->queryPrinterStatus();
        success('', '', $queryRes);
    }


}