<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/5/23
 * Time: 15:21
 */

namespace App\Http\Controllers\shop;


use App\Http\Controllers\Controller;
use App\Model\Bank;
use App\Model\CashLog;
use App\Model\DistributeBank;
use App\Model\DistributeGrade;
use App\Model\DistributeGradeTemplate;
use App\Model\Income;
use App\Model\Member;
use App\Module\DistributeModule;
use App\Module\MessagePushModule;
use App\Module\ProductModule;
use App\S\Distribute\DistributeApplayLogService;
use App\S\Distribute\DistributeApplayPageService;
use App\S\MarketTools\MessagesPushService;
use App\S\Member\MemberService;
use App\S\Product\ProductService;
use App\S\Product\ProductSkuService;
use App\Services\CashLogService;
use App\Services\DistributeTemplateService;
use App\Services\Order\OrderService;
use App\Services\WeixinService;
use DB;
use Illuminate\Http\Request;
use Validator;
use App\S\PublicShareService;
use App\S\Weixin\ShopService;

class DistributeController extends Controller
{


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170526
     * @desc 我的财富
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     * @update 许立 2019年05月10日 返回是否有分销商品
     */
    public function wealth(Request $request,DistributeModule $distributeModule)
    {
        $mid      = session('mid');
        $wid      = session('wid');
        $page     = $request->input('page')??1;
        $pagesize = config('database.perPage');
        $offset   = ($page - 1) * $pagesize;
        $cash     = CashLog::where('wid', $wid)->where('mid', $mid)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->toArray();
        if ($request->input('tag') && $request->input('tag') == 1) {
            return mysuccess('操作成功', '', $cash);
        }
        $member             = Member::find($mid)->toArray();
        $member['amount']   = Income::where('mid', $mid)->where('wid', $wid)->where('status', '!=', '-3')->where('status', 0)->sum('money');
        $member['complete'] = CashLog::where('wid', $wid)->where('mid', $mid)->where('status', 2)->sum('money');
        $grade = $distributeModule->getGradeInfo($wid,$member['distribute_grade_id']);

        $where = [
            'wid'                    => $wid,
            'distribute_template_id' => ['!=', '0'],
            'is_distribution'        => '1',
            'status'                 => '1'
        ];

        return view('shop.distribute.wealth', array(
            'title'     => '我的财富',
            'member'    => $member,
            'cash'      => $cash,
            'grade'     => $grade,
            'shareData' => (new PublicShareService())->publicShareSet($wid),
            'isProductEmpty' => (new ProductService())->getCountByWhere($where) ? 0 : 1
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170527
     * @desc  提取金额
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function withdrawal(Request $request, CashLogService $cashLogService, ShopService $shopService)
    {
        $input = $request->input();
        //获取账户可提现金额
        $mid    = session('mid');
        $member = Member::find($mid)->toArray();

        if ($request->isMethod('post')) {
            $rule      = Array(
                'bank_id' => 'required',
                'money'   => 'required',

            );
            $message   = Array(
                'bank_id.required' => '账户ID不能为空',
                'money.required'   => '提现金额不能为空',
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            if ($input['money'] < 0) {
                return myerror('提取金额必须大于0');
            }
            if ($input['money'] > $member['cash']) {
                return myerror('最大提现金额不大于' . $member['cash']);
            }
            //判断账户是否存在
            $res = (new DistributeModule())->withdrawals($mid, session('wid'));
            if ($res['errCode'] != 0) {
                error($res['errMsg']);
            } else {
                success();
            }

        }
        //显示页面
        $bank = [];
        if (isset($input['id'])) {
            $res = DistributeBank::find($input['id']);
        } else {
            $res = DistributeBank::where('mid', session('mid'))->first();
        }
        if ($res) {
            $bank            = $res->toArray();
            $bank['account'] = substr($bank['account'], -4);
        }
        $shopData = $shopService->getRowById(session('wid'));
        return view('shop.distribute.withdrawal', array(
            'title'       => '提取金额',
            'bank'        => $bank,
            'member'      => $member,
            'company_pay' => $shopData['company_pay']?1:0,
            'shareData'   => (new PublicShareService())->publicShareSet(session('wid'))
        ));
    }

    public function selectAccount()
    {
        $mid  = session('mid');
        $bank = DistributeBank::where('mid', $mid)->get()->toArray();
        foreach ($bank as &$val) {
            $val['account'] = substr($val['account'], -4);
        }
        return view('shop.distribute.selectAccount', array(
            'title' => '选择账户',
            'bank'  => $bank,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170527
     * @desc 管理账户
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manageAccount(Request $request)
    {
        $mid  = session('mid');
        $bank = DistributeBank::where('mid', $mid)->get()->toArray();
        foreach ($bank as &$val) {
            $val['account'] = substr($val['account'], -4);
        }
        return view('shop.distribute.manageAccount', array(
            'title' => '管理账户',
            'bank'  => $bank,
        ));
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170527
     * @desc 添加账户
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addAccount(Request $request)
    {
        if ($request->isMethod('post')) {
            $input     = $request->input();
            $rule      = Array(
                'account'   => 'required',
                'bank_name' => 'required',
                'type'      => 'required|in:1,2',
                'name'      => 'required',
                'logo'      => 'required'
            );
            $message   = Array(
                'account.required'   => '账号不能为空',
                'bank_name.required' => '银行名称不能为空',
                'type.required'      => '类型不能为空',
                'logo.required'      => 'logo不能为空'
            );
            $validator = Validator::make($input, $rule, $message);
            if ($validator->fails()) {
                error($validator->errors()->first());
            }
            $bank       = [
                'mid'       => session('mid'),
                'bank_name' => $input['bank_name'],
                'account'   => $input['account'],
                'name'      => $input['name'],
                'type'      => $input['type'],
                'logo'      => $input['logo'],
            ];
            $id         = DistributeBank::insertGetId($bank);
            $bank['id'] = $id;
            return mysuccess('操作成功', '', $bank);
        }

        $bank = Bank::get()->toArray();
        return view('shop.distribute.addAccount', array(
            'title' => '添加账户',
            'bank'  => $bank,
        ));
    }

    public function addAlipay()
    {
        return view('shop.distribute.addAlipay', array(
            'title' => '添加支付宝',
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170527
     * @desc 收益明细
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function earnings(Request $request)
    {
        $page     = $request->input('page')??1;
        $pagesize = config('database.perPage');
        $offset   = ($page - 1) * $pagesize;
        $mid      = session('mid');
        $income   = Income::where('mid', $mid)->where('wid', session('wid'))->where('status', '!=', '-3')->skip($offset)->take($pagesize)->get()->load('order')->toArray();
        if ($page > 1) {
            return mysuccess('操作成功', '', $income);
        }
        return view('shop.distribute.earnings', array(
            'title'  => '收益详情',
            'income' => $income,
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170527
     * @desc 删除账户
     * @param Request $request
     */
    public function delAccount(Request $request)
    {
        $input     = $request->input();
        $rule      = Array(
            'ids' => 'required',
        );
        $message   = Array(
            'ids.required' => '账户ID 不能为空',
        );
        $validator = Validator::make($input, $rule, $message);
        if ($validator->fails()) {
            error($validator->errors()->first());
        }

        if (!is_array($input['ids'])) {
            return myerror('id应该为数组');
        }
        $mid = session('mid');
        $res = DistributeBank::where('mid', $mid)->whereIn('id', $input['ids'])->delete();
        if ($res) {
            return mysuccess();
        } else {
            return myerror();
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date  20171117
     * @desc 取消成为分销客，或不在显示 tag=1 取消，tag=2 不在显示
     * @param Request $request
     */
    public function cancelDistribute(Request $request)
    {
        $tag = $request->input('tag', 1);
        (new DistributeModule())->cancelDistribute(session('mid'), session('wid'), $tag);
        success();
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171117
     * @desc 成为分销客
     * @param Request $request
     * @update 何书哲 2018年11月09日 发送成为分销客消息通知
     */
    public function beDistribute(Request $request, DistributeApplayLogService $distributeApplayLogService,MemberService $memberService)
    {
        $mid        = session('mid');
        $insertData = [
            'wid'    => session('wid'),
            'mid'    => $mid,
            'status' => '3',
        ];
        $data = $memberService->getRowById($mid);
        if ($data['is_distribute'] == '1'){
            error('您已经可以分销了,无需申请');
        }
        $distributeApplayLogService->add($insertData);
        if ($memberService->batchUpdate([$mid], ['is_distribute' => 1])) {
            //何书哲 2018年11月09日 发送成为分销客消息通知
            (new MessagePushModule(session('wid'), MessagesPushService::BecomePromoter))->sendMsg(['mid'=>$mid]);
            (new MessagePushModule(session('wid'), MessagesPushService::BecomePromoter, MessagePushModule::SEND_TARGET_WECHAT_XCX))->sendMsg(['mid'=>$mid]);
            success();
        }
        error();
    }

    /**
     * @author zhouxing
     * @date 20171121
     * @desc 分销客页面
     */
    public function beDistributor(Request $request)
    {
        return view('shop.distribute.beDistributor', [
            'title' => '分销客'
        ]);
    }

    /**
     * @author zhouxing
     * @date 20171121
     * @desc 分销客页面
     */
    public function distributeAgreement(Request $request)
    {
        return view('shop.distribute.distributeAgreement', [
            'title' => '分销协议'
        ]);
    }


    /**
     * 提现记录
     * @author 张永辉 2018年9月6日
     */
//    public function cashLog(Request $request)
//    {
//        $mid = session('mid');
//        $wid = session('wid');
//        if ($request->isMethod('post')) {
//            $page     = $request->input('page')??1;
//            $pagesize = config('database.perPage');
//            $offset   = ($page - 1) * $pagesize;
//            $cash     = CashLog::where('wid', $wid)->where('mid', $mid)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->toArray();
//            success('操作成功', '', $cash);
//        }
//        return view('shop.distribute.cashLog', array(
//            'title' => '提现记录',
//        ));
//    }

    /**
     * 获取分销商品列表
     * @param Request $request
     * @param ProductService $productService
     * @param ProductSkuService $productSkuService
     * @param DistributeTemplateService $distributeTemplateService
     * @author 张永辉 2018年10月8日
     */
    public function productList(Request $request, ProductService $productService, ProductSkuService $productSkuService, DistributeTemplateService $distributeTemplateService,MemberService $memberService,DistributeModule $distributeModule)
    {
        $product = $distributeModule->productList($request,$productService,$productSkuService,$distributeTemplateService,$memberService,session('wid'),session('mid'));

//        $where        = [
//            'wid'                    => session('wid'),
//            'distribute_template_id' => ['!=', '0'],
//            'is_distribution'        => '1',
//            'status'                 => '1'
//        ];
//        $product      = $productService->listWithPage($where);
//        $pids         = array_column($product[0]['data'], 'id');
//        $templateIds  = array_column($product[0]['data'], 'distribute_template_id');
//        $templateData          = $distributeTemplateService->getListByIds($templateIds);
//        $memberData = $memberService->getRowById(session('mid'));
//        $grade_id = $memberData['distribute_grade_id'];
//        $skuData = $productSkuService->getSkuListByPids($pids);
//        foreach ($product[0]['data'] as &$item) {
//            if (empty($skuData[$item['id']])) {
//                $item['distribute_amount'] = round($item['price'] * $templateData[$item['distribute_template_id']][$grade_id]['one'] * 0.01, 2);
//                $item['distribute_amount_sec'] = round($item['price'] * $templateData[$item['distribute_template_id']][$grade_id]['sec'] * 0.01, 2);
//            } else {
//                foreach ($skuData[$item['id']] as &$val){
//                    $val['distribute_amount'] = round($val['price'] * $templateData[$item['distribute_template_id']][$grade_id]['one'] * 0.01, 2);
//                    $val['distribute_amount_sec'] = round($val['price'] * $templateData[$item['distribute_template_id']][$grade_id]['sec'] * 0.01, 2);
//                }
//                $item['skuData'] = $skuData[$item['id']];
//                $item['distribute_amount'] =$skuData[$item['id']][0]['distribute_amount'] ;
//                $item['distribute_amount_sec'] =$skuData[$item['id']][0]['distribute_amount_sec'] ;
//            }
//        }
        success('操作成功', '', $product);
    }


    /**
     * 我的团队
     * @author 张永辉 2018年10月9日
     */
    public function myTeam(Request $request, MemberService $memberService)
    {
        if ($request->input('pid')){
            $sonMenberData = $memberService->getListByConditionWithPage(['pid' => $request->input('pid')]);
            success('','',$sonMenberData);
        }
        $mid           = session('mid');
        $memberData    = $memberService->getRowById($mid);
        $sonMenberData = $memberService->getListByConditionWithPage(['pid' => $mid]);
        success('', '', ['memberData' => $memberData, 'sonMenberData' => $sonMenberData]);
    }


    /**
     * 申请成为分销客
     * @param $id
     * @param DistributeApplayPageService $distributeApplayPageService
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author 张永辉
     */
    public function apply(Request $request, DistributeApplayPageService $distributeApplayPageService, DistributeApplayLogService $distributeApplayLogService, MemberService $memberService, $wid = 0, $id = 0)
    {
        $mid     = session('mid');
        $where   = [
            'mid'    => $mid,
            'status' => '0',
        ];
        $logData = $distributeApplayLogService->getList($where, $skip = "", $perPage = "", $orderBy = "id", $order = "desc");
        $memberData = $memberService->getRowById($mid);
        $logData = current($logData);
        if ($request->isMethod('post')) {
            $logData && error('您已申请过了，等待审核一下吧……');
            if (!empty($memberData['is_distribute'])) {
                error('您已是该店铺的分销客，无需再次申请!');
            }
            $insertData = [
                'wid' => session('wid'),
                'mid' => $mid,
            ];
            $distributeApplayPageService->increment($request->input('id'), 'num', 1);
            $distributeApplayLogService->add($insertData) && success('申请已提交请耐心等待审核！');
            error();
        }
        $data = $distributeApplayPageService->getRowById($id);
        return view('shop.distribute.apply', [
            'title'         => $data['title'],
            'data'          => $data,
            'logdata'       => $logData,
            'is_distribute' => $memberData['is_distribute']??'0'
        ]);
    }


    /**
     * 团购订单
     * @author 张永辉 2018年10月9日
     */
    public function distributeOrder(Request $request, OrderService $orderService, MemberService $memberService)
    {
        $mid        = session('mid');
        $memberData = $memberService->model->where('pid', $mid)->get(['id'])->toArray();
        if (!$memberData) {
            success('操作成功', '', []);
        }
        $mids      = array_column($memberData, 'id');
        $where     = [
            'mid'             => ['in', $mids],
            'distribute_type' => '1',
        ];
        $page      = $request->input('page') ? $request->input('page') : 1;
        $pagesize  = config('database.perPage');
        $offset    = ($page - 1) * $pagesize;
        $orderData = $orderService->init()->model->wheres($where)->orderBy('id', 'desc')->skip($offset)->take($pagesize)->get()->load('orderDetail')->toArray();
        if ($orderData) {
            $oids       = array_column($orderData, 'id');
            $mids       = array_column($orderData, 'mid');
            $memberTmp  = $memberService->getListById($mids);
            $memberData = [];
            foreach ($memberTmp as $val) {
                $memberData[$val['id']] = $val;
            }
            $res        = Income::where('mid', $mid)->whereIn('oid', $oids)->get()->toArray();
            $incomeData = [];
            foreach ($res as $key => $val) {
                $incomeData[$val['oid']] = $val;
            }
            foreach ($orderData as &$item) {
                $item['commission'] = $incomeData[$item['id']]['money']??'0';
                $item['memberData'] = [
                    'nickname'   => $memberData[$item['mid']]['nickname']??'',
                    'headimgurl' => $memberData[$item['mid']]['headimgurl']??'',
                ];
            }
        }

        success('操作成功', '', $orderData);
    }

    /**
     * 佣金记录
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View|\Symfony\Component\HttpFoundation\Response
     * @author 张永辉
     */
    public function incomeLog()
    {
        $mid       = session('mid');
        $startTime = date('Y-m-01 00:00:00', strtotime('-10 month'));
        $income    = Income::where('mid', $mid)->where('wid', session('wid'))->where('status', '!=', '-3')->where('created_at', '>', $startTime)->orderBy('id', 'desc')->get()->load('order')->toArray();
        $result    = [];
        $nowMoth   = date('Y年m月');
        $data = [];
        foreach ($income as $val) {
            $moth            = date('Y年m月', strtotime($val['created_at']));
            $result[$moth][] = $val;
        }

        foreach ($result as $key => $item) {
            if ($key == $nowMoth) {
                $temp['title'] = '本月';
                $temp['data']  = $item;
            } else {
                $temp['title'] = $key;
                $temp['data']  = $item;
            }
            $data[] = $temp;
        }
        success('操作成功', '', $data);
    }


    /**
     * 佣金提现记录
     * @author 张永辉
     */
    public function cashLog()
    {
        $mid       = session('mid');
        $wid       = session('wid');
        $startTime = date('Y-m-01 00:00:00', strtotime('-10 month'));
        $cash      = CashLog::where('wid', $wid)->where('mid', $mid)->where('created_at', '>', $startTime)->orderBy('id', 'desc')->get()->toArray();
        $result    = [];
        $nowMoth   = date('Y年m月');
        foreach ($cash as $val) {
            $moth            = date('Y年m月', strtotime($val['created_at']));
            $result[$moth][] = $val;
        }

        $data = [];
        foreach ($result as $key => $item) {
            if ($key == $nowMoth) {
                $temp['title'] = '本月';
                $temp['data']  = $item;
            } else {
                $temp['title'] = $key;
                $temp['data']  = $item;
            }
            $data[] = $temp;
        }
        success('操作成功', '', $data);

    }




}