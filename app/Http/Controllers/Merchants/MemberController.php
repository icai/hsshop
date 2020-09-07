<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Jobs\SendRechargeBalanceLog;
use App\Model\Weixin;
use App\Module\MemberCardModule;
use App\Module\MemberModule;
use App\S\Foundation\RegionService;
use App\S\Market\CouponService;
use App\S\Member\MemberImportService;
use App\S\Member\MemberService;
use App\S\ShareEvent\LiRegisterService;
use App\S\Wechat\WeChatShopConfService;
use App\Services\MemberFans;
use App\Services\PointRecordService;
use App\Services\Wechat\ApiService;
use App\Services\WeixinConfigSubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;
use MemberCardRecordService;
use MemberCardService;
use MemberCustomeService;
use MemberFansService;
use MemberLabelService;
use QrCode;
use QrCodeService;
use RedisPagination;
use response;
use Validator;
use WeixinService;
use App\S\BalanceLogService;
use App\S\BalanceRuleService;
use App\Module\LiShareEventModule;
use App\S\Weixin\ShopService;

class MemberController extends Controller
{
    /**
     * @return void
     */

   protected $weixinInfo;

    public function __construct(Request $request, MemberImportService $memberImportService)
    {
        $this->leftNav = 'member';
        $this->memberImportService = $memberImportService;
    }

    /**
     * 客户概况
     * @return [type] [description]
     */
    public function index(Request $request)
    {
        return redirect('/merchants/member/customer');
        $wid = $request->session()->get('wid');
        $yesterday = date('Y-m-d 00:00:00',time()-24*3600);
        $yesterdayTime = strtotime($yesterday);
        $where = array(
            ['created_at', '<=', $yesterdayTime],
            ['wid','=',$wid]
            );
        $count = Db::table('member_fans')->where($where)->count();
        $fans_dashboard = Db::table('member_dashboard')->where('wid',$wid)->first();
        $fans_dashboard = $fans_dashboard?$fans_dashboard:array();
        return view('merchants.member.index',array(
            'title'=>'客户概况',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'index',
            'count' =>$count,
            'fans_dashboard' =>$fans_dashboard
            ));
    }

    /**
     * 客户管理
     *
     * @param  Request $request [http请求类]
     * @return view
     *
     * @author 黄东 406764368@qq.com
     * @version 2017年3月5日 17:22:32
     * @update 梅杰 2018年9月7日增加 筛选条件 ：购次 消费金额 时间
     */
    public function customer( Request $request, MemberService $memberService ) {
        // 店铺id
        $wid = session('wid');
        // 添加客户
        if ( $request->isMethod('post') ) {
            // 接收参数
            $input = $request->only(['truename','nickname', 'mobile', 'wechat_id', 'remark']);
            // 构建验证规则和错误信息
            list($rules, $messages) = MemberService::buildVerify(['truename','nickname', 'mobile', 'wechat_id', 'remark']);
            // 调用验证器执行验证方法
            $validator = Validator::make($input, $rules, $messages);
            // 验证不通过则提示错误信息
            if ( $validator->fails() ) {
                error( $validator->errors()->first() );
            }
            // 插入数据
            $input['source'] = 5;
            $input['wid'] = $wid;
            $memberService->add($input);
            return mysuccess();

        }

        // 获取固定数据数组
        list($sourceList, $buyNumList) = MemberService::getStaticList();
        $where = $request->input();
        $where['wid'] = $wid;

        if ($amount = $request->input(['amount'],0)) {

            switch ($amount) {
                case 1 :
                    $where['amount'] = ['<',100];
                    break;
                case 2:
                    $where['amount'] = ['between',[100,500]];
                    break;
                case 3:
                    $where['amount'] = ['between',[500,1000]];
                    break;
                case 4:
                    $where['amount'] = ['between',[1000,2000]];
                    break;
                case 5:
                    $where['amount'] = ['>',2000];
                    break;
                default:
                    break;
            }
        }

        // 查询数据并分页
        list($list, $pageHtml) = $memberService->getListByConditionWithPage($where);
        return view('merchants.member.customer',array(
            'title'      => '客户概况',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'customer',
            'list'       => $list['data'],
            'pageHtml'   => $pageHtml,
            'sourceList' => $sourceList,
            'buyNumList' => $buyNumList,
            'total'      => $list['total']
        ));
    }

    /**
     * 会员管理
     *
     * @param  Request $request [http请求类]
     * @return view
     *
     * @author 吴晓平
     * @update 梅杰 2018年9月7日增加 筛选条件 ：购次 消费金额 时间
     */
    public function members( Request $request) {
        // 店铺id
        $wid = session('wid');
        $where['wid'] = $wid;
        ($mobile = $request->input(['mobile'],0)) && $where['mobile'] = $mobile;
        ($nickname = $request->input(['nickname'],'')) && $where['nickname'] = ['like',"%{$nickname}%"];
        //购次
        $buyNum = $request->input(['buy_num'],-1);

        if ($buyNum >= 0 && $buyNum !== '') {
            $where['buy_num'] = $buyNum === "0"  ? $buyNum : ['>',$buyNum];
        }

        if ($card_id = $request->input('card_id',0)) {
            $where['card_id'] = $card_id;
        }
        //消费金额
        if ($amount = $request->input(['amount'],0)) {

            switch ($amount) {
                case 1 :
                    $where['amount'] = ['<',100];
                    break;
                case 2:
                    $where['amount'] = ['between',[100,500]];
                    break;
                case 3:
                    $where['amount'] = ['between',[500,1000]];
                    break;
                case 4:
                    $where['amount'] = ['between',[1000,2000]];
                    break;
                case 5:
                    $where['amount'] = ['>',2000];
                    break;
                default:
                    break;
            }
        }

        //增加按最近访问时间排序
        if (isset($input['latest_visit_time_start']) && isset($input['latest_visit_time_end'])) {
            $start = strtotime($input['latest_visit_time_start']);
            $end = $input['latest_visit_time_start'] == $input['latest_visit_time_end'] ? $start + 86400 : strtotime($input['latest_visit_time_end']);
            $where['latest_access_time'] = ['between',[date('Y-m-d H:i:s',$start),date('Y-m-d H:i:s',$end)]];
        }
        //排序
        $order = $request->input('order','');
        $orderBy = $request->input('orderBy','');
        $order = $order && in_array($order,['desc','asc']) ? $order : 'desc' ;
        $orderBy = $orderBy && in_array($orderBy,['buy_num','amount','created_at','latest_access_time']) ? $orderBy : 'mid' ;
        $input = $request->input();
        if ($source = $request->input('source',0)) {
            $where['source'] = $source;
            $source == 2 && $where['source'] = ['in',[0,1,2]];

        }
        //查询时间
        if (!empty($input['latest_visit_time_start']) && !empty($input['latest_visit_time_end'])) {
            $start = strtotime($input['latest_visit_time_start']);
            $end = $input['latest_visit_time_start'] == $input['latest_visit_time_end'] ? $start + 86400 : strtotime($input['latest_visit_time_end']);
            $where['latest_access_time'] = ['between',[date('Y-m-d H:i:s',$start),date('Y-m-d H:i:s',$end)]];
        }
        $memberCardList = MemberCardRecordService::getMemberCardList($where,$orderBy,$order);
        $provinceList = (new RegionService())->getProvinceList();
        $allMemberCard =  MemberCardService::getListByWhere(['wid'=>$wid]);
        return view('merchants.member.members',array(
            'title'          => '客户概况',
            'leftNav'        => $this->leftNav,
            'slidebar'       => 'members',
            'list'           => [],
            'memberCardList' => $memberCardList,
            'provinceList'   => $provinceList,
            'allMemberCard'  => $allMemberCard
        ));
    }

    /**
     *
     *导入会员
     *
     */

