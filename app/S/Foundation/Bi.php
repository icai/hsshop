<?php
/*
 * @desc BI模块
 * @Author: chenwenhao
 */
namespace App\S\Foundation;

use App\Lib\Redis\Bi as BiRedis;
use Cookie;
use App\Jobs\DataStatistics;
use DB;
use App\S\WXXCX\WXXCXConfigService;
use App\Lib\WXXCX\ThirdPlatform;
use App\Jobs\StatisticsXCXData;
use WeixinService;
use MicroPageService;
use ProductService;
use App\Module\GroupsRuleModule;
use App\Module\SeckillModule;
use App\Module\EggModule;
use App\S\Wheel\ActivityWheelService;
use SignService;
use App\S\Wechat\WeixinConfigSubService;
use App\Service\Wechat\ApiService;
use App\Jobs\UserSummary;
use App\Jobs\UserCumulate;
use App\S\WXXCX\XCXStatisticsLogService;
use App\Jobs\RepeatStatisticsXCXData;
use App\Model\WXXCXConfig;

class Bi
{
    const  APPID = 1; //日后如果多个系统需要进行BI统计
    public static $biKey;
    protected $dcDB;

    const MIC_PAGE_VIEW_TYPE = 1001;
    const PRODUCT_VIEW_TYPE  = 1002;

    public static $modelName = [
        1001    =>  'PageView',
        1002    =>  'PageView'
    ];

    /**
     * 需要统计的访问页面
     * 1店铺主页 2微页面 3会员主页 4商品详情页
     * 5优惠券列表 6优惠券详情 7砸金蛋 8大转盘
     * 9签到 10订单列表 11售后\退款列表 12会员卡
     * 13赠品列表 14我的积分
     * @var [type]
     */
    public $urls =[
        'shop/index',            
        'shop/microPage/index',  
        'shop/member/index',     
        'shop/product/detail',   
        'shop/grouppurchase/detail',
        'shop/seckill/detail',
        'shop/member/coupons',
        'shop/activity/couponDetail',
        'shop/activity/egg/index',
        'shop/activity/wheel',
        'shop/point/sign',
        'shop/order/index',
        'shop/order/refund',
        'shop/member/mycards',
        'shop/activity/myGift',
        'shop/point/mypoint',
    ];

    public function __construct()
    {   
        $this->dcDB = DB::connection('mysql_dc_log');
    }

    public static function track(int $function, array $params = [])
    {
        if( empty(config('app.bi')) ) return;
        if (empty($function)) return;
        
        $modelName = self::$modelName[$function];
        $m = '\\App\\Model\\Bi\\' . $modelName;

        $params['appid'] = self::APPID;
        $model = new $m();
        $model->insertGetId($params);
        return true;
    }

    // //考虑并发的写法
    // public static function track(int $function, array $params = [])
    // {
    //  if (empty($function)) return;

    //  if (!empty($params))
    //      $params = json_encode($params);
        
    //  $sendData = $function . "|" . $params;

    //     $redis = new BiRedis();
    //     $redis->insert($sendData);
    //     return true;
    // }

    public static function getUniqueKey()
    {
        $biKey = session('biKey');
        if (empty($biKey)) {
            $biKey =  app('request')->cookie('biKey');
            if (empty($biKey)) {
                $biKey = app('request')->session()->getId();
                Cookie::queue('biKey', $biKey, 3600 * 24);
            }
            app('request')->session()->put('biKey', $biKey);
            app('request')->session()->save();
        }
        self::$biKey = $biKey;
        return $biKey;
    }

    //本来微页面 商品页是分开统计的 后面由于一个需求
    /**
     * 微页面 访问量统计
     * @param wid $wid
     * @param uid $uid
     * @param pageId $pageId
     * @param isHome 1 主页  0 非主页
     * @return array
     * @author cwh
     */
    public static function micPageView($wid, $uid, $pageId, $isHome = 0, $source = "default") 
    {
        $biKey = self::$biKey ?: self::getUniqueKey();
        $params = [
            "uid"        => (int)$uid,
            "wid"        => $wid,
            "bikey"      => $biKey,
            "type"       => 1,
            "p1"         => $pageId,
            "p2"         => $isHome,
            "source"     => $source,
            "createtime"     => date("YmdHis",time()),
        ];
        self::track(self::MIC_PAGE_VIEW_TYPE, $params);
    }

