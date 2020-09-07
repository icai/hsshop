<?php

/**
 * 营销活动-红包
 * @author 许立 2018年07月16日
 */

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Module\BonusModule;
use App\Module\CommonModule;
use App\S\Market\BonusService;
use App\S\WXXCX\WXXCXConfigService;
use Illuminate\Http\Request;
use Validator;

class BonusController extends Controller
{
    /**
     * 构造函数
     * @return $this
     * @author 许立 2018年07月20日
     */
    public function __construct()
    {
        $this->leftNav = 'marketing';
    }

    /**
     * 列表页面
     * @param Request $request 参数类
     * @param string $status 活动状态 future:未开始,on:进行中,end:已结束
     * @return view
     * @author 许立 2018年07月20日
     */
    public function index(Request $request, $status = '')
    {
        // 列表条件
        $where = [
            'wid' => session('wid'),
            'status' => ['<>', 1]
        ];
        $now = date('Y-m-d H:i:s');
        if ($status == 'future') {
            // 未开始
            $where['start_at'] = ['>', $now];
        } else if ($status == 'on') {
            // 进行中
            $where['start_at'] = ['<=', $now];
            $where['end_at'] = ['>', $now];
        } else if ($status == 'end') {
            // 已结束
            $where['end_at'] = ['<=', $now];
        }

        // 活动名检索
        if (!empty($request->input('title'))) {
            $where['title'] = ['like', '%'.$request->input('title').'%'];
        }

        // 列表数据
        $bonus_service = new BonusService();
        list($list, $pageHtml) = $bonus_service->listWithPage($where);

        // 处理列表数据
        $bonuses = (new BonusModule())->dealWithListData($list['data']);

        //判断当前店铺是否授权了小程序
        $lite_app_is_authorized = 0;
        $lite_app_config = (new WXXCXConfigService())->getRow(session('wid'));
        if (empty($lite_app_config['errCode']) && !empty($lite_app_config['data'])) {
            $lite_app_is_authorized = 1;
        }

        return view('merchants.marketing.bonus.index',array(
            'title'    => '红包活动',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'index',
            'bonuses'  => $bonuses,
            'pageHtml' => $pageHtml,
            'tabList'  => $bonus_service->getStatusList(),
            'is_xcx'   => $lite_app_is_authorized
        ));
    }

