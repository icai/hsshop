<?php
/**
 * @author wuxiaoping <2018.03.20>
 * @desc  数据统计队列任务
 */
namespace App\Jobs;

use App\Lib\BLogger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\S\Foundation\Bi;

class DataStatistics implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    protected $data = [];
    protected $pageUrl;
    protected $pageId;
    protected $wid;
    protected $mid;
    protected $biKey;
    protected $fields = ['wid','uid','appid','type','p1','p2','biKey','source','createtime'];
    const  APPID = 1; //日后如果多个系统需要进行BI统计
    /**
     * 需要统计的访问页面
     * 1店铺主页 2微页面 3会员主页 4商品详情页
     * 5优惠券列表 6优惠券详情 7砸金蛋 8大转盘
     * 9签到 10订单列表 11售后\退款列表 12会员卡
     * 13赠品列表 14我的积分 15享立减商品详情页
     * 16底部logo跳转链接
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
        'shop/shareevent/product/showproductdetail',//何书哲 2018年8月7日 微商城享立减详情页
        'xcx/shareevent/product/showproductdetail',//何书哲 2018年8月7日 小程序享立减详情页
        'shop/activity/freeApply',//何书哲 2018年9月11日 公众号底部logo跳转链接
        'xcx/store/logoMicroPage',//何书哲 2018年9月11日 小程序底部logo跳转获取微页面
    ];

    public $xcx_urls = [
        'xcx/shareevent/product/showproductdetail',//何书哲 2018年8月7日 小程序享立减详情页
        'xcx/store/logoMicroPage',//何书哲 2018年9月11日 小程序底部logo跳转获取微页面
    ];

    public $tries = 3;
    public $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pageUrl,$pageId,$wid,$mid,$biKey)
    {
        //$this->data = $data;
        $this->pageUrl = $pageUrl;
        $this->pageId  = $pageId;
        $this->wid     = $wid;
        $this->mid     = $mid;
        $this->biKey   = $biKey;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            \Log::info(__FILE__.'文件,队列报错次数超限');
            return true;
        }

        $params = parse_url($this->pageUrl); 
        $bi = new Bi();
        $returnData = ['type' => 1,'p1' => 0,'p2' => 1,'biKey' => $this->biKey, 'url' => config('app.data_center_url')];
        $isValid = false;
        $source = 1;//来源 1:微商城 2：小程序
        foreach ($this->urls as $value) {
            if (strpos($params['path'], $value)) {
                switch ($value) {
                    case 'shop/index':
                        $returnData['type'] = 1;
                        $returnData['p1'] = $this->wid;
                        $returnData['p2'] = 1;
                        /*$storeInfo = WeixinService::getStageShop($wid);
                        $returnData['title'] = $storeInfo['shop_name'] ?? '店铺主页';*/
                        break;
                    case 'shop/microPage/index':
                        $returnData['type'] = 1;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 0;
                        /*$mircoData = MicroPageService::getRowByid($this->pageId);
                        $returnData['title'] = $mircoData['data']['page_title'] ?? '微页面';*/
                        break;
                    case 'shop/member/index':
                        $returnData['type'] = 4;
                        $returnData['p1'] = $this->wid;
                        $returnData['p2'] = 0;
                        //$returnData['title'] = '会员中心';
                        break;
                    case 'shop/product/detail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 1;
                        /*$product = ProductService::getDetail($this->pageId);
                        $returnData['title'] = $product['title'] ?? '商品详情页';*/
                        break;
                    case 'shop/grouppurchase/detail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 2;
                        /*$groupsRuleModule = new GroupsRuleModule();
                        $ruleData = $groupsRuleModule->getById($this->pageId);
                        $returnData['title'] = $ruleData['title'] ?? '拼团详情';*/
                        break;
                    case 'shop/seckill/detail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 3;
                        /*$seckill = (new SeckillModule())->getSeckillDetail($this->pageId);
                        $returnData['title'] = $seckill['title'] ?? '秒杀详情';*/
                        break;
                    case 'shop/member/coupons':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 1;
                        //$returnData['title'] = '我的优惠券列表';
                        break;
                    case 'shop/activity/couponDetail':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 1;
                        break;
                    case 'shop/activity/egg/index':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 3;
                        /*$eggData = (new EggModule())->getEggDetailById($this->pageId);
                        $returnData['title'] = $eggData['title'] ?? '砸金蛋详情';*/
                        break;
                    case 'shop/activity/wheel':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 2;
                        /*$wheelData = (new ActivityWheelService())->getRowById($this->pageId);
                        $returnData['title'] = $wheelData['title'] ?? '大转盘详情';*/
                        break;
                    case 'shop/point/sign':
                        $returnData['type'] = 3;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 4;
                        //$returnData['title'] = '签到详情';
                        break;
                    case 'shop/shareevent/product/showproductdetail':
                    case 'xcx/shareevent/product/showproductdetail':
                        $returnData['type'] = 2;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 4;
                        break;
                    case 'shop/activity/freeApply':
                    case 'xcx/store/logoMicroPage':
                        $returnData['type'] = 6;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 0;
                        break;
                    default:
                        $returnData['type'] = 5;
                        $returnData['p1'] = $this->pageId;
                        $returnData['p2'] = 0;
                        //$returnData['title'] = '其他页面';
                        break;
                }
                $isValid = true;
            }
        }

        //何书哲 2018年8月7日 来源是小程序
        foreach ($this->xcx_urls as $value) {
            if (strpos($params['path'], $value)) {
                $source = 2;
                break;
            }
        }

        if ($isValid) {
            $jobData = [
                'wid'        => $this->wid,
                'uid'        => $this->mid,
                'appid'      => self::APPID,
                'type'       => $returnData['type'],
                'p1'         => $returnData['p1'],
                'p2'         => $returnData['p2'],
                'biKey'      => $returnData['biKey'],
                'source'     => $source,
                'createtime' => time(),
            ];
            foreach ($jobData as $key => $value) {
                if (!in_array($key,$this->fields)) {
                    \Log::info('数据不完整');
                    return;
                }
            }
            if (empty($returnData['url'])) {
                \Log::info('远程访问路径为空，请先配置env文件的远程访问路径');
                return ;
            }
            $res = $bi->commitStatisticsData($returnData['url'],$jobData);
            if ($res != 200) {
                BLogger::getDCLogger('error','hsy_user_pv')->error($jobData);
            }
            \Log::info('hsy_user_pv 队列运行结束'.date('Y-m-d H:i:s',time()).'返回结果:'.$res);
        }
        
    }


}