    public function membersImport(Request $request){
        $wid = $request->session()->get('wid');

        $where = ['wid' => $wid];
        list($list,$pageHtml)  = $this->memberImportService->getListByConditionWithPage($where);
        $cardRow = MemberCardService::getListByWid($wid);
        //设置导入记录的会员卡名
        if (isset($list['data']) && !empty($list['data'])) {
            foreach ($list['data'] as $k => $v) {
                foreach ($cardRow as $c) {
                    if ($c['id'] == $v['card_id']) {
                        $list['data'][$k]['card_title'] = $c['title'];
                    }
                }
            }
        } else {
            $list['data'] = [];
        }

        return view('merchants.member.members_import',array(
            'title'=>'客户概况',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'members',
            'importRow'=>$list['data'],
            'pageHtml'=>$pageHtml
        ));
    }

    /*
     * 新建导入会员
     */
    public function addImport(Request $request, MemberService $memberService){
        $wid = $request->session()->get('wid');
        $cardRow = MemberCardService::getListByWid($wid);
        if($request->isMethod('post')) {
            $input = $request->input();
            $fileSize = ($_FILES["file"]["size"] / 1024/1024);
            $fileSize = sprintf('%.2f',$fileSize);
            if($fileSize>10){
                error('文件大于10M');
            }
            $filePath = $_FILES["file"]["tmp_name"];
            $contents = file($filePath);
            $imports = array();
            $imports['wid'] = $wid;
            $total = count($contents)-1;
            $imports['total'] = '导入中...';
            $imports['success_num'] = '导入中...';
            $imports['fail_num'] = '导入中...';
            $imports['card_id'] = $input['card_id'];
            //默认不需要验证
            $imports['isverify'] = 0;
            $imports['editor'] = $request->session()->get('userInfo')['name'];

            $last_id  = $this->memberImportService->add($imports);

            //最终入库数据列表数组
            $customerList = [];
            // 设置是否出错
            $i = 1;
            //成功插入条数
            $success_num = 0;

            try{
                $filepath = $_FILES['file']['tmp_name'];
                if($filepath) {
                    //获取扩展名
                    $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    if ($extension == 'csv') {
                        //csv文件
                        foreach ($contents as $line_num => $line) {
                            $line = iconv('gb2312', 'utf-8', $line);
                            if($line_num) {
                                $customerList[] = MemberService::getMemberInfo($line, $wid);
                                $success_num++;
                            }
                        }
                    } else if ($extension == 'xls' || $extension == 'xlsx') {
                        //excel文件
                        $objPHPExcel = \PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);
                        $sheet = $objPHPExcel->getSheet(0);
                        //初始列表数据
                        $dataArr = $sheet->toArray();
                        //除去标题行
                        unset($dataArr[0]);
                        foreach ($dataArr as $v) {
                            $customerList[] = MemberService::getMemberInfo($v, $wid);
                            $success_num++;
                        }
                        $total = $success_num;
                    }

                    foreach ($customerList as $v) {
                        //保存到客户表
                        $mid = $memberService->add($v);
                        //保存到会员卡领取表
                        //生成会员卡卡号
                        $str = md5($wid);
                        $card_no = '';
                        for($i=0;$i<strlen($str);$i++){
                            $num = (int)($str[$i]);
                            if($num == 0){
                                $num = rand(0,9);
                            }
                            $card_no .= $num;
                        }
                        $cardRecordData = [];
                        $cardRecordData['wid']      = $wid;
                        $cardRecordData['mid']      = $mid;
                        $cardRecordData['card_id']  = $input['card_id'];
                        $cardRecordData['card_num'] = $card_no;
                        MemberCardRecordService::init('wid', $wid)->add($cardRecordData,false);
                    }
                }else{
                    error('文件上传失败！');
                }
            }catch(Exception $e){
                $i = 0;
                if(empty($success_num)){
                    $data['total'] = '导入失败';
                    $data['success_num'] = '导入失败';
                    $data['fail_num'] = '导入失败';
                }else{
                    $data['total'] = $total;
                    $data['success_num'] = $success_num;
                    $data['fail_num'] = $total-$success_num;
                }
                $this->memberImportService->update($last_id, $data);
            }

            if($i){
                $data['total'] = $total;
                $data['success_num'] = $success_num;
                $data['fail_num'] = $total-$success_num;
                $this->memberImportService->update($last_id, $data);
            }
            return redirect('/merchants/member/import');
        }