    /**
     * 商品详情页访问
     * @param wid $wid
     * @param uid $uid
     * @param productId $productId
     * @param productType   1 团购  2 秒杀 
     * @return array
     * @author cwh
     */
    public static function productView($wid, $uid, $productId, $productType, $source = "default") 
    {
        $biKey = self::$biKey ?: self::getUniqueKey();
        $params = [
            "uid"        => (int)$uid,
            "wid"        => $wid,
            "bikey"      => $biKey,
            "type"       => 2,
            "p1"         => $productId,
            "p2"         => $productType,
            "source"     => $source,
            "createtime"     => date("YmdHis",time()),
        ];
        self::track(self::PRODUCT_VIEW_TYPE , $params);
    }

    /**
     * 获取访问url时的有效数据
     * @param  [string]  $url    [远程访问url地址]
     * @param  [int]  $wid       [店铺id]
     * @param  [int]  $mid       [用户id]
     * @param  integer $pageId   [访问相应页面的id]
     * @return [type]            [description]
     */
    public function getVisitData($url,$wid,$mid,$pageId=0)
    {
        $biKey = self::$biKey ?: self::getUniqueKey(); //唯一标识
        $params = parse_url($url); 
        /* type-类型（1-微页面，2商品页，3营销活动页,4会员主页,5其他）
         *type == 1:
         *   p1:微页面的id
         *   p2: 1-主页 0-非主页
         *type == 2:
         *   p1:商品id
         *   p2: 1-普通商品 2-多人拼团详情  3-秒杀详情 4-享立减详情 5-集赞
         *type == 3:
         *   p1:营销活动id
         *   p2: 1-优惠券  2-大转盘  3-砸金蛋  4-签到
         */
        $returnData = ['type' => 1,'p1' => 0,'p2' => 1,'biKey' => $biKey, 'url' => config('app.data_center_url')];
        $isValid = false;
        foreach ($this->urls as $value) {
            if (strpos($params['path'], $value)) {
                switch ($value) {
                    case 'shop/index':
                        $returnData['type'] = 1;
                        $returnData['p1'] = $wid;
                        $returnData['p2'] = 1;
                        /*$storeInfo = WeixinService::getStageShop($wid);
                        $returnData['title'] = $storeInfo['shop_name'] ?? '店铺主页';*/
                        break;
                    case 'shop/microPage/index':
                        $returnData['type'] = 1;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 0;
                        /*$mircoData = MicroPageService::getRowByid($pageId);
                        $returnData['title'] = $mircoData['data']['page_title'] ?? '微页面';*/
                        break;
                    case 'shop/member/index':
                        $returnData['type'] = 4;
                        $returnData['p1'] = $wid;
                        $returnData['p2'] = 0;
                        //$returnData['title'] = '会员中心';
                        break;
                    case 'shop/product/detail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 1;
                        /*$product = ProductService::getDetail($pageId);
                        $returnData['title'] = $product['title'] ?? '商品详情页';*/
                        break;
                    case 'shop/grouppurchase/detail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 2;
                        $groupsRuleModule = new GroupsRuleModule();
                        /*$ruleData = $groupsRuleModule->getById($pageId);
                        $returnData['title'] = $ruleData['title'] ?? '拼团详情';*/
                        break;
                    case 'shop/seckill/detail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 3;
                        /*$seckill = (new SeckillModule())->getSeckillDetail($pageId);
                        $returnData['title'] = $seckill['title'] ?? '秒杀详情';*/
                        break;
                    case 'shop/member/coupons':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 1;
                        //$returnData['title'] = '我的优惠券列表';
                        break;
                    case 'shop/activity/couponDetail':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 1;
                        break;
                    case 'shop/activity/egg/index':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 3;
                        /*$eggData = (new EggModule())->getEggDetailById($pageId);
                        $returnData['title'] = $eggData['title'] ?? '砸金蛋详情';*/
                        break;
                    case 'shop/activity/wheel':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 2;
                        /*$wheelData = (new ActivityWheelService())->getRowById($pageId);
                        $returnData['title'] = $wheelData['title'] ?? '大转盘详情';*/
                        break;
                    case 'shop/point/sign':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 4;
                        //$returnData['title'] = '签到详情';
                        break;
                    default:
                        $returnData['type'] = 5;
                        $returnData['p1'] = $pageId;
                        $returnData['p2'] = 0;
                        //$returnData['title'] = '其他页面';
                        break;
                }
                $isValid = true;
            }
        }
        if ($isValid) {
            $jobData = [
                'wid'        => $wid,
                'uid'        => $mid,
                'appid'      => self::APPID,
                'type'       => $returnData['type'],
                'p1'         => $returnData['p1'],
                'p2'         => $returnData['p2'],
                'biKey'      => $returnData['biKey'],
                'source'     => 1,
                'createtime' => strtotime(date('Y-m-d',time())),
            ];
            $job = new DataStatistics($jobData,$returnData['url']);
            dispatch($job->onQueue('DataStatistics')->onConnection('dc'));
        }
        return $returnData;
    }

