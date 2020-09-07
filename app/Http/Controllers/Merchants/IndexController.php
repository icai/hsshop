<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Module\CommonModule;
use App\Module\StoreModule;
use App\Services\Permission\WeixinRoleService;
use App\Services\Staff\InformationService;
use App\S\Staff\InformationTypeService;
use DB;
use Illuminate\Http\Request;
use OrderLogService;
use OrderService;
use PermissionService;
//use WeixinService;
use App\Services\Wechat\ApiService;
use App\Services\CashLogService;
use App\S\Staff\AfficheService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductService;
use QrCodeService;
use App\Module\FeeModule;
use App\S\Weixin\ShopService;
use Carbon\Carbon;

class IndexController extends Controller
{
    
    /**
     * 店铺管理后台首页
     *
     * @param   InformationService $informationService [资讯服务类实例]
     * @param   integer            $wid                [店铺id]
     * @return  view
     *
     * @author 黄东 406764368@qq.com
     * @version 2016年12月30日 12:05:01
     * @update 吴晓平 2018年08月22日  未过期店铺对之前的数据增加两个默认分组（未分组，卡密商品），并对已添加的商品进行分组
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function index(InformationService $informationService,InformationTypeService $informationTypeService,ShopService $shopService, $wid = 0)
    {
        // 查询店铺信息
        $wid = session('wid');
        //$weixinInfo = WeixinService::init('uid', session('userInfo')['id'])->getInfo($wid);
        $weixinInfo = $shopService->getRowById($wid);
        //还未上传店铺Logo到微信服务器(店铺已被授权的
        //情况下)
         if(empty($weixinInfo['weixin_logo_url']) && $weixinInfo['access_token']){
             $apiService = new ApiService();
             //创建店铺成功后设置一个默认的logo
             $filename = 'hsshop/image/static/huisouyun_120.png'; //相对地址
             $result = $apiService->uploadFile($wid,$filename);
             if(!isset($result['errcode']) && empty($result['errcode'])){
                 $weixin_logo_url = $result['url'];
                 $data['weixin_logo_url'] = $weixin_logo_url;
                 //WeixinService::init('uid', session('userInfo')['id'])->where(['id' => $wid])->update($data, false);
                 $shopService->update($wid,$data);
             }
         }
        // 查询订单信息
        $orderInfo = OrderService::init('wid', $wid)->where(['wid' => $wid])->statistical();
        $orderInfo['refundStatusCount'] = isset($orderInfo['refundStatus']) ? array_sum(array_column($orderInfo['refundStatus'], 'count')) : 0;
        $yestodayStr = date('Y-m-d', strtotime('-1 days'));

        /*全部订单统计 全部收入金额*/
        $orderTotalCount  = OrderLogService::statistical([2], [strtotime($weixinInfo['created_at']),time()]);
        /*分销打款金额*/
        $distributionDayList = (new CashLogService)->statical($wid,2,[strtotime($yestodayStr . ' 00:00:00')]);
        //减去分销打款的金额
        if(!empty($distributionDayList)){
            foreach($distributionDayList as $item){
                $orderTotalCount['income'] -= $item['money'];
            }
        }

        // 查询昨日订单数量（未付款+已付款）日志表是订单一生，只统计下单数就可以（不然会重复）
        $yestodayInfo = OrderLogService::statistical([1], [mktime(0,0,0,date('m'),date('d')-1,date('Y'))]);

        //查询昨天收入金额
        $yestodayIcome = OrderLogService::statistical([2], [mktime(0,0,0,date('m'),date('d')-1,date('Y'))]);
        if ($yestodayIcome) {
            $yestodayIcome['income'] = sprintf('%.2f',$yestodayIcome['income']); //保留两位小数
        }
        
        //获取首页资讯
        $information = $informationService->getFirstPageInfo();

        //获取s首页右侧分类
        $nav = [];
        $data = $informationTypeService->getNewsList('',0,2,'detail');
        if (isset($data['nav']) && $data['nav']) {
            $nav = array_slice($data['nav'],0,3);
            foreach ($nav as $key => &$value) {
                $value['newList'] = $informationTypeService->getListFromSecById($value['id']);
            }
        }
        $inforData = $nav;
        //dd($inforData);
        /*//获取s首页右侧分类
        $tmp = DB::table('information as i')->leftJoin('information_type as it','i.info_type','=','it.id')->where(function ($query){
                $query->Where('it.parent_id','89');
        })->whereNull('i.deleted_at')->order('i.id desc')->get(['i.id','i.title','it.id as type_id','it.name','it.type_path','it.parent_id'])->toArray();
        $inforData = [];
        foreach ($tmp as $val){
            list($id) = explode(',',$val->type_path);
            $val->title = str_limit($val->title,30);
            $inforData[$id][] = $val;
        }*/

        /*add wuxiaoping 2017.11.07 总金额显示小数点两位*/
        if (isset($orderTotalCount['income']) && $orderTotalCount['income']) {
            $orderTotalCount['income'] = sprintf('%.2f',$orderTotalCount['income']);
        }

        $affiche = (new  AfficheService())->getRowById('1');// fuguowei 20180206 用于公告
        if(isset($affiche['content']) && $affiche['content'])
        {
            $affiche['content'] = $this->pictures($affiche['content']);
        }