        return view('merchants.member.add_import',array(
            'title'=>'客户概况',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'members',
            'cardRow'=>$cardRow
        ));
    }

    /*
     * 标签管理
     */
    public function label(Request $request,$list=''){

        $wid = $request->session()->get('wid');

        //表单提交
        if($request->isMethod('post')) {
            $label = MemberLabelService::verify(['rule_name','trade_limit','amount_limit','points_limit']);
            $label['wid'] = $wid;
            $label['created_at'] = time();

            $query = MemberLabelService::init('wid', $wid)->insert($label);

            if($query){
                success('添加成功', '/merchants/member/label');
            }else{
                error('参数错误');
            }
        }
        $where = [];

        list($lists,$pageHtml) = MemberLabelService::init('wid',$wid)->where($where)->getList();
        //导出 csv
        if ($list=="csv") {
            //导出 csv
            $this->list_csv($lists['data']);
            // return false;
        } else if ($list == 'xls') {
            //导出excel
            MemberLabelService::exportExcel($lists['data']);
        }

        return view('merchants.member.label',array(
            'title'=>'标签管理',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'label',
            'label'   => $lists['data'],
            'total'   =>$lists['total'],
            'pageList'=>$pageHtml
        ));
    }


    /*
     * 标签管理-新建标签
     */
    public function labelAdd(){
        return view('merchants.member.label_add',array(
            'title'=>'新建标签',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'label'

        ));
    }

    /*
     * 标签管理-删除
     */

    public function labelDel(Request $request,$id){
        $wid = $request->session()->get('wid');
        $query = MemberLabelService::init('wid',$wid)->del($id);
       // $query = DB::table('member_tag')->where('id',$id)->delete();
        if($query){
            success('删除成功');
        }else{
            error('失败！');
        }
    }

    /**
     * 查看会员卡
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @update: 梅杰 2018年8月31日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function membercard(Request $request,ShopService $shopService){
        /* 将获取session的id */
        $wid = $request->session()->get('wid');

        //获取相关店铺信息
        $uid = D('Weixin')->model->where('id', $wid)->value('uid');
        /*$weixinService = D('Weixin', 'uid', $uid);
        $info = $weixinService->getInfo($wid);*/
        $info = $shopService->getRowById($wid);
        //表单提交
        if($request->isMethod('post')){

            $card_status = $request->input('card_status');

            $card = ['title','member_power','description'];
            if($card_status == 1){
                array_push($card,'card_rank');
            }

            MemberCardService::verify($card);
            $postData = $request->all();
            if($card_status == 1){
                if (empty($postData['cumulative_pay'])
                    && empty($postData['cumulative_amount'])
                    && empty($postData['cumulative_score'])) {
                    return myerror('领取设置不能为空');
                }
            }
            //hsz 添加判断处理
            if (isset($postData['date_limit']) && isset($postData['limit_days'])) {
                if (($postData['date_limit'] == 1 && empty($postData['limit_days'])) || $postData['date_limit'] == 2 && (empty($postData['startAt']) || empty($postData['endAt']))) {
                    return myerror('请设置会员卡期限');
                }
            }
            //end
            //处理提交的数据
            $saveData = MemberCardService::postCardDataHandle($postData,$card_status,$wid);
            $powerArr = explode(',',$saveData['member_power']);
            $power_explain = '';
            if(in_array(1,$powerArr)){
                $power_explain .= '享受会员包邮 ';
            }
            $discount = 0;
            if(in_array(2,$powerArr)){
                $power_explain .= '会员折扣'.$saveData['discount'].'折 ';
                $discount = $saveData['discount'];
            }
            if(in_array(3,$powerArr)) {
                $member_power['coupon'] = '';
            }
            if(in_array(4,$powerArr)){
                $power_explain .= '随卡获赠积分'.$saveData['score'].'分 ';
            }
            $saveData['wid'] = $wid;  //店铺id

            $id = $request->input('id');  //如果有提交表示进行更新操作

            $otherData['logo']          = empty($info['logo']) ? config('app.url').'home/image/huisouyun_120.png' : $info['logo'];
            $otherData['shop_name']     = $info['shop_name'];
            $otherData['power_explain'] = $power_explain;
            $otherData['weixin_logo_url'] = !empty($info['weixin_logo_url']) ? $info['weixin_logo_url'] : 'http://mmbiz.qpic.cn/mmbiz/iaL1LJM1mF9aRKPZJkmG8xXhiaHqkKSVMMWeN3hLut7X7hicFNjakmxibMLGWpXrEXB33367o7zHN0CwngnQY7zb7g/0';
            //设置一个默认的客服联系电话
//            if(empty($saveData['service_phone'])){
//                $saveData['service_phone'] = '0571-58123088';
//            }
            $saveData['discount'] = $discount;
            //处理添加或编辑会员卡
            $this->memberCardUp($saveData,$otherData,$wid,$id);

        }

        $state = $request->input('state', 1);
        $list = MemberCardService::getListByWhere(['wid'=>$wid,'state'=> $state]);
        /* 查询店铺信息 */
        $weixin = new Weixin();
        $this->weixinInfo = $weixin->select('id', 'shop_name')->find($wid);

        $cardRow = array();
        foreach ($list as $l){
            $cardRow[$l['card_status']][]=$l;
        }

        return view('merchants.member.membercard',array(
            'title'        => '查看会员',
            'leftNav'      => $this->leftNav,
            'slidebar'     => 'membercard',
            'weixinInfo'   => $this->weixinInfo,
            'cardRow'      => $cardRow,
            'pageList'     => '',
            'wid'          => $wid,
            'redirect_url' => config('app.url').'shop/index/'.$wid,
            'state' => $state
        ));
    }

    /**
     * 获取会员卡列表
     * @param Request $request
     * @author: 梅杰 2018年8月14日
     */
    public function  getMemberCardList(Request $request)
    {
        $wid = $request->session()->get('wid');
        $where = ['wid'=>$wid,'state'=> 1];
        if ($title = $request->input('title','')) {
            $where['title'] = ['like',"%$title%"];
        }
        $list = MemberCardService::getListByWhere($where);
        success('请求成功','',$list);
    }

    /**
     * 获取用户的会员卡
     * @param Request $request
     * @param MemberCardModule $module
     * @author: 梅杰 2018年9月10号
     */
    public function getOneMemberMemberCardList(Request $request)
    {
        $wid = $request->session()->get('wid',0);
        $mid = $request->input('mid',0);
        $re = MemberCardRecordService::init('wid',$wid)->model->where(['mid'=>$mid,'wid'=>$wid,'status'=>1])->orderBy('id','asc')->with(['memberCard'])->paginate(5);
        foreach ($re as &$value) {
            $powerArr = $value->memberCard->member_power;
            $powerArr = explode(',',$powerArr);
            $power_explain = '';
            if(in_array(1,$powerArr)){
                $power_explain .= '享受会员包邮/';
            }
            if(in_array(2,$powerArr)){
                $power_explain .= '会员折扣/';
            }
            if(in_array(3,$powerArr)) {
                $power_explain .= '优惠券/';
            }
            if(in_array(4,$powerArr)){
                $power_explain .= '随卡获赠积分';
            }
            $value->memberCard->member_explain = $power_explain;
        }
        success('请求成功','',$re);
    }


    public function storageValue(Request $request){
        $wid = session('wid');
        $balanceRule = new BalanceRuleService();
        $ruleList    = $balanceRule->getWidRule($wid);
        return view('merchants.member.storageValue',array(
            'title'        => '会员储值',
            'leftNav'      => $this->leftNav,
            'slidebar'     => 'membercard',
            'ruleList'     => $ruleList
        ));
    }

    public function storageValueAdd(Request $request){
        $id = intval($request->input('id'));
        $balanceRule = new BalanceRuleService();
        $ruleData    = $balanceRule->getRowById($id);
        return view('merchants.member.storageValueAdd',array(
            'title'        => '储值规则',
            'leftNav'      => $this->leftNav,
            'slidebar'     => 'membercard',
            'ruleData'     => $ruleData
        ));
    }

    //会员储值
    public function storageRecord(Request $request, MemberService $memberService){
        $wid   = session('wid');
        $balanceLogService = new BalanceLogService();
        list($list, $pageHtml)  = $balanceLogService->getWidLog($wid);
        $memberId = [];
        if (!empty($list['data'])) {
            foreach ($list['data'] as  $balance) {
                $memberId[] = $balance['mid'];
            }
        }
        $members = [];
        if (!empty($memberId)) {
            $memberData = $memberService->getListById(array_unique($memberId));
        }
        if (!empty($memberData)) {
            foreach ($memberData as $val){
                $members[$val['id']] = $val;
            }
        }
        //累计储值金额
        $allRecharge = $balanceLogService->getAllRecharge($wid);
        $allCost = $balanceLogService->getAllRecharge($wid, 2);
        $costNum = $balanceLogService->getDistinct($wid);

        return view('merchants.member.storageRecord',array(
            'title'        => '储值记录',
            'list'     => $list['data'],
            'members'   => $members,
            'pageHtml' => $pageHtml,
            'leftNav'      => $this->leftNav,
            'allRecharge'  => $allRecharge,
            'allCost'  => $allCost,
            'costNum'  => $costNum,
            'slidebar'     => 'membercard'
        ));
    }

    /**
     * 余额明细
     *
     * @param  Request $request [http请求类]
     * @return view
     *
     * @author 陈文豪
     * @version 2017年3月5日 17:22:32
     * @update 2018-07-30 19:15:00 陈文豪 余额分页
     */

    public function getMemberBalaceLog(Request $request) {
        $wid = $request->session()->get('wid');
        $mid = $request->input('mid');
        $balanceLogService = new BalanceLogService();
        list($list) = $balanceLogService->getUserLog($wid, $mid, 0);

        $data = $page = [];

        if(!empty($list))
        {
            $page['currentPage'] = $list['current_page'];
            $page['pageSize']    = $list['per_page'];
            $page['total']       = $list['total'];
        }

        if (!empty($list['data'])) {
            $userinfo = (new MemberService())->getRowById($mid);
            $money = $userinfo['money'];
            foreach ($list['data'] as $key => $value) {
                $data[$key]['type_name'] = '-';
                $data[$key]['pay_name'] = '支付';
                $data[$key]['pay_way_name'] = '余额支付';

                if ($value['type'] == 1) {
                    $data[$key]['type_name'] = '+';
                    $data[$key]['pay_name'] = '充值成功';
                    $data[$key]['pay_way_name'] = '微信安全支付';
                }
                if ($value['pay_way'] == 4) {
                    $data[$key]['pay_way_name'] = '系统操作';
                }
                $data[$key]['pay_desc'] = $value['pay_desc'];
                $data[$key]['money'] = $value['money']/100;
                $data[$key]['created_at'] = date('Y-m-d H:i:s',$value['created_at']);

                if($value['type'] == 1) {
                    $data[$key]['money_total'] = $money;
                    $money -= $value['money'];
                } else {
                    $data[$key]['money_total'] = $money;
                    $money += $value['money'];
                }
            }
        }
        $return[0] = $data;
        $return[1] = $page;
        success('', '', $return);
    }

    //删除储值规则
    public function delBalanceRule(Request $request) {
        $returnData = ['errCode'=>0,'errMsg'=>'','data'=>0];

        $wid = session('wid');
        $id = intval($request->input('id'));

        $balanceRule = new BalanceRuleService();
        $num    = $balanceRule->countRule($wid);
        if ($num <= 1) {
            $returnData['errCode'] = 1;
            $returnData['errMsg'] = '至少要保留一个储值规则';
            return $returnData;
        }

        $balanceRule->delBalanceRule($id, $wid);
        return $returnData;
    }

    public function addBalanceRule(Request $request) {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $wid = session('wid');
        $id = intval($request->input('id')) ?: 0;
        $data['wid']       = $wid;
        $data['title']     = $request->input('title');
        $data['money']     = intval($request->input('money')*100);
        $data['add_score'] = $request->input('add_score') ?: 0;

        if ($data['money'] < 1) {
            $returnData['errCode'] = 1;
            $returnData['errMsg'] = '金额错误';
        }
        if (!$data['title']) {
            $returnData['errCode'] = 1;
            $returnData['errMsg'] = '标题不能为空';
        }
        if ($data['add_score'] < 0) {
            $returnData['errCode'] = 1;
            $returnData['errMsg'] = '积分不能为负数';
        }
        $balanceRule = new BalanceRuleService();

        if($balanceRule->checkMoney($wid, $data['money'], $id) === false) {
            $returnData['errCode'] = 1;
            $returnData['errMsg'] = '存在该档位的金额';
        }
        if ($returnData['errCode'] == 1) {
            return $returnData;
        }
        if ($id > 0) {
            $balanceRule->editBalanceRule($id, $wid, $data);
        } else {
            $balanceRule->addRule($wid, $data['title'], $data['money'], 0, $data['add_score']);
        }
        return $returnData;
    }

    public function addBalanceBySystem(Request $request) {
        $returnData=['errCode'=>0,'errMsg'=>'','data'=>0];
        $wid = session('wid');
        $mid = intval($request->input('mid'));
        $money   = $request->input('money');
        $addStatus = (new MemberService())->operateMoney($mid,$money*100);
        if ($addStatus['errCode'] != 0 ) {
            return $addStatus;
        }

        $type = $money > 0 ? 1 : 2;
        $money = abs($money);

        $pay_way = 4;
        $status = 1;
        $msg   = $request->input('msg');

        $balanceLogService = new BalanceLogService();
        $balanceLogService->insertLog($wid, $mid, $money, $pay_way, $type , $status, $msg);

        //余额充值发送到日志服务器 hsz
        $job = new SendRechargeBalanceLog($wid, $mid, $money, $type, $pay_way, time());
        dispatch($job->onQueue('dsBalance'));

        return $returnData;
    }

    /**
     * 添加或编辑会员卡（同步到公众号的会员卡列表）
     * @param  [array]  $saveData  [表单提交要保存会员卡相关信息]
     * @param  [array]  $otherData [同步会员卡相关其他信息比如：店铺logo，店铺名称]
     * @param  [int]  $wid       [店铺id]
     * @param  integer $id        [编辑表单提交的会员卡id]
     * @return [type]             [提示信息]
     */
    public function memberCardUp($saveData,$otherData,$wid,$id=0)
    {
        $dbResult = DB::transaction(function() use($saveData,$otherData,$wid,$id){
            $apiService = new ApiService();
            //先进行数据入库保存，获取相应的card_id
            $status = 0;
            $returnMsg = '';
            if($id){
                $cardId = $id;
                $dbResult = MemberCardService::update($id,$saveData);
                if($dbResult){
                    $status = 1;
                    $returnMsg = '修改成功';
                }
            }else{
                $dbResult = MemberCardService::add($saveData);
                if($dbResult){
                    $cardId = $dbResult;
                    $status = 1;
                    $returnMsg = '添加成功';
                }
            }

            $data_info['type'] = 'DATE_TYPE_PERMANENT';  //设置默认永久有效
            if($status && $saveData['is_sync_wechat'] == 1){
                $cardData = [];
                switch(isset($saveData['limit_type'])  && $saveData['limit_type']){
                    case 1:
                        /****有效期类型，由于仅支持更改type为1的时间戳，不支持填入2（固定时长的时间修改）
                            固定时长转为日期区间(从当天时间开始计有效)
                        ****/
                        // $data_info['type']              = 'DATE_TYPE_FIX_TERM';
                        // $data_info['fixed_term']        = $saveData['limit_days'];
                        // $data_info['fixed_begin_term']  = 0;
                        $data_info['type'] = 'DATE_TYPE_FIX_TIME_RANGE';
                        $data_info['begin_timestamp'] = time();
                        $data_info['end_timestamp']   = strtotime("+".$saveData['limit_days']."days");
                        //用于更改会员卡的有效期
                        $end_timestamp = strtotime('+'.$saveData['limit_days'].'days');
                        break;
                    case 2:
                        $data_info['type']            = 'DATE_TYPE_FIX_TIME_RANGE';
                        $data_info['begin_timestamp'] = strtotime($saveData['limit_start']);
                        $data_info['end_timestamp']   = strtotime($saveData['limit_end']);
                        $end_timestamp = strtotime($saveData['limit_end']);
                        break;
                    default:
                        $data_info['type'] = 'DATE_TYPE_PERMANENT';
                        break;
                }
                $color = 'Color010';
                if($saveData['cover'] == 0){
                    $color = $saveData['cover_value'];
                }

                //核销卡号的方式
                if($saveData['closure_type'] == 0){
                    $code_type = 'CODE_TYPE_TEXT'; //显示卡号
                }else if($saveData['closure_type'] == 1){
                    $code_type = 'CODE_TYPE_BARCODE'; //卡号和条形码
                }else if($saveData['closure_type'] == 2){
                    $code_type = 'CODE_TYPE_QRCODE';  // 卡号和二维码
                }

                $cardData['member_card']['background_pic_url'] = !empty($saveData['weixin_bg_img']) ? $saveData['weixin_bg_img'] : 'https://mmbiz.qlogo.cn/mmbiz/';
                $cardData['member_card']['base_info']  = [
                    'logo_url'        => $otherData['weixin_logo_url'],
                    'code_type'       => $code_type,
                    'title'           => $saveData['title'],
                    'color'           => $color,
                    'notice'          => '使用时出示此会员卡',
                    'service_phone'   => $saveData['service_phone'],
                    'description'     => $saveData['description'],
                    'get_limit'       => 1

                ];

                $cardData['member_card']['supply_bonus'] = true;
                $cardData['member_card']['supply_balance'] = false;
                $cardData['member_card']['prerogative'] = $otherData['power_explain'];
                $cardData['member_card']['custom_field1'] =  [
                    'name_type' => 'FIELD_NAME_TYPE_LEVEL',
                    'url'       => config('app.url').'shop/member/index/'.$wid,
                ];
                $cardData['member_card']['custom_cell1'] = [
                    'name'  => '我的店铺',
                    'tips'  => '进入商城',
                    'url'   => config('app.url').'/shop/index/'.$wid,
                ];
                //是否需要激活会员卡
                if($saveData['is_active'] == 0){
                    $cardData['member_card']['auto_activate'] = true;
                }else{
                    $cardData['member_card']['activate_url'] = config('app.url').'shop/member/detail/'.$wid.'/'.$cardId;
                }
                $cardData['member_card']['base_info']['center_title'] = '查看会员卡';
                $cardData['member_card']['base_info']['center_url'] = config('app.url').'shop/member/detail/'.$wid.'/'.$cardId;
                if($saveData['discount'] > 0){
                    $cardData['member_card']['discount'] = (10-$saveData['discount'])*10; //微信的折扣显示计算
                }

                if($id){ //更新同步卡券信息
                    $memberCardData = MemberCardService::getRowById($id);
                    $card_id = $memberCardData['card_id'];
                    //数据库有保存会员卡id时同步更新操作否则添加同步操作
                    if($card_id){
                        $cardData['card_id'] = $card_id;
                        if($saveData['limit_type'] == 1 || $saveData['limit_type'] == 2){
                            if($saveData['limit_type'] == 1){
                                $begin_timestamp = time();
                                if($memberCardData['limit_days'] > $saveData['limit_days']){
                                    error('微信会员卡有效期时间修改须比原来的区间大');
                                }
                            }else{
                                $limit_end = strtotime($memberCardData['limit_end']);
                                $begin_timestamp = strtotime($saveData['limit_start']);
                                if($limit_end > $end_timestamp){
                                    error('微信会员卡有效期时间修改须比原来的区间大');
                                }
                            }
                            $cardData['member_card']['base_info']['date_info']['type'] = 'DATE_TYPE_FIX_TERM';
                        }else{
                            $cardData['member_card']['base_info']['date_info']['type'] = 'DATE_TYPE_PERMANENT';
                        }
                        $result = $cardData;
                        $result = $apiService->wxCardUpdate($wid,$result);
                        if($result['errcode']){
                            error('同步更新微信会员卡失败');
                        }
                    }else{
                        $cardData['card_type'] = 'MEMBER_CARD';
                        $cardData['member_card']['base_info']['brand_name'] = $otherData['shop_name'];
                        $cardData['member_card']['base_info']['date_info'] = $data_info;
                        $cardData['member_card']['base_info']['sku'] = ['quantity'=>'1000'];
                        $result['card'] = $cardData;
                        $result = $apiService->wxCardCreated($wid,$result);
                        if($result['errcode']){
                            error('同步微信会员卡失败');
                        }else{
                            if(!MemberCardService::update($cardId,['card_id'=>$result['card_id']])){
                                $status = 0;
                                $returnMsg = '';
                            }
                        }

                    }

                }else{
                    $cardData['card_type'] = 'MEMBER_CARD';
                    $cardData['member_card']['base_info']['brand_name'] = $otherData['shop_name'];
                    $cardData['member_card']['base_info']['date_info'] = $data_info;
                    $cardData['member_card']['base_info']['sku'] = ['quantity'=>'1000'];
                    $result['card'] = $cardData;
                    $result = $apiService->wxCardCreated($wid,$result);
                    if($result['errcode']){
                        error('同步微信会员卡失败');
                    }else{
                        if(!MemberCardService::update($cardId,['card_id'=>$result['card_id']])){
                            $status = 0;
                            $returnMsg = '';
                        }
                    }
                }
            }
            $returnData = ['status'=>$status,'info'=>$returnMsg,'url'=>config('app.url').'merchants/member/membercard'];
            return $returnData;
        });

        echo json_encode($dbResult);
        exit;
    }

    /**
     * 发卡
     */
    public function putCard(Request $request)
    {
        $wid = session('wid');
        $id = $request->input('id'); //数据库中保存的会员卡id
        $url = config('app.url').'shop/member/detail/'.$wid.'/'.$id;
        $result['show_qrcode_url'] = $url;
        $qrcodeStr = QrCode::size(150)->generate(URL($url));
        $result['qrcodeStr'] = $qrcodeStr;
        echo json_encode($result);
        exit;
    }

    /**
     * 删除会员卡
     */
    public function membercardDelete(Request $request){
        $wid = session('wid');
        $apiService = new ApiService();
        if($request->isMethod('post')){
            $rowId = $request->input('id');
            if(empty($rowId)){
                error('不存在这个会员卡');
            }
            $memberCardData = MemberCardService::getRowById($rowId);
            $data = ['state'=>'-1'];
            $res = MemberCardService::update($rowId,$data);
            if($res) {
                if(!empty($memberCardData['card_id'])){
                    $result['card_id'] = $memberCardData['card_id'];
                    $apiService->wxCardDelete($wid,$result);
                }
                success('操作成功', '/merchants/member/membercard');
            }
        }
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170518
     * @desc 禁用会员卡
     * @param $id
     */
    public function disableCard($id)
    {
        $res = MemberCardService::getRowById($id);
        if (!$res){
            myerror('该会员卡不存在');
        }
        if ($res['state'] == 1){
            $state = 0;
        }else{
            $state = 1;
        }
        $re = MemberCardService::update($id,['state'=>$state]);
        if($re == false)
            error();
        success();
    }

    /*
     * 领卡记录
     */
    public function membercardObtain(Request $request){
        $wid = $request->session()->get('wid');
        $card_num = $request->input('card_no');
        $whereArr = array('status'=>1);
        if($card_num){
            $whereArr = array_merge(array('card_num'=>$card_num),$whereArr);
        }
        $with = ['memberCard'];
        list($list,$page) = MemberCardRecordService::init('wid', $wid)->with($with)->where($whereArr)->getList();
        return view('merchants.member.membercard_obtain',array(
            'title'=>'领卡记录',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'membercard',
            'card_no'=>$card_num,
            'obtain'=>$list['data'],
            'page'  => $page,
            'weixinInfo' => $this->weixinInfo
        ));
    }

    /*
     * 退卡记录
     */
    public function membercardRefund(Request $request){
        $wid = $request->session()->get('wid');
        $card_no = $request->input('card_no');
        $whereArr = array('status'=>0);
        if($card_no){
            $whereArr = array_merge(array('card_no'=>$card_no),$whereArr);
        }

        $with = ['memberCard'];
        list($list,$pageHtml) = MemberCardRecordService::init('wid',$wid)->with($with)->where($whereArr)->getList();
        return view('merchants.member.membercard_refund',array(
            'title'=>'退卡记录',
            'leftNav'=>$this->leftNav,
            'card_no'=>$card_no,
            'slidebar'=>'membercard',
            'refund'=>$list['data'],
            'weixinInfo' => $this->weixinInfo
        ));
    }


    /**
     * 会员卡添加编辑
     * @param Request $request 参数类
     * @param int $id 会员卡id
     * @param int $card_status 会员卡状态
     * @return view
     * @author 黄东 2017年03月07日
     * @update 许立 2018年08月01日 获取有效的优惠券
     * @update 许立 2018年09月14日 只显示自己店铺的优惠券
     */
    public function membercardAdd(Request $request,$id=0,$card_status=0){
        $card_status = $request->input('card_status')??$card_status;

        if(empty($this->weixinInfo)){
            /* 将店铺id存入session */
            $wid = $request->session()->get('wid');
            /* 查询店铺信息 */
            $weixin = new Weixin();
            $this->weixinInfo = $weixin->select('id', 'shop_name','logo')->find($wid);
        }
        $memberCard = array();
        if($id){
            $memberCard = MemberCardService::getRowById($id);
        }

        /****优惠券过期、被删除、或库存为0时，系统不再送券 ***/
        $where = ['wid' => session('wid')];
        $where['left'] = array('>',0);
        $where['_string'] = 'invalid_at IS NULL';
        // 获取有效的优惠券
        $where['_closure'] = function ($query) {
            $query->where(function($query){
                $now = date('Y-m-d H:i:s');
                $query->where('expire_type', 0)->where('start_at', '<=', $now)->where('end_at', '>', $now);
            })->orWhere(function ($query){
                //领取后生效的 属于进行中
                $query->where('expire_type','>',0);
            });
        };

        /*****判断绑定店铺的微信公众号是订阅号还是服务号*****/
        $service_type_info = 0;
        $weChatShopConfService = new WeChatShopConfService();
        if($data = $weChatShopConfService->getRowByWid($wid)){
            $service_type_info = $data['service_type_info'];
        }
      
        return view('merchants.member.membercard_add',array(
            'title'=>'新建会员卡',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'membercard',
            'card_status'=>$card_status,
            'memberCard'=>$memberCard,
            'cardTitles'=>'',
            'conponList'=> (new CouponService())->listWithoutPage($where)[0]['data'],
            'weixinInfo' => $this->weixinInfo,
            'service_type_info' =>$service_type_info,
        ));
    }

    /**
     *粉丝管理
     */

    public function fans(Request $request){
        $wid = $request->session()->get('wid');

        list($list,$pageHtml) = MemberLabelService::init('wid',$wid)->select(['id','rule_name'])->perPage(100)->getList();
        $label = $list['data'];

        $input = $request->input();

        $regions = DB::table('regions')->where(['pid'=>'-1'])->get(); //地区
        $default = array('-2'=>'江浙沪','-3'=>'珠三角','-4'=>'港澳台','-5'=>'京津');

        $where = MemberFansService::verifyWhere(['gander','fans_name','regions_id','fs'],$regions);

        if(isset($where['regions_default'])){ //选中参数
            $default = $where['regions_default'];
            unset($where['regions_default']);
        }

        list($list,$pageHtml) = MemberFansService::init('wid',$wid)->where($where)->getList();

        return view('merchants.member.fans',array(
            'title'=>'粉丝管理',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'fans',
            'fans' => $list['data'],
            'total'=>$list['total'],
            'label' =>$label,
            'regions'=>$regions,
            'input'=>$input,
            'pageList'=>$pageHtml,
            'default'=>$default
        ));
    }

    /*
     * 粉丝管理-更改
     */
    public function fansEdit(Request $request){
        $wid = $request->session()->get('wid');
        //验证 id
        $data = $ids = array();

        $input = MemberFansService::verify(['id','integral','tag_id','fans_level']);

        $ids['id'] =['in',array_unique($input['id'])];
        // 初始发
        $query = MemberFansService::init('wid', $wid);
        if(!empty($input['integral'])) {
            $query->where($ids)->increment('integral', $input['integral']);//update($data)->toSql();
        }else{
            $query->where($ids)->update($input);
        }


        if(!empty($input['tag_id'])){
            $counts = $query->getTagNum();
            foreach ($counts as $c){
                $data = array('w_name_num'=>$c['tag_counts'],'id'=>$c['tag_id']);

                $where = ['id'=>$c['tag_id']];

                MemberLabelService::init('wid',$wid)->where($where)->update($data,false);
                $tagIds[] = $c['tag_id'];
            }
            $data = array('w_name_num'=>0);
           // $where =['not in',$tagIds];
           // MemberLabelService::init('wid',$wid)->where($where)->update($data,false);
            //暂时 还没有not in 更新redis
             DB::table('member_tag')->whereNotIn('id',$tagIds)->update($data);

        }



        success('成功','/merchants/member/fans');


    }

    /*
     * 粉丝等级筛选-购买力
     */

    public function fansScreen(Request $request,$status=0){
        $wid = $request->session()->get('wid');
        list($list,$pageHtml) = MemberLabelService::init('wid',$wid)->select(['id','rule_name'])->where(['isDelete'=>'1'])->perPage(100)->getList();
        $label = $list['data'];
        list($list,$pageHtml) = MemberCardService::getListPage(['isDelete'=>'1'],'','',100);
        $card = $list['data'];
        $input = $request->input();
        $query =  MemberFansService::init('wid',$wid);
        $where = array();
        if(!empty($input)){
            if($status){
                $where  = $this->fansSearchStatus(['t_time','tradeCount','avg_price']);

            }else {
                $where = $this->fansSearch(['integral','cid','tag_id','tao_level','tao_vip','f_time']);
            }
        }

        list($fansList,$pageHtml) = $query->where($where)->getList();


        return view('merchants.member.fans_screen',array(
            'title'=>'等级筛选',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'fans',
            'fans' =>$fansList['data'],
            'total'=>$fansList['total'],
            'label'=>$label,
            'status'=>$status,
            'input'=>$input,
            'card'=>$card,
            'pageList'=>$pageHtml
        ));
    }

    /**
     * 积分规则
     *
     */
    public function indexPoint(){
        return view('merchants.member.indexPoint',array(
            'title'=>'等级筛选',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'score'
        ));
    }

    //购买力筛选
    protected function fansSearchStatus($verifyField=['id']){ // ['t_time','tradeCount','avg_price']
        /* 接收数据 */
        $input = MemberFansService::getRequest($verifyField);

        $where = array();
        foreach ($verifyField as $value){
            switch ($value){
                case 't_time';
                    $t_time = $input[$value];
                    switch ($t_time){
                        case '1w':
                            $where['buy_at'] = ['>', time()-(7*24*3600)];
                            break;
                        case '2w':
                            $where['buy_at'] = ['>', time()-2*(7*24*3600)];
                            break;
                        case '1m':
                            $where['buy_at'] = ['>', time()-30*(7*24*3600)];
                            break;
                        case '2m':
                            $where['buy_at'] = ['>', time()-2*30*(7*24*3600)];
                            break;
                        case '3m':
                            $where['buy_at'] = ['>', time()-3*30*(7*24*3600)];
                            break;
                        case '6m':
                            $where['buy_at'] = ['>', time()-6*30*(7*24*3600)];
                            break;
                        case '-6m':
                            $where['buy_at'] = ['<=', time()-6*30*(7*24*3600)];
                            break;
                        default:
                            if(empty($t_time)){
                                break;
                            }

                            list($start_time,$end_time) = explode('|',$t_time);
                            $buy_atArr[0]= strtotime($start_time);
                            $buy_atArr[1] = strtotime($end_time);
                            $where['buy_at'] = ['between',$buy_atArr];
                            break;
                    }
                    break;
                case 'tradeCount':
                    if (!empty($input[$value])) {
                        if(strpos($input[$value],'-')){
                            list($startTradeCount,$endTradeCount)= explode('-',$input[$value]);
                            $tradeArr[0]= $startTradeCount;
                            $tradeArr[1] = $endTradeCount;
                            $where[$value] = ['between',$tradeArr];
                        }else {
                            $where[$value] = ['>', $input[$value]];
                        }
                    }
                    break;
                case 'avg_price':
                    if (!empty($input[$value])) {
                        $avg_price = $input[$value];
                        $arg = explode('-',$avg_price);
                        if(count($arg)==1) {
                            if ($avg_price <= 50) {
                                $where[$value] = ['<=', $input[$value]];
                            } else if ($avg_price > 1000) {
                                $where[$value] = ['>', $input[$value]];
                            }
                        }else{
                            $where[$value] = ['between',$arg];
                        }

                    }
                    break;
                default :
                    // code ..
                    break;
            }
        }
        return $where;
    }

    //等级筛选
    protected function fansSearch($verifyField=['id']){
        /* 接收数据 */
        $input = MemberFansService::getRequest($verifyField); //integral=&cid=&tag_id=&tao_level=&tao_vip=&f_time=
        $where = array();
        foreach ($verifyField as $value){
            switch ($value) {
                case  'integral':
                    $integral = !empty($input[$value])?$input[$value]:null;
                    if($integral=='1000+'){
                        $where[$value] = ['>',100];
                    }else if($integral){
                        $between = explode('-',$integral);
                        $where[$value] = ['between',$between];
                    }
                    break;
                case  'cid':
                    if(!empty($input[$value])){
                        $where[$value] = $input[$value];
                    }
                    break;
                case  'tag_id':
                    if(!empty($input[$value])){
                        $where[$value] = $input[$value];
                    }
                    break;
                case  'tao_level':
                    if(!empty($input[$value])){
                        $where[$value] = $input[$value];
                    }
                    break;
                case  'tao_vip':
                    if(!empty($input[$value])){
                        $where[$value] = $input[$value];
                    }
                    break;
                case  'f_time':
                    if(!empty($input[$value])){
                        $f_time = $input[$value];
                        switch ($f_time) {
                            case '1w':
                                $where['created_at'] = ['>',time() - (7 * 24 * 3600)];
                                break;
                            case '2w':
                                $where['created_at'] = ['>',time() - 2 * (7 * 24 * 3600)];
                                break;
                            case '1m':
                                $where['created_at'] = ['>',time() -  30 * (7 * 24 * 3600)];
                                break;
                            case '2m':
                                $where['created_at'] = ['>',time() -   2 * 30 * (7 * 24 * 3600)];
                                break;
                            case '3m':
                                $where['created_at'] = ['>',time() -   3 * 30 * (7 * 24 * 3600)];
                                break;
                            case '6m':
                                $where['created_at'] = ['>',time() -   6 * 30 * (7 * 24 * 3600)];
                                break;
                            case '-6m':
                                $where['created_at'] = ['<=',time() -   6 * 30 * (7 * 24 * 3600)];
                                break;
                            default:
                                if(empty($f_time)){
                                    break;
                                }
                                list($start_time, $end_time) = explode('|', $f_time);

                                $buy_atArr[0]= strtotime($start_time);
                                $buy_atArr[1] = strtotime($end_time);
                                $where['created_at'] = ['between',$buy_atArr];
                                break;
                        }
                    }
                    break;
                default :
                    // code ...
                    break;
            }
        }
        return $where;

    }

    protected function addImport_csv(){
        $str = "卡号,姓名,手机号（必填）,性别,微信号,收货地址,生日\n";
        $str = iconv('utf-8','gb2312',$str);
        $filename = 'member_import.csv'; //设置文件名
        $this->export_csv($filename,$str); //导出
    }

    protected function list_csv($label){
        header("Content-type:text/html;charset=utf-8");
        $str = "标签名,微信会员,手机会员,自动加标签条件\n";
        $str = iconv('utf-8','gb2312',$str);
        foreach($label as $row){
            $rule_name = '';
            if(!empty($row['rule_name'])) {
                $rule_name = iconv('utf-8', 'gb2312', $row['rule_name']);  //中文转码
            }
            $w_name_num = '';
            if(!empty($row['w_name_num'])) {
                $w_name_num = iconv('utf-8', 'gb2312', $row['w_name_num']);
            }
            $mobile_name_num = '';
            if(!empty($row['mobile_name_num'])) {
                $mobile_name_num = iconv('utf-8','gb2312',$row['mobile_name_num']);
            }

            $whereTag = ['trade_limit'=>$row['trade_limit'],'amount_limit'=>$row['amount_limit'],'points_limit'=>$row['points_limit']];
            if(!empty($row['trade_limit'])){
                $whereTag['trade_limit'] = '累计成功交易 '.$row['trade_limit'].' 笔';
            }else{
                unset($whereTag['trade_limit']);
            }
            if(!empty($row['amount_limit'])){
                $whereTag['amount_limit'] = '累计购买金额 '.$row['amount_limit'].' 元';
            }else{
                unset($whereTag['amount_limit']);
            }

            if(!empty($row['points_limit'])){
                $whereTag['points_limit'] = '累计积分达到 '.$row['points_limit'];
            }else{
                unset($whereTag['points_limit']);
            }
            if(empty($whereTag)){
                $td = iconv('utf-8','gb2312','未设置');
            }else{
                $td = iconv('utf-8','gb2312',implode(" ",$whereTag));
            }

            $str .= $rule_name.",".$w_name_num.",".$mobile_name_num.",".$td."\n"; //用引文逗号分开
        }

        $str = mb_convert_encoding($str,'utf-8','gb2312');
        //var_dump($str);exit;
        $filename = date('Ymd').'.csv'; //设置文件名
        $this->export_csv($filename,$str); //导出
    }

    protected function export_csv($filename,$data){
        //header("Content-Type: application/vnd.ms-excel; charset=gb2312");
        //header("Content-type:text/html;charset=gb2312");
        header('Content-Transfer-Encoding: GBK');
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header("Pragma: no-cache");
        header('Expires:0');
        header('Pragma:public');
        print(chr(0xEF).chr(0xBB).chr(0xBF));
        echo $data;
        exit;
        return false;
    }

    /**
     * @auth zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 201703172037
     * @desc 添加会员
     * @param Request $request
     * @param MemberCardService $memberCardService
     */
    public function  addMemberCard(Request $request,MemberCardService $memberCardService)
    {
        $input = $request->input();
        $rule = [
            'cover'         => 'required',
            'title'         => 'required',
            'description'  => 'required',
            'service_phone'=> 'required',
            'limit_type'    => 'required',
            'limit_card_id'=> 'required',
            'is_share'      => 'required',
        ];
        $message = [
            'cover.required'         => '卡片封面不能为空',
            'title.required'         => '会员卡名称不能为空',
            'description.required'  => '使用须知不能为空',
            'service_phone.required'=> '客服电话不能为空',
            'limit_type.required'    => '会员期限不能为空',
            'limit_card_id.required'=> '过期设置不能为空',
            'is_share.required'      => '分享设置不能为空',
        ];

        if ($input['limit_type.required'] == 1){
            $rule['limit_days'] = 'required';
            $message['limit_days.required'] = '期限天数不能为空';
        }elseif ($input['limit_type.required'] == 2){
            $rule['limit_start'] = 'required';
            $rule['limit_end'] = 'required';
            $message['limit_start.required'] = '会员卡期限开始时间不能为空';
            $message['limit_end.required'] = '会员卡期限结束时间不能为空';
        }
        $validator = Validator::make($input,$rule,$message);
        if ($validator->fails()){
            error($validator->errors()->first());
        }
        $memberCardData = [
            'title'         => $input['title'],
            'description'  => $input['description'],
            'service_phone'=> $input['service_phone'],
            'limit_type'    => $input['limit_type'],
            'limit_card_id'=> $input['limit_card_id'],
            'is_share'      => $input['is_share']?1:0,
        ];
        if (is_numeric($input['cover'])){
            $memberCardData['cover_img'] = $input['cover'];
        }else{
            $memberCardData['cover_bgcolor'] = $input['cover'];
        }
        $memberCardData['wid'] = $request->session()->get('wid');
        (isset($input['is_free_freight']) && !empty($input['is_free_freight']))? $memberCardData['is_free_freight']= $input['is_free_freight']:'';
        if (isset($input['discount']) && !empty($input['discount'])){
            if ($input['discount']>=1 && $input['discount']<=10){
                $memberCardData['discount'] = $input['discount'];
            }else{
                error('折扣必须大于1小于10');
            }
        }
        if (isset($input['is_coupon']) && !empty($input['is_coupon'])){
            $memberCardData['is_coupon'] = 1;
            $memberCardData['coupon_conf'] = json_encode($input['is_coupon']);
        }else{
            $memberCardData['is_coupon'] = 0;
        }
        if (isset($input['score']) && ($input['score']>0)){
            $memberCardData['score'] = $input['score'];
        }else{
            $memberCardData['score'] = 0;
        }
        if ($input['limit_type'] == 1){
            $memberCardData['limit_days'] = $input['limit_days'];
        }elseif($input['limit_type'] == 2){
            $memberCardData['limit_start'] = $input['limit_start'];
            $memberCardData['limit_end'] = $input['limit_end'];
        }

    }

    //下载二维码
    //@update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
    public function downQrcode(Request $request,ShopService $shopService)
    {

        $wid = $request->session()->get('wid');
        $card_id = $request->input('card_id') ?? 0;
        $qrcode_type = $request->input('qrcode_type');
        //扫二维码要跳转的地址
        if($qrcode_type == 'card'){
            $data = MemberCardService::getRowById($card_id);
//            $webUrl = $request->input('callback');
            $webUrl = config('app.url').'shop/member/detail/'.$wid.'/'.$data['id'];
        }else if($qrcode_type == 'store'){
            //$stores = WeixinService::getStageShop($wid);
            $stores = $shopService->getRowById($wid);
            $webUrl = config('app.url').'shop/index/'.$wid;
        }
        if(!empty($data)){
            //$logo = '/public/mctsource/images/merchants_logo.png';
            $filename = QrCodeService::create($webUrl,'',238,$qrcode_type.'/'.$data['title']); //生成二维码
            return response()->download($filename, $data['title'].'.png');  //下载
        }else if(!empty($stores)){
            $filename = QrCodeService::create($webUrl,'',238,$qrcode_type.'/'.$wid); //生成二维码
            return response()->download($filename, $stores['shop_name'].'.png');  //下载
        }


    }

    //获取可用未删除的会员卡，微页面调用   Mj
    public function getUsefulCard(Request $request)
    {
        $keyword = $request->input(['keyword']) ?? '';
        $where = [
            'wid' => session('wid'),
            'state' => 1,
            'card_status' => 0,
        ];
        if($keyword){
            $where['title'] = ['LIKE',"%".$keyword."%"];
        }
        list($list,$html) = MemberCardService::getListPage($where, $orderBy = '', $order = '',$pagesize = '5');
        if($list['data']){
            foreach ($list['data'] as $k=>$v){
                $powerArr = explode(',',$v['member_power']);
                $member_power = '';
                if(in_array(1,$powerArr)){
                    $member_power .= '包邮 ';
                }
                if(in_array(2,$powerArr)){
                    $member_power .= ' '.$v['discount'].'折 ';
                }
                if(in_array(3,$powerArr)){
                    $member_power .= " 优惠券";
                }
                if(in_array(4,$powerArr)){
                    $member_power .= ' 赠送'.$v['score'].'积分,';
                }
                $list['data'][$k]['power_desc'] = mb_substr($member_power,0,-1);
            }
        }
        $data = [
            'data'=>$list['data'],
            'total' =>$list['total'],
            'per_page' =>$list['per_page']
        ];
        success('','',$data);
    }

    /***
     * todo 获取某个用户在某个店铺下的会员卡信息
     * @param Request $request
     * @return array
     * @author jonzhang
     * @date 2017-11-29
     */
    public function getMemberCardByDeveloper(Request $request)
    {
        $returnData=[
            'errCode'=>0,'errMsg'=>'', 'data'=>[]];
        $mid=$request->input('mid');
        $wid=$request->input('wid');
        $errStr='';
        if(empty($mid))
        {
            $errStr.='mid为空';
        }
        if(empty($wid))
        {
            $errStr.='wid为空';
        }
        if(strlen($errStr)>0)
        {
            $returnData['errCode']=-1;
            $returnData['errMsg']=$errStr;
            return $returnData;
        }
        return  MemberCardRecordService::useCard($mid,$wid);
    }

    /***
     * todo 修改会员/客户备注信息
     * fuguowei
     * @date 20180110
     */
    public function updateRemark(Request $request)
    {
        $id = $request->input('id') ?? '';
        $input= $request->input('remark') ?? '';
        if(!$id)
        {
            error('请选择要修改的用户');
        }
        if($request->isMethod('post'))
        {
            $remark1 = trim($input);
            $data['remark']= $remark1;
            $member = new memberService();
            $search = $member->updateData($id, $data);
            if($search)
            {
                success('修改成功','',$search);
            }else{
                error('修改失败');
            }
        }else{
            $member = new memberService();
            $search = $member->getRowById($id);
            if($search)
            {
                success('查找成功','',$search);
            }else{
                error('修改失败');
            }
        }


    }

    /**
     * 客户-注册信息-列表
     * @param Request $request 参数类
     * @return view
     * @author 许立 2018年06月20日
     * @update 许立 2018年07月10日 增加上级手机号检索
     * @update 许立 2018年07月11日 列表返回上级手机号
     */
    public function registerList(Request $request)
    {
        // 过滤
        $input = $request->input();
        $where = [];
        if (!empty($input['name'])) {
            $where['name'] = [ 'like', "%" . $input['name'] . "%"];
        }
        if (!empty($input['type'])) {
            $where['type'] = $input['type'];
        }
        if (!empty($input['phone'])) {
            $where['phone'] = $input['phone'];
        }
        if (!empty($input['start_at'])) {
            $where['created_at'] = ['>=', $input['start_at']];
        }
        if (!empty($input['end_at'])) {
            $where['created_at'] = ['<=', $input['end_at']];
        }

        $register_service = new LiRegisterService();
        // 上级手机号检索
        if (!empty($input['parent_phone'])) {
            // 根据上级手机号获取上级mid
            $row = $register_service->getRowByPhone($input['parent_phone'], session('wid'), $input['type'] ?? 0);
            $where['parent_mid'] = $row['mid'] ?? -1;
        }

        // 列表
        $list = $register_service->listWithPage($where);

        // 获取上级手机号
        $list_final = $register_service->dealWithParentPhone($list[0]['data']);

        return view('merchants.member.registerList',array(
            'title'        => '注册信息',
            'leftNav'      => $this->leftNav,
            'slidebar'     => 'registerList',
            'list'         => $list_final,
            'pageHtml'     => $list[1]
        ));
    }

    public function info()
    {
        return view('merchants.member.info',array(
            'title'=>'注册信息',
            'leftNav'=>$this->leftNav,
            'slidebar'=>'info'
        ));
    }


    /***
     * todo 导出注册信息数据
     * @param Request $request
     * @return array
     * @author fuguowei
     * @date 20180124
     */
    public function  exportXlsApi(Request $request)
    {
        $wid = session('wid');
        if(!$wid)
        {
            error('您还没有权限导出');
        }
        $register = (new LiRegisterService());
        if($request->input('all'))
        {
            $dataTo = $register->listWithoutPage();
            if($dataTo[0]['data'])
            {
                $register->exportExcelXls($dataTo[0]['data']);
            }
        }else{
            //接受数据
            $input = $request->input('idarr') ?? '';
            if(!$input)
            {
                error('请选择要导出的数据');
            }
            $explode = explode(',',$input);
            $where['id'] = ['in',$explode];

            //无分页获取数据
            $dataTo = $register->listWithoutPage($where);
            if($dataTo[0]['data'])
            {
                $register->exportExcelXls($dataTo[0]['data']);
            }
        }

    }

    /***
     * todo 用户信息
     * @param Request $request
     * @return array
     * @author fuguowei
     * @date 20180227
     */
    public function User(Request $request,LiShareEventModule $liShareEventModule)
    {
        $ids= $request->input('id') ?? 0;
        if(!$ids)
        {
            error('请选择用户');
        }
        $all= $request->input('all') ?? 0;
        $ids = [$ids];
        if($all && $all == '1')
        {
            $data = (new LiRegisterService())->getListById($ids);
            if(isset($data[0]['is_register']) && $data[0]['is_register']=='1')
            {
                error('用户已经注册');
            }
            $result = $liShareEventModule->dealRegister($ids);
            success('','',$result);
        }
        if($all && $all == '2')
        {
            $data = (new LiRegisterService())->getListById($ids);
            if(isset($data[0]['is_sms']) && $data[0]['is_sms']=='2')
            {
                error('用户已发送短信');
            }
            $result = $liShareEventModule->sendSMS($ids);
            success('','',$result);
        }


    }


    /**
     * 小程序会员卡小程序码
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function putXcxMemberCard(Request $request ,MemberCardModule $cardModule)
    {
        $card_id = $request->input('card_id');
        if (!$card_id) {
            error('请选择会员卡');
        }
        if ($code = $cardModule->qrCodeLinkMemberCard(session('wid'),$card_id)) {
            success('','',['code'=>$code]);
        }
        error();
    }

    /**
     *
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadXcxMemberCard(Request $request ,MemberCardModule $cardModule)
    {
        $card_id = $request->input('card_id');
        if (!$card_id) {
            error('请选择会员卡');
        }
        if ($filename = $cardModule->downloadXcxCode(session('wid'),$card_id)) {
            return response()->download($filename, '会员卡'.'.png');  //下载
        }
        error();
    }

    /**
     * 用户拉黑列表
     * @return [type] [description]
     */
    public function memberBlackList(Request $request)
    {
        $where = $request->input() ?? [];
        $where['is_pull_black'] = 1;
        $where['wid'] = session('wid');
        // 获取固定数据数组
        list($sourceList, $buyNumList) = MemberService::getStaticList();
        $memberService = new MemberService();
        list($list,$pageHtml) = $memberService->getListByConditionWithPage($where);
        return view('merchants.member.blackList',[
            'title'      => '黑名单',
            'leftNav'    => $this->leftNav,
            'slidebar'   => 'memberBlackList',
            'list'       => $list['data'],
            'pageHtml'   => $pageHtml,
            'buyNumList' => $buyNumList,
            'sourceList' => $sourceList,
            'total'      => $list['total']
        ]);
    }

    /**
     * 设置用户类别（拉黑，移除黑名单）
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function setMemberType(Request $request)
    {
        $input = $request->input();
        $id = $input['id'] ?? 0;
        if (!$id) {
            error('请选择拉黑用户');
        }
        /*type为0时，移出黑名单;type为1时，拉入黑名单*/
        $saveData['is_pull_black'] = $input['type'];
        $memberService = new MemberService();
        $result = $memberService->updateData($id,$saveData);
        return $result;
    }

    /**
     * 给指定会员发卡
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function grantCardToMember(Request $request,MemberCardModule $cardModule)
    {
        $card_id    = $request->input(['card_id'],0);
        $mid        = $request->input(['mid'],[]);
        $wid        = $request->session()->get('wid');
        if (!$mid || !$wid) {
            error('参数缺失');
        }
        $re = $cardModule->grantCardToMember($wid,$mid,$card_id);
        if ($re['err_code'] == 0) {
            success();
        }
        error('操作失败','',$re);
    }

    /**
     * 获取指定会员的未领取的会员卡 未传mid则获取所有的会员卡列表
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function getUnclaimedMemberCardList(Request $request,MemberCardModule $cardModule)
    {
        $mid        = $request->input(['mid'],0);
        $wid        = $request->session()->get('wid');

        $data = $cardModule->getUnclaimedMemberCardList($wid,$mid);
        success('操作成功','',$data);
    }

    /**
     * 给会员删除会员卡
     * Author: MeiJay
     * @param Request $request
     * @param MemberCardModule $cardModule
     */
    public function deleteMemberCard(Request $request,MemberCardModule $cardModule)
    {
        $memberData = $request->input(['memberData']);
        $wid        = $request->session()->get('wid');
        if (!$memberData) {
            error('参数缺失');
        }
        $re = $cardModule->deleteCardForMember($wid,$memberData);
        if ($re['err_code'] == 0) {
            success();
        }
        error();
    }


}