    /**
     * 添加页面和接口
     * @param Request $request 参数类
     * @return view|json
     * @author 许立 2018年07月20日
     */
    public function add(Request $request)
    {
        $wid = session('wid');
        if ($request->isMethod('post')) {
            // 验证参数
            $input = $request->input();
            $rules = array(
                'title' => 'required|between:1,30',
                'start_at' => 'required',
                'end_at' => 'required',
                'coupon_ids' => 'required',
            );
            $messages = array(
                'title.required' => '请填写活动名称',
                'title.between' => '活动名称最多填写30个字符',
                'start_at.required' => '请填写活动开始时间',
                'end_at.required' => '请填写活动开始时间',
                'coupon_ids.required' => '请选择优惠券',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            // 添加活动
            $input['wid'] = $wid;
            if ((new BonusModule())->addBonus($input)) {
                success('新建活动成功');
            } else {
                error('新建活动失败');
            }
        }

        return view('merchants.marketing.bonus.add',array(
            'title'    => '添加红包活动',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'index',
            'wid'      => $wid
        ));
    }

    /**
     * 编辑页面和接口
     * @param Request $request 参数类
     * @param int $id 活动id
     * @return view|json
     * @author 许立 2018年07月20日
     */
    public function edit(Request $request, $id)
    {
        // 获取参数
        if (empty($id)) {
            error('活动ID不能为空');
        }
        $bonus_module = new BonusModule();
        $wid = session('wid');
        $bonus = $bonus_module->getDetail($id);
        if (empty($bonus)) {
            error('活动不存在');
        }
        // 判断是否是商家自己编辑
        if ($wid != $bonus['wid']) {
            error('只能操作本店铺的活动');
        }
        if ($request->isMethod('post')) {
            // 验证参数
            $input = $request->input();
            $rules = array(
                'title' => 'required|between:1,30',
                'start_at' => 'required',
                'end_at' => 'required',
                'coupon_ids' => 'required',
            );
            $messages = array(
                'title.required' => '请填写活动名称',
                'title.between' => '活动名称最多填写30个字符',
                'start_at.required' => '请填写活动开始时间',
                'end_at.required' => '请填写活动开始时间',
                'coupon_ids.required' => '请选择优惠券',
            );
            $validator = Validator::make($input, $rules, $messages);
            if ( $validator->fails() ) {
                error($validator->errors()->first());
            }
            // 编辑活动
            if ((new BonusModule())->editBonus($id, $input)) {
                success('新建活动成功');
            } else {
                error('新建活动失败');
            }
        }

        return view('merchants.marketing.bonus.edit',array(
            'title'    => '编辑红包活动',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'index',
            'bonus'    => $bonus_module->getDetail($id),
            'wid'      => $wid,
            'id'       => $id
        ));
    }

    /**
     * 删除接口
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年07月20日
     */
    public function delete($id)
    {
        // 获取参数
        if (empty($id)) {
            error('活动ID不能为空');
        }
        $wid = session('wid');
        $bonus = (new BonusModule())->getDetail($id);
        if (empty($bonus)) {
            error('活动不存在');
        }
        // 判断是否是商家自己编辑
        if ($wid != $bonus['wid']) {
            error('只能操作本店铺的活动');
        }
        // 停止活动
        if ((new BonusModule())->deleteBonus($id)) {
            success('删除活动成功');
        } else {
            error('删除活动失败');
        }
    }

    /**
     * 停止接口
     * @param int $id 活动id
     * @return json
     * @author 许立 2018年07月20日
     */
    public function stop($id)
    {
        // 获取参数
        if (empty($id)) {
            error('活动ID不能为空');
        }
        $wid = session('wid');
        $bonus = (new BonusModule())->getDetail($id);
        if (empty($bonus)) {
            error('活动不存在');
        }
        // 判断是否是商家自己编辑
        if ($wid != $bonus['wid']) {
            error('只能操作本店铺的活动');
        }
        // 停止活动
        if ((new BonusModule())->stopBonus($id)) {
            success('停止活动成功');
        } else {
            error('停止活动失败');
        }
    }

    /**
     * 是否有进行中的活动接口
     * @return json
     * @author 许立 2018年07月20日
     * @update 许立 2018年07月27日 增加店铺参数过滤
     */
    public function isOn()
    {
        success('', '', ['is_on' => (new BonusService())->getOn(session('wid')) ? 1 : 0]);
    }

    /**
     * 设置的时间是否不跟现有活动冲突接口
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年07月20日
     * @update 许立 2018年07月27日 增加店铺参数过滤
     * @update 许立 2018年07月31日 店铺id获取修改
     */
    public function isTimeValid(Request $request)
    {
        // 判断参数
        $input = $request->input();
        $start_at = $input['start_at'];
        $end_at = $input['end_at'];
        $bonus_id = $input['bonus_id'];
        if (empty($start_at) || empty($end_at)) {
            error('参数不完整');
        }
        // 判断活动时间是否冲突
        success('', '', ['is_valid' => (new BonusService())->isTimeValid(session('wid'), $start_at, $end_at, $bonus_id) ? 1 : 0]);
    }

    /**
     * 获取微商城红包活动二维码
     * @return string
     * @author 许立 2018年08月07日
     */
    public function qrCode()
    {
        // 跳转到店铺主页
        success('', '', (new CommonModule())->qrCode(session('wid'), config('app.url') . 'shop/index/' . session('wid')));
    }

    /**
     * 下载微商城红包活动二维码
     * @return file
     * @author 许立 2018年08月07日
     */
    public function qrCodeDownload()
    {
        // 跳转到店铺主页
        return (new CommonModule())->qrCodeDownload(session('wid'), config('app.url') . 'shop/index/' . session('wid'));
    }

    /**
     * 生成小程序红包活动二维码
     * @return string
     * @author 许立 2018年08月07日
     */
    public function qrCodeXcx()
    {
        // 跳转到店铺主页
        success('', '', (new CommonModule())->qrCode(session('wid'), 'pages/index/index', 1));
    }

    /**
     * 下载小程序红包活动二维码
     * @return file
     * @author 许立 2018年08月07日
     */
    public function qrCodeDownloadXcx()
    {
        // 跳转到店铺主页
        (new CommonModule())->qrCodeDownload(session('wid'), 'pages/index/index', 1);
    }
}