    /**
     * 提交统计数据
     * @author wuxiaoping <2018.03.20>
     * @param array $data 提交的对应表的字段数据，包含以下字段
     * wid-店铺id， uid-用户id，  appId-标识（默认是汇搜云系统，以后可能会有jave app等使用）
     * type-类型（1-微页面，2商品页，3营销活动页，4会员主页，5其他，6底部logo链接）
     *     type == 1:
     *         p1:微页面的id
     *         p2: 1-主页 0-非主页
     *     type == 2:
     *         p1:商品id
     *         p2: 1-普通商品 2-多人拼团详情  3-秒杀详情 4-享立减详情 5-集赞
     *     type == 3:
     *         p1:营销活动id
     *         p2: 1-优惠券  2-大转盘  3-砸金蛋  4-签到
     *     type == 6:
     *         p1:微页面id或0(公众号)
     *         p2:0
     * bikey(区分用户唯一标识)，source(1-微商城  2-小程序) createtime(访问时间，年月日时分秒)
     * @return [type] [description]
     */
    public function commitStatisticsData($url,$data)
    {
        $result = $this->request_post($url, json_encode($data, JSON_UNESCAPED_UNICODE));
        return $result;
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param mix $datas
     */
    public function request_post($url = '', $datas) {
        if (empty($url) || empty($datas)) {
            return false;
        }
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, false);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//要求结果为字符串且输出到屏幕上
        if ( !empty($datas) ) {
            // 发送一个常规的Post请求
            curl_setopt($ch, CURLOPT_POST, true);
            // Post提交的数据包
            curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        }
        $result = curl_exec($ch);//运行curl
        $info   = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $info;
    }