        //add by 张国军 2018-08-17 显示店铺版本
        $versionName="";
        $orderData=(new FeeModule())->showOrders(['wid'=>$wid]);
        if($orderData['errCode']==0&&!empty($orderData['data']))
        {
            $versionName=$orderData['data'][0]['serviceVersion']??'';
        }

        $res = (new WeixinRoleService())->init()->where(['wid'=>$wid])->getList(false);
        $beginTime=$res[0]['data'][0]['start_time'] ? date('Y.m.d', strtotime($res[0]['data'][0]['start_time'])) : '';
        $endTime=$res[0]['data'][0]['end_time'] ? date('Y.m.d', strtotime($res[0]['data'][0]['end_time'])) : '';

        $roleId = $res[0]['data'][0]['admin_role_id']??0;
        $frameTag = (new StoreModule())->getRedisFrameInfo($wid);
        $frameType = 0; //弹框是否显示
        // if ($roleId == 6){
        //     $frameType = 1;
        // }elseif ($roleId == 5 && !$frameTag){
        //     $frameType = 2;
        // }
        //add by 吴晓平 2018年08月22日  未过期店铺对之前的数据增加两个默认分组（未分组，卡密商品），并对已添加的商品进行分组
        /*$is_overdue = app('request')->get('is_overdue');
        $productService = new ProductService();
        if (!$is_overdue) {
            $groupService = new ProductGroupService();
            $defaultGroupData = $groupService->getGroupIdByDefault($wid);
            if (empty($defaultGroupData)) {
                $productService->createDefaultUnGroup($wid);
            }
            $productService->authSetDefaultGroup($wid);
        }*/

        /**生成下载详情页的二维码，并返回生成url**/
        $logo_water = '/public/home/image/logo_water.png';
        $url = config('app.url').'home/index/downLoadDetail';
        $qrcodePath = QrCodeService::create($url, $logo_water, 300,'app',20);
        $pathData = explode('public',$qrcodePath);
        $qrcodeUrl = count($pathData) >=2 ? $pathData[1] : '';
        /**
         * @Description: 数据库迁移新增的公告
         * @Author:      吴晓平 [wuxiaoping1559@dingtalk.com] at  2019年08月01
         */
        $isNotice = 0;
        if ((new Carbon)->lt(Carbon::parse('2019-08-24 07:00:00'))) {
            $isNotice = 1;
        }
        // end

        return view('merchants.index.index', [
            'title'           => '整体概况',
            'bodyClass'       => 'class=onlyone',
            'leftNav'         => 'index',
            'weixinInfo'      => $weixinInfo,
            'orderInfo'       => $orderInfo,
            'yestodayInfo'    => $yestodayInfo,
            'information'     => $information,
            'inforData'       => $inforData,
            'orderTotalCount' => $orderTotalCount,
            'yestodayIcome'   => $yestodayIcome,
            'affiche'         => $affiche ?? [],   // 公告
            'frameType'       => $frameType,
            'qrcodeUrl'       => $qrcodeUrl,
            'versionName'     => $versionName,
            'beginTime'       => $beginTime,
            'endTime'         => $endTime,
            'is_notice'       => $isNotice
        ]);
    }

    /**
     * @author fuguowei
     * @date 20180105
     * @desc  商品详情的图片加域名处理
     */
    public  function  pictures($str='')
    {
        preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/",$str,$matches);
        $img_src_arr = $matches[3];
        $url =config('app.source_img_url');
        if($img_src_arr)
        {
            foreach($img_src_arr as $k=>$v)
            {
                $http =strpos($v,'ttp:');
                $https =strpos($v,'ttps:');
                if($http != 1 && $https !=1)
                {
                    $str = str_replace($v,$url."$v",$str);
                }
            }
        }
        return $str;
    }

    /**
     * 获取微商城二维码
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年08月31日
     */
    public function qrCode(Request $request)
    {
        $request->input('url') || error('二维码页面路径不能为空');
        success('', '', (new CommonModule())->qrCode(session('wid'), $request->input('url')));
    }

    /**
     * 下载微商城二维码
     * @param Request $request 参数类
     * @return file
     * @author 许立 2018年08月31日
     */
    public function qrCodeDownload(Request $request)
    {
        $request->input('url') || error('二维码页面路径不能为空');
        return (new CommonModule())->qrCodeDownload(session('wid'), $request->input('url'));
    }

    public function download(Request $request)
    {
        $url = $request->input('url', '');
        $url || error('路径不能为空');
        header("Content-type: application/octet-stream");
        header("Content-Disposition:attachment;filename = " . time() . '.png');
        // 获取https头像 取消ssl验证
        $arrContextOptions = [
            "ssl" => [
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ],
        ];

        echo file_get_contents($url, false, stream_context_create($arrContextOptions));
        exit;
    }

    /**
     * 获取小程序二维码
     * @param Request $request 参数类
     * @return json
     * @author 许立 2018年08月31日
     */
    public function qrCodeXcx(Request $request)
    {
        $request->input('url') || error('二维码页面路径不能为空');
        success('', '', (new CommonModule())->qrCode(session('wid'), $request->input('url'), 1));
    }

    /**
     * 下载小程序二维码
     * @param Request $request 参数类
     * @return file
     * @author 许立 2018年08月31日
     */
    public function qrCodeDownloadXcx(Request $request)
    {
        $request->input('url') || error('二维码页面路径不能为空');
        (new CommonModule())->qrCodeDownload(session('wid'), $request->input('url'), 1);
    }
}