    /**
     * 模拟get进行url请求
     * @param string $url
     * @param mix $datas
     */
    public function request_get($url,$timeout = 5)
    {

        if($url == "" || $timeout <= 0){
            return false;
        }
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 1);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $timeout);
        //执行命令
        $result = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $result;

    }

    /**
     * 统计小程序的每天的数据为前一天为止的一个月内数据
     * @param  [int] $wid [店铺id]
     * @return [type]      [description]
     */
    public function statisticsXCXData($wid=0,$date='')
    {
        if(!$wid && empty($date)) {
            \Log::info('暂时只支持以下三种统计数据：');
            \Log::info('支持指定小程序并统计指定日期的数据');
            \Log::info('支持指定日期并统计不同小程序的数据');
            \Log::info('支持指定小程序统计一个月内不同日期的数据');
            exit;
        }
        $where['current_status'] = 0;
        $where['verify_type'] = 0; //表示微信已认证
        $wxConfigService = new WXXCXConfigService();
        if ($wid) {
            $data = $wxConfigService->getRow($wid);
            if ($data['data']) {
                if ($date) {
                    $selectSql = "SELECT count(*) as num from dc_viewxcx_count where wid=".$wid.' and createTime='."'".strtotime($date)."'";
                    $res = $this->dcDB->select($selectSql);
                    if ($res[0]->num >= 1) {
                        \Log::info($wid.'---该小程序统计日期--'.$date.'--数据已存在');
                        exit;
                    }
                    \Log::info('指定的小程序并统计指定的日期数据');
                    $saveData['wid'] = $wid;
                    $saveData['beginDate'] = $date;
                    $saveData['endDate']   = $date;
                    $job = new StatisticsXCXData($saveData);
                }else {
                    $createtime = $data['data']['created_at'];
                    $currentTime = time();
                    $thirtyDaysAgo = strtotime('-30 day');
                    /* 由于小程序授权的时间点不一样，这里按从当天算到一个月内的数据开始入库，
                     * 再往前的日期不作统计，默认设置为0  
                     */
                    if ((strtotime($createtime) > $thirtyDaysAgo)) {
                        $startDate = date('Ymd',strtotime($createtime));
                    }else{
                        $startDate = date('Ymd',strtotime('-30 day'));
                    }
                    \Log::info('指定的小程序并统计一个月内不同日期的数据');
                    //获取一个月内的日期（是一个数组）
                    $days = $this->getDaysForMonth($startDate,date('Ymd',time()));
                    if ($days) {
                        foreach ($days as $key => $value) {
                            $selectSql = "SELECT count(*) as num from dc_viewxcx_count where wid=".$wid.' and createTime='."'".strtotime($value)."'";
                            $res = $this->dcDB->select($selectSql);
                            if ($res[0]->num >= 1) {
                                \Log::info($wid.'---该小程序统计日期--'.$value.'--数据已存在');
                                continue;
                            }
                            $saveData[$key]['wid'] = $wid;
                            $saveData[$key]['beginDate'] = $value;
                            $saveData[$key]['endDate'] = $value;
                        }
                    }
                    $job = new StatisticsXCXData($saveData);
                }
                dispatch($job->onQueue('StatisticsXCXData')->onConnection('dc'));
                return true;
            }
        }else {
            \Log::info('不同的小程序统计指定日期的数据');
            WXXCXConfig::where('current_status',0)->where('verify_type',0)->chunk(100,function($data) use ($date) {
                foreach ($data as $key => $value) {
                    if ($date) {
                        $selectSql = "SELECT count(*) as num from dc_viewxcx_count where wid=".$value['wid'].' and createTime='."'".strtotime($date)."'";
                        $res = $this->dcDB->select($selectSql);
                        if ($res[0]->num >= 1) {
                            \Log::info($value['wid'].'---该小程序统计日期--'.$date.'--数据已存在');
                            continue;
                        }
                        $saveData['wid']       = $value['wid'];
                        $saveData['beginDate'] = $date;
                        $saveData['endDate']   = $date;
                        $job = new StatisticsXCXData($saveData);
                        dispatch($job->onQueue('StatisticsXCXData')->onConnection('dc'));
                    }else {
                        \Log::info('数据类型不正确');
                        break;
                    }
                }
            });
            return true;
        }
        return false;  
    }

    /**
     * 更新小程序失败的统计数据
     * @param  [type] $date [指定更新的日期]
     * @return [type]       [description]
     */
    public function updateXcxStatistics($date)
    {
        $returnData = ['errCode' => 0,'errMsg' => '','data' => []];
        try {
            WXXCXConfig::where('current_status',0)->where('verify_type',0)->chunk(100,function($data) use ($date) {
                foreach ($data as $key => $value) {
                    $selectSql = "SELECT count(*) as num from dc_viewxcx_count where wid=".$value['wid'].' and createTime='."'".strtotime($date)."'";
                    $res = $this->dcDB->select($selectSql);
                    if ($res[0]->num >= 1) {
                        \Log::info($value['wid'].'---该小程序统计日期--'.$date.'--数据已存在');
                        continue;
                    }
                    $saveData['wid']       = $value['wid'];
                    $saveData['beginDate'] = $date;
                    $saveData['endDate']   = $date;
                    $job = new StatisticsXCXData($saveData);
                    dispatch($job->onQueue('StatisticsXCXData')->onConnection('dc'));
                }
            });
        } catch (\Exception $e) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = $e->getMessage();
        }
        return $returnData;
        
    }

    /**
     * 小程序统计数据调用微信接口入库处理（日趋势）
     * @param  [array] $data [对接微信口需传入的时间数据（二维数组：批量插入）]
     * @return [type]       [description]
     */
    public function commitStatisticsXCXData($data=[])
    {
        $thirdPlatform = new ThirdPlatform();
        $createTime = strtotime($data['beginDate']);
        $result = $thirdPlatform->visitTrendForDaily($data);
        $failData = []; //保存统计失败数据
        $BiRedis = new BiRedis();
        if ($result['errCode'] == 0 && !empty($result['data'])) {
            $selectSql = "SELECT count(*) as num from dc_viewxcx_count where wid=".$data['wid'].' and createTime='.$createTime;
            $res = $this->dcDB->select($selectSql);
            if ($res[0]->num >= 1) {
                \Log::info($data['wid'].'--小程序统计日期--'.$data['beginDate'].'-已统计入库');
            }else {
                $insertSql = 'INSERT INTO dc_viewxcx_count(wid,xcx_pv,xcx_uv,visit_uv_new,stay_time_uv,visit_depth,createTime) VALUES ';
                $insertSql .= "({$data['wid']},{$result['data'][0]['visit_pv']},{$result['data'][0]['visit_uv']},{$result['data'][0]['visit_uv_new']},{$result['data'][0]['stay_time_uv']},{$result['data'][0]['visit_depth']},{$createTime})";
                if ($this->dcDB->insert($insertSql)) {
                    $saveData['wid']        = $data['wid'];
                    $saveData['start_date'] = $data['beginDate'];
                    $saveData['status']     = 1;
                    $saveData['log']        = '数据入库成功';
                    $this->saveXcxStatisticsToDatabase($saveData,'数据入库成功');
                }else {
                    $saveData['wid']        = $data['wid'];
                    $saveData['start_date'] = $data['beginDate'];
                    $saveData['status']     = 0;
                    $saveData['log']        = '数据入库失败';
                    $this->saveXcxStatisticsToDatabase($saveData,'数据入库失败');
                }
            }
        }else if ($result['errCode'] == 0 && empty($result['data'])) {
            \Log::info($data['wid'].'小程序接口统计数据日期----'.$data['beginDate'].'--数据未有记录');
        }else {
            $saveData['wid']        = $data['wid'];
            $saveData['start_date'] = $data['beginDate'];
            $saveData['status']     = 0;
            $saveData['log']        = $result['errMsg'];
            $this->saveXcxStatisticsToDatabase($saveData,$result['errMsg']);
        }
        return false;
    }

    /**
     * 统计数据入库
     * @param  [array] $saveData [要保存到数据库的数组数据]
     * @param  [string] $msg      [打印日志提示的信息]
     * @return [type]           [description]
     */
    public function saveXcxStatisticsToDatabase($saveData,$msg) 
    {
        $xcxStatisticsLogService = new XCXStatisticsLogService();
        $where['wid'] = $saveData['wid'];
        $where['start_date'] = $saveData['start_date'];
        if ($id = $xcxStatisticsLogService->isHaveData($where)) {
            $xcxStatisticsLogService->update($id,$saveData);
            \Log::info($saveData['wid'].'--小程序统计日期--'.$saveData['start_date'].'--更新'.$msg);
        }else {
            $xcxStatisticsLogService->add($saveData);
            \Log::info($saveData['wid'].'--小程序统计日期--'.$saveData['start_date'].'--'.$msg);
        }
    }

    /**
     * 手动更新统计入库失败的数据
     * @return [type] [description]
     */
    public function updateErrorStatistics($data)
    {
        $nums = count($data);
        if ($nums == 1) {
            $saveData[$key]['wid']       = $data[0]['wid'];
            $saveData[$key]['beginDate'] = $data[0]['start_date'];
            $saveData[$key]['endDate']   = $data[0]['start_date'];
            $job = new StatisticsXCXData($saveData);
            dispatch($job->onQueue('StatisticsXCXData')->onConnection('dc'));
        }else {
            foreach ($data as $key => $value) {
                $saveData['wid']       = $value['wid'];
                $saveData['beginDate'] = $value['start_date'];
                $saveData['endDate']   = $value['start_date'];
                $job = new StatisticsXCXData($saveData);
                dispatch($job->onQueue('StatisticsXCXData')->onConnection('dc'));
            }
        }
    }

    /**
     * 小程序统计失败数据重新跑脚本
     * @author 吴晓平 2018年06月29日
     * @return [type] [description]
     * @update 何书哲 2018年10月23日 修改小程序统计失败脚本
     */
    public function repeatCommitXcxData()
    {
        //查询小程序统计失败的数据
        DB::table('xcx_statistics_log')->select('wid', 'start_date')
            ->where('status', 0)
            ->chunk(50, function ($logs) {
                foreach ($logs as $log) {
                    $queueData = [
                        'wid' => $log->wid,
                        'beginDate' => $log->start_date,
                        'endDate' => $log->start_date
                    ];
                    dispatch((new RepeatStatisticsXCXData($queueData))->onQueue('RepeatStatisticsXCXData')->onConnection('dc'));
                }
            });
    }

    /**
     * 列表两个日期间的所有日期（不包含当天的日期）
     * @param  [date] $startTime   [要开始计的时间]
     * @param  [date] $currentTime [当前的时间]
     * @return [type]              [description]
     */
    public function getDaysForMonth($startTime,$currentTime)
    {
        $time1 = strtotime($startTime); // 自动为00:00:00 时分秒
        $time2 = strtotime($currentTime);
        $days  = [];
        while( ($time1 = strtotime('+1 days', $time1)) < $time2) {
            $days[] = date('Ymd',$time1); // 取得递增日期; 
        }
        return $days;
    }

    /**
     *访问分布
     */
    public function commitAccessSource($data)
    {
        $thirdPlatform = new ThirdPlatform();
        try {
            foreach ($data as $key => $value) {
                $createTime = strtotime($value['beginDate']);
                $result = $thirdPlatform->visitDistribution($value);
                if ($result['errCode'] == 0) {
                    $selectSql = "SELECT id,visit_list from dc_viewxcx_count where wid=".$value['wid'].' and createTime='.$createTime;
                    $res = $this->dcDB->select($selectSql);
                    if ($res[0]->id && empty($res[0]->visit_list)) {
                        $updateSql = 'UPDATE dc_viewxcx_count SET visit_list='.json_encode($result['data'][0]).' where wid='.$value['wid'].' and createTime='.$createTime;
                        if ($this->dcDB->update($updateSql)) {
                            \Log::info($value['wid'].'--小程序统计日期--'.$value['beginDate'].'--更新访问来源数据成功');
                        }else {
                            \Log::info($value['wid'].'--小程序统计日期--'.$value['beginDate'].'--更新访问来源数据失败');
                            continue;
                        }
                    }else if ($res[0]->id && !empty($res[0]->visit_list)) {
                        \Log::info('小程序接口访问来源数据日期----'.$value['beginDate'].'--已存在');
                        continue;
                    }else {
                        \Log::info('小程序接口统计数据----'.$value['beginDate'].'--还未统计');
                        continue;
                    }
                }else {
                    \Log::info('小程序接口访问来源数据失败原因----'.$result['errMsg']);
                    break;
                }
            }
        } catch (Exception $e) {
            \Log::info('小程序接口访问来源数据失败原因----'.$e->getMessage());
        }
        return false;
    }

    /**
     * 根据需求获取90天的日期
     * @return [type] [description]
     */
    public function getDates()
    {
        $time1 = strtotime('-91 days'); // 自动为00:00:00 时分秒
        $time2 = time();
        $days  = $dates = [];
        while( ($time1 = strtotime('+1 days', $time1)) < $time2) {
            $days[] = date('Y-m-d',$time1); // 取得递增日期; 
        }
        if ($days) {
            $pageNum = ceil(count($days)/7); 
            //根据微信接口最大的时间跨度进行分组
            for ($i = 1;$i <= $pageNum;$i++){
                foreach ($days as $key => $day) {
                    $start = ($i-1) * 7;
                    $end   = $i*7-1;
                    if ($start <= $key && $key <= $end) {
                        $dates[$i][] = $day;
                    }
                }
            }
        }
        return $dates;
    }

    /**
     * 根据店铺id,时间段获取微信公众号的粉丝量（还有状态）
     * @param  integer $wid   [description]
     * @param  array   $dates [description]
     * $dates 事例 ['begin_date' => 2017-12-02,'end_date' => '2017-12-07']
     * @return [type]         [description]
     */
    public function getWeixinFansData($wid=0,$begin_date='',$end_date='')
    {
        if ((empty($begin_date) && !empty($end_date)) || (!empty($begin_date) && empty($end_date))) {
            \Log::info('日期时间段不完整，可以指定开始时间和结束时间或不指定日期统计90天内的数据');
            return ;
        }
        $dates = [];
        if (!empty($begin_date) && !empty($end_date)) {
            $dates['begin_date'] = $begin_date;
            $dates['end_date']   = $end_date;
        }
        $job = new UserSummary($wid,$dates);
        dispatch($job->onQueue('UserSummary')->onConnection('dc'));         
    }

    /**
     * 微信粉丝统计数据入库处理
     * @param  [int] $wid  [店铺id]
     * @param  [array] $data [入库数据]
     * @return [type]       [description]
     */
    public function storeUserSersummary($wid,$data)
    {
        $filename = public_path().'fansLog/errorlog.txt';
        $returnData['success'] = $returnData['error'] = 0;
        try {
            foreach ($data['list'] as $key => $value) {
                $time = strtotime($value['ref_date']);
                $selectSql = "SELECT count(*) as num from dc_usersummary where wid=".$wid." and createtime=".$time;
                $rs = $this->dcDB->select($selectSql);
                if ($rs[0]->num >= 1) {
                    \Log::info($wid.'--该店铺在日期'.$value['ref_date'].'粉丝统计数据已存在');
                }else {
                    $insertSql = "INSERT INTO dc_usersummary(wid,user_source,new_user,cancel_user,createtime) VALUES ({$wid},{$value['user_source']},{$value['new_user']},{$value['cancel_user']},{$time})";
                    if (!$this->dcDB->insert($insertSql)) {
                        \Log::info('店铺'.$wid,'统计日期'.$value['ref_date'].'微信粉丝数据失败');
                        $returnData['error']++;
                        $storeData['wid'] = $wid;
                        $storeData['date'] = $value['ref_date'];
                        $content = json_encode($storeData);
                        $myfile = fopen($filename,'a');
                        fwrite($myfile,$content);
                        fclose($myfile);
                    }else {
                        $returnData['success']++;
                    }
                }
                
            }
        } catch (\Exception $e) {
            \Log::info('微信粉丝统计数据入库处理错误原因--'.$e->getMessage());
            $returnData['error']++;
        }
        return $returnData;
    }

    /**
     * 根据店铺id,时间段获取微信公众号的粉丝量（还有状态）
     * @param  integer $wid   [description]
     * @param  array   $dates [description]
     * $dates 事例 ['begin_date' => 2017-12-02,'end_date' => '2017-12-07']
     * @return [type]         [description]
     */
    public function getWeixinFansCumulateData($wid=0,$begin_date='',$end_date='')
    {
        if ((empty($begin_date) && !empty($end_date)) || (!empty($begin_date) && empty($end_date))) {
            \Log::info('日期时间段不完整，可以指定开始时间和结束时间或不指定日期统计90天内的数据');
            return ;
        }
        $dates = [];
        if (!empty($begin_date) && !empty($end_date)) {
            $dates['begin_date'] = $begin_date;
            $dates['end_date']   = $end_date;
        }
        $job = new UserCumulate($wid,$dates);
        dispatch($job->onQueue('UserCumulate')->onConnection('dc'));         
    }

    /**
     * 微信粉丝总量统计入库
     * @param  [int] $wid  [店铺id]
     * @param  [array] $data [入库数据]
     * @return [type]       [description]
     */
    public function storeUserCumulate($wid,$data)
    {
        $filename = public_path().'fansLog/errorlog.txt';
        $returnData['success'] = $returnData['error'] = 0;

        try {
            foreach ($data['list'] as $key => $value) {
                $time = strtotime($value['ref_date']);
                $selectSql = "SELECT count(*) as num from dc_usercumulate where wid=".$wid." and createtime=".$time;
                $rs = $this->dcDB->select($selectSql);
                if ($rs[0]->num >= 1) {
                    \Log::info($wid.'--该店铺在日期'.$value['ref_date'].'粉丝总量统计数据已存在');
                }else {
                    $insertSql = "INSERT INTO dc_usercumulate(wid,user_source,cumulate_user,createtime) VALUES ({$wid},{$value['user_source']},{$value['cumulate_user']},{$time})";
                    if (!$this->dcDB->insert($insertSql)) {
                        \Log::info('店铺'.$wid,'统计日期'.$value['ref_date'].'微信粉丝数据失败');
                        $returnData['error']++;
                        $storeData['wid'] = $wid;
                        $storeData['date'] = $value['ref_date'];
                        $content = json_encode($storeData);
                        $myfile = fopen($filename,'a');
                        fwrite($myfile,$content);
                        fclose($myfile);
                    }else {
                        $returnData['success']++;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::info('微信粉丝统计数据入库处理错误原因--'.$e->getMessage());
            $returnData['error']++;
        }
        return $returnData;
    }


}



