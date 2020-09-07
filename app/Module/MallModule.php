<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/22
 * Time: 10:48
 */

namespace App\Module;

use App\Model\UserFile;
use App\S\Member\MemberService;
use App\S\Product\ProductGroupService;
use App\S\Product\ProductImgService;
use App\S\Product\ProductMsgService;
use App\S\Product\ProductPropsToValuesService;
use App\S\Product\ProductService;
use App\S\ShareEvent\ShareEventService;
use App\S\Store\TemplateMarketService;
use App\S\Wechat\WeixinImportService;
use App\Services\DistributeTemplateService;
use App\Services\FreightService;
use App\Services\Order\OrderRefundService;
use App\Services\Permission\WeixinRoleService;
use App\Services\WeixinService;
use FileInfoService as StoreFileInfoService;
use Log;
use MemberHomeService as StoreMemberHomeService;
use MicroPageNoticeService as StoreMicroPageNoticeService;
use MicroPageService as StoreMicroPageService;
use MicroPageTemplateService;
use ProductService as StoreProductService;
use StoreNavService as StoreStoreNavService;
use WeixinService as StoreWeixinService;
use App\S\Customer\KefuService;
use App\S\PublicShareService;
use WXXCXMicroPageService;
use App\Lib\BLogger;
use App\S\ShareEvent\LiEventService;
use OrderService;
use App\S\Store\StoreTopNavService;
use MicPage;
use App\S\Member\MemberHomeModuleService;
use Bi;
use App\S\Weixin\ShopService;

class MallModule
{

    /**
     * 移动端店铺主页和会员主页
     * @param int $wid 店铺id
     * @param int $type 类型 1：店铺首页 2:会员主页
     * @param bool $filter 是否过滤
     * @return array
     * @update 何书哲 2018年8月30日  整合移动端店铺首页和会员主页
     */
    public function processMobileData($wid = 0, $type = 1, $filter = true, $mid = 0)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        if (empty($wid)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
        //获取公共广告
        $microPageNoticeData = $this->getMicroPageNoticeData($wid, $type == 1 ? 5 : 6, $filter);
        $returnData['data'] = array_merge($returnData['data'], $microPageNoticeData);
        //获取底部导航
        $storeNavData = $this->getStoreNavData($wid, $type == 1 ? 1 : 2);
        $returnData['data'] = array_merge($returnData['data'], $storeNavData);
        if ($type == 1) {
            //获取店铺信息
            $microPageData = $this->getMicroPageData($wid, $filter);
            isset($returnData['data']['container']) ? $returnData['data']['header'] = $microPageData['data'] : $returnData['data']['container'] = $microPageData['data'];
            unset($microPageData['data']);
            $returnData['data'] = array_merge($returnData['data'], $microPageData);
            //获取顶部导航
            $storeTopNavData = $this->getStoreTopNavData($wid);
            $returnData['data'] = array_merge($returnData['data'], $storeTopNavData);
            //获取店铺信息
            $storeData = $this->getStoreData($wid);
            $returnData['data'] = array_merge($returnData['data'], $storeData);
            //店铺主页数据特殊处理
            if (isset($returnData['data']['pageid'])) {
                Bi::micPageView($wid, session('mid'), $returnData['data']['pageid'], 1);
            }
            if (isset($returnData['data']['container'])) {
                $returnData['data']['container'] = str_replace('videoreplace', '<video width=\"100%\" height=\"280\" controls=\"controls\" poster=\"/ueditor/php/upload/image/20171103/1509689299520580.jpg\"><source src=\"https://upx.cdn.huisou.cn/wscphp/music/aa.mp4 \" type=\"video/mp4\"></video>', $returnData['data']['container']);
            }
            //add by zhangyh
            $returnData['data']['container'] = ProductModule::addProductContentHost($returnData['data']['container']);
            $returnData['data']['header'] = ProductModule::addProductContentHost($returnData['data']['header']);
            $returnData['data']['info'] = ProductModule::addProductContentHost($returnData['data']['info']);
        }
        //获取会员主页
        if ($type == 2) {
            $storeMemberHomeData = $this->getStoreMemberHomeData($wid, $filter, $mid);
            isset($returnData['data']['container']) ? $returnData['data']['header'] = $storeMemberHomeData['data'] : $returnData['data']['container'] = $storeMemberHomeData['data'];
            unset($storeMemberHomeData['data']);
            $returnData['data'] = array_merge($returnData['data'], $storeMemberHomeData);
            //会员主页数据特殊处理
            //add by zhangyh
            $returnData['data']['container'] = ProductModule::addProductContentHost($returnData['data']['container'] ?? '');
            $returnData['data']['footer'] = ProductModule::addProductContentHost($returnData['data']['footer'] ?? '');
            $returnData['data']['header'] = ProductModule::addProductContentHost($returnData['data']['header'] ?? '');
        }
        return $returnData;
    }

    /**
     * 获取店铺顶部导航数据
     * @param int $wid 店铺id
     * @return array
     * @author 何书哲 2018年8月30日
     */
    public function getStoreTopNavData($wid = 0)
    {
        $data['topNav'] = '';
        $storeTopNavService = new StoreTopNavService();
        $storeTopNavData = $storeTopNavService->getRow($wid);
        if ($storeTopNavData['errCode'] != 0 || empty($storeTopNavData['data'])) {
            return $data;
        }
        $data['color_setting'] = $storeTopNavData['data']['color_setting'];
        if (!$storeTopNavData['data']['is_on'] || empty($storeTopNavData['data']['template_data'])) {
            return $data;
        }
        $headerData = json_decode($storeTopNavData['data']['template_data'], true);
        $pageIds = array_column($headerData, 'pageId');
        $pageData = StoreMicroPageService::getListById($pageIds);
        foreach ($pageData as $item) {
            $pageData[$item['id']] = $item;
        }
        foreach ($headerData as &$item) {
            //效验数据 删除的数据不显示
            if (!isset($pageData[$item['pageId']])) {
                unset($item);
            }
        }
        $headerData && ($data['topNav'] = json_encode($headerData));
        return $data;
    }

    /**
     * 获取店铺公共广告
     * @param int $wid 店铺id
     * @param int $apply_location 出现的位置
     * @param bool $filter 是否过滤
     * @return array
     * @author 何书哲 2018年8月31日
     */
    public function getMicroPageNoticeData($wid = 0, $apply_location = 0, $filter = true)
    {
        $data = [];
        //公共广告
        $noticeResult = StoreMicroPageNoticeService::getRow($wid);
        //判断是否查询到公共广告信息
        if ($noticeResult['errCode'] != 0 || empty($noticeResult['data'])) {
            $data['header'] = '';
            return $data;
        }
        if ($noticeResult['data']['is_used'] != 1 || strpos($noticeResult['data']['apply_location'], strval($apply_location)) == false) {
            $data['header'] = '';
            return $data;
        }
        $noticeTemplateData = $noticeResult['data']['notice_template_info'];
        $noticeTemplateData && $filter && ($noticeTemplateData = $this->processTemplateData($wid, $noticeTemplateData));
        $noticeResult['data']['position'] == 1 ? ($data['header'] = $noticeTemplateData ?? '') : ($data['container'] = $noticeTemplateData ?? '');
        return $data;
    }

    /**
     * 获取店铺信息
     * @param int $wid 店铺id
     * @param bool $filter 是否过滤
     * @return array
     * @author 何书哲 2018年8月31日
     */
    public function getMicroPageData($wid = 0, $filter = true)
    {
        $data['title'] = $data['data'] = '';
        $storeResult = StoreMicroPageService::getRowByCondition(['wid' => $wid, 'is_home' => 1]);
        //判断是否查询到店铺信息
        if ($storeResult['errCode'] != 0 || empty($storeResult['data'])) {
            return $data;
        }
        $data['title'] = $storeResult['data']['page_title'];
        $data['bgcolor'] = $storeResult['data']['page_bgcolor'];
        $data['description'] = $storeResult['data']['page_description'];
        $data['pageid'] = $storeResult['data']['id']; //2017年08月22日 陈文豪
        $data['isWebsite'] = 0;
        $data['qq'] = '';
        $data['weixin'] = '';
        $data['telphone'] = '';

        $storeResult['data']['qq']
        && ($res = (new KefuService())->getRowByCondition(['qq' => $storeResult['data']['qq']]))
        && ($data['qq'] = $res['qq'])
        && ($data['weixin'] = $res['weixin'])
        && ($data['telphone'] = $res['telphone']);

        $storeResult['data']['template_id']
        && ($microPageTemplateData = (new TemplateMarketService())->getRowById($storeResult['data']['template_id']))
        && ($microPageTemplateData['errCode'] == 0)
        && $microPageTemplateData['data']
        && ($data['isWebsite'] = $microPageTemplateData['data']['is_website']);

        $storeTemplateData = $storeResult['data']['page_template_info'];
        $storeTemplateData && $filter
        && ($storeTemplateData = $this->processTemplateData($wid, $storeTemplateData))
        && ($data['data'] = $storeTemplateData ?? '');

        return $data;
    }

    /**
     * 获取店铺会员主页
     * @param int $wid 店铺id
     * @param bool $filter 是否过滤
     * @return array
     * @author 何书哲 2018年8月31日
     */
    public function getStoreMemberHomeData($wid = 0, $filter = true, $mid)
    {
        $data['title'] = $data['data'] = '';
        //会员主页
        $homeResult = StoreMemberHomeService::getRow($wid);
        if ($homeResult['errCode'] != 0 || empty($homeResult['data'])) {
            return $data;
        }
        $data['title'] = $homeResult['data']['home_name'];
        $module_ids = $homeResult['data']['module_ids'] ? explode(',', $homeResult['data']['module_ids']) : [2, 4, 5, 6, 8, 9];
        $distributeModule = new DistributeModule();
        if (!$distributeModule->distributePermission($wid, $mid)) {
            $key = array_search('4', $module_ids);
            if ($key) {
                unset($module_ids[$key]);
            }
        }
        $data['homeModule'] = json_encode((new MemberHomeModuleService())->getListByIds($module_ids, $wid));
        $homeTemplateData = $homeResult['data']['custom_info'];
        $homeTemplateData && $filter && ($homeTemplateData = $this->processTemplateData($wid, $homeTemplateData));
        $data['data'] = $homeTemplateData ?? '';
        return $data;
    }

    /**
     * 获取店铺底部导航
     * @param int $wid 店铺id
     * @param int $apply_location 出现的位置
     * @return array
     * @author 何书哲 2018年8月31日
     */
    public function getStoreNavData($wid = 0, $apply_location = 0)
    {
        $data['footer'] = '';
        $navResult = StoreStoreNavService::getRow($wid);
        //判断是否查询到店铺导航数据
        if ($navResult['errCode'] != 0 || empty($navResult['data'])) {
            return $data;
        }
        if ($navResult['data']['is_used'] != 1 || strpos($navResult['data']['apply_page'], strval($apply_location)) == false) {
            return $data;
        }
        $data['footer'] = $navResult['data']['nav_template_info'] ?? '';
        return $data;
    }

    /**
     * 获取店铺信息
     * @param int $wid 店铺id
     * @return mixed
     * @author 何书哲 2018年8月31日
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function getStoreData($wid = 0)
    {
        //店铺信息
        $storeInfo = '';
        $shopService = new ShopService();
        /*$storeData = StoreWeixinService::getStoreInfo($wid);
        $storeData['data'] && ($storeInfo = json_encode($storeData['data']));*/
        $storeInfo = $shopService->getRowById($wid);
        $storeInfo && ($storeInfo = json_encode($storeInfo));
        $data['info'] = $storeInfo;
        return $data;
    }

    private function _checkProcessMicroPageData($data)
    {
        $returnData['data'] = ['title' => ''];

        if (empty($data)) {
            $returnData['errCode'] = -1;
            $returnData['errMsg'] = '数据为空';
            return $returnData;
        }
        if (!isset($data['id']) || empty($data['id'])) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = 'id为空';
            return $returnData;
        }

        if (!isset($data['wid']) || empty($data['wid'])) {
            $returnData['errCode'] = -2;
            $returnData['errMsg'] = 'wid为空';
            return $returnData;
        }
        return true;
    }

    /**
     * 移动端微页面
     * $data 为存放数据数组 $filter为false 表示不对原数据进行处理 为true表示对原数据进行处理
     * @return array
     * @author jonzhang guo.jun.zhang@163.com
     * @desc  header,container,footer 3个模块
     * @date 2017-03-22
     * @update 2018-07-27 陈文豪 规范代码 理顺流程
     */
    public function processMicroPageData($data = [], $filter = true)
    {
        $checkData = $this->_checkProcessMicroPageData($data);
        if ($checkData !== true) {
            return $checkData;
        }
        $id = $data['id'];
        $wid = $data['wid'];

        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => ['title' => '']];
        $returnData['data']['header'] = '';
        $returnData['data']['container'] = '';
        $returnData['data']['footer'] = '';

        //①①①①①① 第一部分公共广告
        $noticeResult = StoreMicroPageNoticeService::getRow($wid);

        if ($noticeResult['errCode'] == 0 && !empty($noticeResult['data'])) {
            $noticeData = $noticeResult['data'];
            if ($noticeData['is_used'] == 1 &&
                strpos($noticeData['apply_location'], '1') !== false) {

                $noticeTemplateData = $noticeData['notice_template_info'];
                if (!empty($noticeTemplateData) && $filter) {
                    $noticeTemplateData = $this->processTemplateData($wid, $noticeTemplateData);
                }
                if ($noticeData['position'] == 1) {
                    $returnData['data']['header'] = $noticeTemplateData ?? '';
                } else {
                    $returnData['data']['container'] = $noticeTemplateData ?? '';
                }
            }
        }

        //②②②②②② 微页面块
        $microPageResult = StoreMicroPageService::getRowById($id);
        if ($microPageResult['errCode'] == 0 && !empty($microPageResult['data'])) {
            $microPageData = $microPageResult['data'];
            $returnData['data']['title'] = $microPageData['page_title'];
            $returnData['data']['bgcolor'] = $microPageData['page_bgcolor'];
            $returnData['data']['description'] = $microPageData['page_description'];
            $returnData['data']['isWebsite'] = 0;

            /*---------------------------------------*/
            /*add by wuxiaoping 2017.09.28 把客服QQ添加返回数据*/
            $returnData['data']['qq'] = '';
            $returnData['data']['weixin'] = '';
            $returnData['data']['telphone'] = '';
            if ($microPageData['qq']) {
                $res = (new KefuService())->getRowByCondition(['qq' => $microPageData['qq']]);
                if ($res) {
                    $returnData['data']['qq'] = $res['qq'];
                    $returnData['data']['weixin'] = $res['weixin'];
                    $returnData['data']['telphone'] = $res['telphone'];
                }
            }

            /*add by wuxiaoping 2017.09.01  分享内容*/
            $returnData['data']['share_title'] = $microPageData['share_title'];
            $returnData['data']['share_desc'] = $microPageData['share_desc'];
            $returnData['data']['share_img'] = imgUrl() . $microPageData['share_img'];

            if (empty($microPageData['share_title'])) {
                if ($microPageData['share_title'] != '0') {//排序标题为0的情况
                    $shareData = (new PublicShareService())->publicShareSet($wid);
                    $returnData['data']['share_title'] = $shareData['share_title'];
                    $returnData['data']['share_desc'] = $shareData['share_desc'];
                    $returnData['data']['share_img'] = $shareData['share_img'];
                }
            }
            /*---------------------------------------*/

            //模板ID
            if (!empty($microPageData['template_id'])) {
                $templateMarketService = new TemplateMarketService();
                $microPageTemplateData = $templateMarketService->getRowById($microPageData['template_id']);
                if ($microPageTemplateData['errCode'] == 0 && !empty($microPageTemplateData['data'])) {
                    $returnData['data']['isWebsite'] = $microPageTemplateData['data']['is_website'];
                }
            }

            //主体区块
            $pageTemplateData = $microPageData['page_template_info'];
            if (!empty($pageTemplateData) && $filter) {
                $pageTemplateData = $this->processTemplateData($wid, $pageTemplateData);
                //同步视频  todo优化
                $videosData = json_decode($pageTemplateData, 1);
                if (is_array($videosData)) {
                    foreach ($videosData as $k => $v) {
                        if ($v['type'] == 'video') {
                            $obj = UserFile::find($v['id']);
                            if ($obj) {
                                $videosData[$k]['videoItem'] = $obj->load('FileInfo')->toArray();
                            }
                        }
                    }
                }
                $tempData = json_encode($videosData);
                $pageTemplateData = $tempData;
            }
            if (!empty($returnData['data']['container'])) {
                $returnData['data']['header'] = $pageTemplateData ?? '';
            } else {
                $returnData['data']['container'] = $pageTemplateData ?? '';
            }
        }


        //③③③③③③ 导航信息
        $navResult = StoreStoreNavService::getRow($wid);
        //判断该店铺下的导航信息是否存在
        $returnData['data']['footer'] = '';
        if ($navResult['errCode'] == 0 && !empty($navResult['data'])) {
            $navData = $navResult['data'];
            if ($navData['is_used'] == 1 && strpos($navData['apply_page'], '3') !== false) {
                $returnData['data']['footer'] = $navData['nav_template_info'] ?? '';
            }
        }

        //add by zhangyh
        $returnData['data']['container'] = ProductModule::addProductContentHost($returnData['data']['container']);
        $returnData['data']['footer'] = ProductModule::addProductContentHost($returnData['data']['footer']);
        $returnData['data']['header'] = ProductModule::addProductContentHost($returnData['data']['header']);
        //end

        return $returnData;
    }

    /**
     * todo 处理模板中的模块信息
     * @param $wid为店铺id ,$templateData为微页面中模块原始数据,$
     * @author jonzhang guo.jun.zhang@163.com
     * @date 2017-04-27
     * @update 许立 2018年08月27日 微页面公告前增加公告二字
     * @update 陈文豪 2018年08月28日 优化代码
     * @update 许立 2018年08月31日 增加秒杀列表组件
     */
    public function processTemplateData($wid, $templateData, $isPC = 0, $isXCX = 0, $isLoopSpellType = 1)
    {
        $handleTemplateData = [];

        $templateArrayData = json_decode($templateData, true);
        if (empty($templateArrayData) || !is_array($templateArrayData)) {
            $error = json_last_error();
            $handleTemplateData = '[{"showRight":true,"cardRight":6,"type":"title","editing":"editing","titleName":"系统暂时无法访问，请联系客服!","titleStyle":1,"subTitle":"","showPosition":2,"bgColor":"#ffffff","addLink":false,"chooseLink":false,"dropDown":false,"linkName":"","linkUrl":"","date":"","author":"","wlinkTitle":"","wlinkUrlChoose":1,"wlinkUrl":"","is_add_content":false}]';
            BLogger::getLogger('error')->error('店铺id为:' . $wid . ' 模板数据为:' . $templateData . ' 数据转化出现问题，异常信息为：' . $error);
            return $handleTemplateData;
        }

        foreach ($templateArrayData as $item) {
            switch ($item['type']) {
                case 'goods':
                    // 类型为goods的数据进行过滤
                    $item = MicPage::to($wid, $isXCX)->productData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'coupon':
                    // 类型为coupon的数据进行过滤
                    $item = MicPage::to($wid, $isXCX)->couponData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'image_ad':
                    // 图片广告数据进行过滤筛选
                    $item = MicPage::to($wid, $isXCX)->imageAdData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'store':
                    // 店铺
                    $item = MicPage::to($wid, $isXCX)->storeData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'model':
                    // 自定义模板
                    $item = MicPage::to($wid, $isXCX, $isPC)->modelData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'goodslist':
                    // 商品列表
                    $item = MicPage::to($wid, $isXCX, $isPC)->goodsListData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'good_group':
                    // 商品分组
                case 'group_template':
                    $item = MicPage::to($wid, $isXCX, $isPC)->goodGroupData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'marketing_active':
                    // 秒杀模板
                    $item = MicPage::to($wid, $isXCX)->marketingActiveData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'seckill_list':
                    // 秒杀列表
                    $item = MicPage::to($wid, $isXCX)->seckillListData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'goods_group':
                    // Herry 商品分组详情页处理
                    $item = MicPage::to($wid, $isXCX)->goodsGroupData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'card':
                    // 会员卡查询
                    $item = MicPage::to($wid, $isXCX)->cardData($item);
                    ($item !== false) && $handleTemplateData[] = $item;
                    break;
                case 'spell_goods':
                    // 拼团商品
                    if (empty($item['groups_id']) || !is_array($item['groups_id'])) {
                        $handleTemplateData[] = $item;
                        break;
                    }
                    $handleTemplateData[] = $this->handleSpellGoods($item, $isPC, $isXCX);
                    break;
                case 'video':
                    // 视频 add by meijie
                    if (empty($item['id'])) {
                        $handleTemplateData[] = $item;
                        break;
                    }
                    $handleTemplateData[] = $this->handleVideo($item);
                    break;
                case 'share_goods':
                    if (empty($item['activity_id']) || !is_array($item['activity_id'])) {
                        $handleTemplateData[] = $item;
                        break;
                    }
                    $handleTemplateData[] = $this->handleShareGoods($item);
                    break;
                case 'li_goods':
                    if (empty($item['activity_id']) || !is_array($item['activity_id'])) {
                        $handleTemplateData[] = $item;
                        break;
                    }
                    $handleTemplateData[] = $this->handleLiShareGoods($item);
                    break;
                case 'notice':
                    $item['content'] = '公告：' . $item['content'];
                    $handleTemplateData[] = $item;
                    break;
                case 'live':
                    // 直播 add 何书哲 2020年03月13日
                    if (!empty($item['room'])) {
                        $handleTemplateData[] = $item;
                    }
                    break;
                default:
                    $handleTemplateData[] = $item;
                    break;
            }
        }

        if (!empty($handleTemplateData)) {
            if (is_array($handleTemplateData)) {
                $handleTemplateData = json_encode($handleTemplateData);
            }
        } else {
            $handleTemplateData = '';
        }
        return $handleTemplateData;
    }

    /**
     * todo 处理模板数据中的拼团模块
     * @param array $item
     * @param int $isXCX
     * @return array
     * @author jonzhang
     * @date 2017-10-19
     */
    private function handleSpellTitle($item = [], $isPC = 0, $isXCX = 0)
    {
        try {
            $item['pages'] = [];
            $item['default']['data'] = [];
            if (empty($item['pages_id']) || empty($item['default']['id'])) {
                return $item;
            }
            //小程序微页面
            if ($isXCX) {
                $where = ['current_status' => 0, 'ids' => $item['pages_id']];
                $list = WXXCXMicroPageService::getListByConditionWithPage($where);
                if (!empty($list[0]['data'])) {
                    $xcxMicroPages = $list[0]['data'];
                    foreach ($xcxMicroPages as $microPage) {
                        if ($item['default']['id'] == $microPage['id'] && !empty($microPage['template_info']) && $microPage['template_info'] != '[]' && !$isPC) {
                            $item['default']['data'] = $this->processTemplateData($microPage['id'], $microPage['template_info'], $isPC, $isXCX, 0);
                        }
                        $item['pages'][] = ['id' => $microPage['id'], 'name' => $microPage['title']];
                    }
                    if (!empty($item['pages'])) {
                        array_reverse($item['pages']);
                    }
                } else {
                    $item['error'] = $list[0]['data'];
                }
            }//微页面
            else {
                $list = StoreMicroPageService::getListByConditionWithPage(['id' => $item['pages_id']]);
                if (!empty($list[0]['data'])) {
                    $microPages = $list[0]['data'];
                    foreach ($microPages as $microPage) {
                        if ($item['default']['id'] == $microPage['id'] && !empty($microPage['page_template_info']) && $microPage['page_template_info'] != '[]' && !$isPC) {
                            //$item['default']['data']=$microPage['page_template_info'];
                            $item['default']['data'] = $this->processTemplateData($microPage['id'], $microPage['page_template_info'], $isPC, $isXCX, 0);
                        }
                        $item['pages'][] = ['id' => $microPage['id'], 'name' => $microPage['page_title']];
                    }
                    if (!empty($item['pages'])) {
                        array_reverse($item['pages']);
                    }
                } else {
                    $item['error'] = $list[0]['data'];
                }
            }
        } catch (\Exception $ex) {
            $item['error'] = $ex->getMessage();
        }
        return $item;
    }

    /**
     * todo 处理模板数据中的拼团商品模块
     * @param array $item
     * @return array
     * @author jonzhang
     * @date 2017-10-20
     * @update 张永辉 2018年7月25日 微页面调用团购接口重写
     * @update 何书哲 2018年11月09日 返回添加开始结束时间字段
     */
    private function handleSpellGoods($item = [], $isPC = 0, $isXCX = 0)
    {
        try {
            $item['groups'] = [];
            $groupsRuleModule = new GroupsRuleModule();
            foreach ($item['groups_id'] as $group) {
                $groupData = $groupsRuleModule->getFirstGroups($group);
                if ($groupData['errCode'] == 0 && !empty($groupData['data'])) {
                    $groupInfo = [];
                    //id
                    $groupInfo['id'] = $groupData['data']['id'] ?? 0;
                    //标签
                    $groupInfo['label'] = $groupData['data']['label'] ?? '';
                    //标题
                    $groupInfo['title'] = $groupData['data']['title'] ?? '';
                    //副标题
                    $groupInfo['subtitle'] = $groupData['data']['subtitle'] ?? '';
                    //售价
                    $groupInfo['price'] = $groupData['data']['min'] ?? 0;
                    //正方形图片
                    $groupInfo['square_image'] = $groupData['data']['img2'] ?? '';
                    //长方形图片
                    $groupInfo['rectangle_image'] = $groupData['data']['img'] ?? '';
                    //目标人数
                    $groupInfo['target_num'] = $groupData['data']['groups_num'] ?? 0;
                    //已参团商品数
                    $groupInfo['attend_num'] = $groupData['data']['pnum'] ?? 0;
                    $groupInfo['attend_num_2'] = $groupData['data']['pnum'] ?? 0;
                    //已参团人数
                    $groupInfo['cnt'] = $groupData['data']['mnum'] ?? 0;
                    //参团人信息
                    if (!$isPC) {
                        $groupInfo['member'] = $groupData['data']['member'] ?? [];
                    }
                    //开始时间
                    $groupInfo['start_time'] = $groupData['data']['start_time'] ?? '';
                    //结束时间
                    $groupInfo['end_time'] = $groupData['data']['end_time'] ?? '';
                    $groupInfo['now_time'] = date('Y-m-d H:i:s');
                    $item['groups'][] = $groupInfo;
                } else {
                    $item['error'][] = ['groupID' => $group, 'groupInfo' => $groupData];
                }
            }
        } catch (\Exception $ex) {
            $item['error'] = $ex->getMessage();
        }
        return $item;
    }

    /***
     * todo 视频
     * @param array $item
     * @add by meijie
     * @modify by jonzhang
     * @date 2017-12-13
     */
    private function handleVideo($item = [])
    {
        try {
            $obj = UserFile::find($item['id']);
            if ($obj) {
                $item['videoItem'] = $obj->load('FileInfo')->toArray();
            }
        } catch (\Exception $ex) {
            $item['error'] = $ex->getMessage();
        }
        return $item;
    }

    /***
     * todo 享立减商品
     * @param array $item
     * @return array
     * @author jonzhang
     * @date 2017-12-13
     * @author update by 吴晓平 2018年08月22日  取消分销商品不能够享立减限制
     */
    private function handleShareGoods($item = [])
    {
        try {
            $shareEventService = new ShareEventService();
            foreach ($item['activity_id'] as $value) {
                //status为1表示已删除 type为1表示失效
                $obj = $shareEventService->getRow(['id' => $value, 'status' => 0, 'type' => 0]);
                if ($obj['errCode'] == 0 && !empty($obj['data']['product_id'])) {
                    $shareEventData = $obj['data'];
                    //过期享立减活动不显示
                    //add by jonzhang 2018-01-22
                    if ($shareEventData['end_time'] < time()) {
                        $item['error'][] = ['activityId' => $value, 'code' => $obj['errCode'], 'msg' => '享立减活动过期', 'endTime' => $shareEventData['end_time']];
                        continue;
                    }
                    $productData = StoreProductService::getDetail($obj['data']['product_id']);
                    $errMsg = "";
                    if (isset($productData['status'])) {
                        //status为1表示上架
                        if ($productData['status'] != 1) {
                            $errMsg .= "该商品已下架";
                        }
                    }
                    //update by 吴晓平 2018年08月22日  取消分销商品不能够享立减限制
                    /*if(isset($productData['is_distribution']))
                    {
                        //分销商品不能够享立减
                        if($productData['is_distribution']==1)
                        {
                            $errMsg.="分销商品不能够享立减";
                        }
                    }*/
                    if (strlen($errMsg) > 0) {
                        $item['error'][] = ['activityId' => $value, 'productId' => $obj['data']['product_id'], 'msg' => $errMsg];
                        continue;
                    }
                    //保底价分转化为元
                    if (isset($shareEventData['lower_price']) && $shareEventData['lower_price'] > 0) {
                        $shareEventData['lower_price'] = sprintf('%.2f', $shareEventData['lower_price'] / 100);
                    }
                    //逐减人数
                    $total = 0;
                    $total = $total + $shareEventData['reduce_total'];
                    //开启初始值
                    if ($shareEventData['is_initial']) {
                        $total = $total + $shareEventData['initial_value'];
                    }
                    $item['activitys'][] = [
                        "id" => $obj['data']['id'],
                        "product_id" => $productData['id'],
                        "name" => $productData['title'],
                        "thumbnail" => $productData['img'],
                        "price" => $productData['price'],
                        //add by jonzhang 2018-01-10
                        "title" => $shareEventData['title'] ?? '',
                        "subtitle" => $shareEventData['subtitle'] ?? '',
                        "activityImg" => $shareEventData['act_img'] ?? '',
                        "lowerPrice" => $shareEventData['lower_price'] ?? 0,
                        "attendCount" => $total,
                        "buttonTitle" => $shareEventData['button_title'] ?? '',
                        //add by jonzhang 2018-01-22
                        "startTime" => $shareEventData['start_time'] ?? '',
                        "endTime" => $shareEventData['end_time'] ?? '',
                        "currentTime" => time()
                    ];
                } else {
                    $item['error'][] = ['activityId' => $value, 'code' => $obj['errCode'], 'msg' => $obj['errMsg']];
                }
            }
        } catch (\Exception $ex) {
            $item['error'][] = $ex->getMessage();
        }
        return $item;
    }

    /**
     * todo 魔方
     * @param array $item
     * @author jonzhang
     * @date 2017-01-05
     */
    private function handleMagic($item = [])
    {
        try {
            foreach ($item['content'] as &$itemContent) {
                //魔方商品
                if ($itemContent['type'] == 1 && $itemContent['id'] > 0) {
                    $productData = StoreProductService::getDetail($itemContent['id']);
                    //status为1表示上架
                    if (isset($productData['status']) && $productData['status'] == 1) {
                        $itemContent['linkTitle'] = $productData['title'];
                    } else {
                        $itemContent['id'] = 0;
                        $itemContent['linkTitle'] = '';
                        $data = '';
                        if (!empty($productData)) {
                            $data = json_encode($productData);
                        }
                        $item['error'][] = ['productId' => $itemContent['id'], 'data' => $data, 'msg' => '商品下架'];
                    }
                }//魔方微页面
                else if ($itemContent['type'] == 2 && $itemContent['id'] > 0) {
                    $xcxPageData = WXXCXMicroPageService::getRowById($itemContent['id']);
                    if ($xcxPageData['errCode'] == 0 && !empty($xcxPageData['data'])) {
                        $itemContent['linkTitle'] = $xcxPageData['data']['title'];
                    } else {
                        if ($xcxPageData['errCode'] == 0) {
                            $msg = "数据不存在";
                        } else {
                            $msg = $xcxPageData['errMsg'];
                        }
                        $itemContent['id'] = 0;
                        $itemContent['linkTitle'] = '';
                        $item['error'][] = ['pageId' => $itemContent['id'], 'data' => json_encode($xcxPageData), 'msg' => $msg];
                    }
                }
            }
        } catch (\Exception $ex) {
            $item['error'][] = $ex->getMessage();
        }
        return $item;
    }

    /***
     * todo 虚拟享立减for高总
     * @param array $item
     * @return array
     * @author jonzhang
     * @date 2018-01-24
     */
    private function handleLiShareGoods($item = [])
    {
        try {
            $item['activitys'] = [];
            $shareEventService = new LiEventService();
            foreach ($item['activity_id'] as $value) {
                //status为1表示已删除 type为1表示失效
                $obj = $shareEventService->getRow(['id' => $value, 'status' => 0, 'type' => 0]);
                if ($obj['errCode'] == 0 && !empty($obj['data']['product_id'])) {
                    $shareEventData = $obj['data'];
                    //add by jonzhang 2018-01-29
                    if ($shareEventData['end_time'] < time()) {
                        $item['error'][] = ['activityId' => $value, 'code' => $obj['errCode'], 'msg' => '享立减活动过期', 'endTime' => $shareEventData['end_time']];
                        continue;
                    }
                    $productData = StoreProductService::getDetail($obj['data']['product_id']);
                    $errMsg = "";
                    if (isset($productData['status'])) {
                        //status为1表示上架
                        if ($productData['status'] != 1) {
                            $errMsg .= "该商品已下架";
                        }
                    }
                    if (isset($productData['is_distribution'])) {
                        //分销商品不能够享立减
                        if ($productData['is_distribution'] == 1) {
                            $errMsg .= "分销商品不能够享立减";
                        }
                    }
                    if (strlen($errMsg) > 0) {
                        $item['error'][] = ['activityId' => $value, 'productId' => $obj['data']['product_id'], 'msg' => $errMsg];
                        continue;
                    }
                    //保底价分转化为元
                    if (isset($shareEventData['lower_price']) && $shareEventData['lower_price'] > 0) {
                        $shareEventData['lower_price'] = sprintf('%.2f', $shareEventData['lower_price'] / 100);
                    }
                    //逐减人数
                    $total = 0;
                    $total = $total + $shareEventData['reduce_total'];
                    //开启初始值
                    if ($shareEventData['is_initial']) {
                        $total = $total + $shareEventData['initial_value'];
                    }
                    $item['activitys'][] = [
                        "id" => $obj['data']['id'],
                        "product_id" => $productData['id'],
                        "name" => $productData['title'],
                        "thumbnail" => $productData['img'],
                        "price" => $productData['price'],
                        //add by jonzhang 2018-01-10
                        "title" => $shareEventData['title'] ?? '',
                        "subtitle" => $shareEventData['subtitle'] ?? '',
                        "activityImg" => $shareEventData['act_img'] ?? '',
                        "lowerPrice" => $shareEventData['lower_price'] ?? 0,
                        "attendCount" => $total,
                        "buttonTitle" => $shareEventData['button_title'] ?? '',
                        //add by jonzhang 2018-01-29
                        "startTime" => $shareEventData['start_time'] ?? '',
                        "endTime" => $shareEventData['end_time'] ?? '',
                        "currentTime" => time()
                    ];
                } else {
                    $item['error'][] = ['activityId' => $value, 'code' => $obj['errCode'], 'msg' => $obj['errMsg']];
                }
            }
        } catch (\Exception $ex) {
            $item['error'][] = $ex->getMessage();
        }
        return $item;
    }

    /**
     * 店铺数据互导(商品、商品分组、运费模板和分销模板)
     * @param $wid_from int 源店铺ID
     * @param $wid_to int 目标店铺ID
     * @author Herry
     * @since 2018/01/23
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     */
    public function importAndExport($wid_from, $wid_to)
    {
        //返回格式
        $resultArr = [
            'errCode' => 1,
            'errMsg' => '',
            'data' => []
        ];

        //源店铺信息
        $shopService = new ShopService();
        //$shop_from = StoreWeixinService::init()->model->wheres(['id' => $wid_from])->get(['id'])->toArray();
        $shop_from = $shopService->getRowById($wid_from);
        if (empty($shop_from)) {
            $resultArr['errMsg'] = '源店铺不存在';
            return $resultArr;
        }
        //$shop_from = $shop_from[0];

        //目标店铺信息
        //$shop_to = StoreWeixinService::init()->model->wheres(['id' => $wid_to])->get(['id'])->toArray();
        $shop_to = $shopService->getRowById($wid_to);
        if (empty($shop_to)) {
            $resultArr['errMsg'] = '目标店铺不存在';
            return $resultArr;
        }
        //$shop_to = $shop_to[0];

        //获取源店铺所有商品
        $product_service = new ProductService();
        $products_from = $product_service->getAllByWID($shop_from['id']);

        //遍历源店铺所有商品
        $import_service = new WeixinImportService();
        foreach ($products_from as $product_from) {
            //先检查该源商品是否已经导入过到目标店铺 导入到什么进度(可能正在导入中 再次点击了导入按钮)
            $row = $import_service->getRow($product_from['id'], $shop_to['id']);
            if (empty($row)) {
                //啥都没导入
                //1.导入商品
                $product_id_to = $this->_importProduct($shop_to['id'], $product_from);

                //2.生成导入记录表
                $import_id = $import_service->createImportRecord($shop_from['id'], $shop_to['id'], $product_from['id'], $product_id_to);
                if (empty($import_id)) {
                    $resultArr['errMsg'] = '生成导入记录出错';
                    return $resultArr;
                }

                //3.导入运费模板
                $this->_importFreight($import_id, $shop_to['id'], $product_id_to, $product_from);

                //4.导入商品分组
                $this->_importGroup($import_id, $shop_to['id'], $product_id_to, $product_from);

                //5.导入分销模板
                $this->_importDistribute($import_id, $shop_to['id'], $product_id_to, $product_from);

                $row['id'] = $import_id;
            } elseif ($row['schedule_status'] == WeixinImportService::SCHEDULE_STATUS_PRODUCT) {
                //仅商品导入完成
                //3.导入运费模板
                $this->_importFreight($row['id'], $shop_to['id'], $row['pid_to'], $product_from);

                //4.导入商品分组
                $this->_importGroup($row['id'], $shop_to['id'], $row['pid_to'], $product_from);

                //5.导入分销模板
                $this->_importDistribute($row['id'], $shop_to['id'], $row['pid_to'], $product_from);
            } elseif ($row['schedule_status'] == WeixinImportService::SCHEDULE_STATUS_FREIGHT) {
                //商品和运费模板导入完成
                //4.导入商品分组
                $this->_importGroup($row['id'], $shop_to['id'], $row['pid_to'], $product_from);

                //5.导入分销模板
                $this->_importDistribute($row['id'], $shop_to['id'], $row['pid_to'], $product_from);
            } elseif ($row['schedule_status'] == WeixinImportService::SCHEDULE_STATUS_GROUP) {
                //商品和运费模板和分组导入完成
                //5.导入分销模板
                $this->_importDistribute($row['id'], $shop_to['id'], $row['pid_to'], $product_from);
            }

            //设置导入状态为成功
            $import_service->update($row['id'], ['schedule_status' => WeixinImportService::SCHEDULE_STATUS_COMPLETE]);
        }

        $resultArr['errCode'] = 0;
        $resultArr['errMsg'] = '店铺数据导入成功';
        return $resultArr;
    }

    /**
     * 导入商品信息
     * @param $wid_to int 目标店铺ID
     * @param $product_from array 源商品信息
     * @return int 目标商品ID
     */
    private function _importProduct($wid_to, $product_from)
    {
        $product_service = new ProductService();
        $product_id_from = $product_from['id'];
        unset($product_from['id']);
        $product_from['wid'] = $wid_to;
        empty($product_from['deleted_at']) && $product_from['deleted_at'] = null;
        $product_id_to = $product_service->model->insertGetId($product_from);

        //新增规格
        (new ProductPropsToValuesService())->copySkuByProductID($product_id_from, $product_id_to);

        //新增图片
        $imgService = new ProductImgService();
        $imgs = $imgService->getListByProduct($product_id_from);
        if (count($imgs)) {
            $product_service->batchAddImg($imgs, $product_id_to, $wid_to);
        }

        //新增留言
        $msgService = new ProductMsgService();
        $msgs = $msgService->getListByProduct($product_id_from);
        if (count($msgs)) {
            $product_service->batchAddMsg($msgs, $product_id_to);
        }

        return $product_id_to ? $product_id_to : 0;
    }

    /**
     * 导入运费模板
     * @param $import_id int 导入记录ID
     * @param $wid_to int 目标店铺ID
     * @param $product_id_to int 目标商品ID
     * @param $product_from array 源商品信息
     * @return bool
     */
    public function _importFreight($import_id, $wid_to, $product_id_to, $product_from)
    {
        //无运费模板
        if ($product_from['freight_type'] == 1) {
            return true;
        }

        //有运费模板
        $product_service = new ProductService();
        $freight_service = new FreightService();

        //先获取源商品的运费模板信息
        $freight_from = $freight_service->getOne($product_from['freight_id']);
        $product_to = $product_service->getDetail($product_id_to);
        if (empty($freight_from)) {
            //当做无运费模板处理
            $product_service->update($product_id_to, ['freight_type' => 1, 'freight_id' => 0]);
        } else {
            //判断是否已经导入过该运费模板
            $where = [
                'wid_from' => $product_from['wid'],
                'wid_to' => $wid_to,
                'freight_id_from' => $product_from['freight_id']
            ];
            $import_service = new WeixinImportService();
            $row = $import_service->getOneByWhere($where);
            if ($row) {
                //已导入该运费模板
                $freight_id_to = $row['freight_id_to'];
            } else {
                //复制一条源商品运费模板到目标店铺
                unset($freight_from['id']);
                $freight_from['wid'] = $wid_to;
                $freight_from['created_at'] = $freight_from['updated_at'] = date('Y-m-d H:i:s');
                empty($freight_from['deleted_at']) && $freight_from['deleted_at'] = null;
                $freight_id_to = $freight_service->addOne($freight_from);
            }

            //更新导入运费信息进度
            $updateData = [
                'schedule_status' => WeixinImportService::SCHEDULE_STATUS_FREIGHT,
                'freight_id_from' => $product_from['freight_id'],
                'freight_id_to' => $freight_id_to ?? 0
            ];
            $import_service->update($import_id, $updateData);

            //更新目标商品的运费模板
            $freight_id_to && $product_service->update($product_id_to, ['freight_type' => 2, 'freight_id' => $freight_id_to]);
        }

        return true;
    }

    /**
     * 导入商品分组
     * @param $import_id int 导入记录ID
     * @param $wid_to int 目标店铺ID
     * @param $product_id_to int 目标商品ID
     * @param $product_from array 源商品信息
     * @return bool
     */
    public function _importGroup($import_id, $wid_to, $product_id_to, $product_from)
    {
        //无商品分组
        if (empty($product_from['group_id'])) {
            return true;
        }

        //有商品分组
        $product_service = new ProductService();
        $group_service = new ProductGroupService();

        //先获取源商品的商品分组信息
        $group_ids_from = explode(',', $product_from['group_id']);
        $group_id_array_to = [];
        $import_service = new WeixinImportService();
        foreach ($group_ids_from as $group_id_from) {
            if (empty($group_id_from)) {
                continue;
            }
            $group_from = $group_service->getDetail($group_id_from);
            if (!empty($group_from) && $group_from['is_default'] == 0) {
                //判断是否已经导入过该商品分组
                $group_id_from = addslashes(strip_tags($group_id_from));
                $where = [
                    'wid_from' => $product_from['wid'],
                    'wid_to' => $wid_to,
                    '_string' => ' FIND_IN_SET(' . $group_id_from . ',group_id_from) '
                ];
                $row = $import_service->getOneByWhere($where);
                if ($row) {
                    //已导入该商品分组 获取对应的新分组ID
                    $map_array = explode(';', $row['group_id_map']);
                    foreach ($map_array as $map) {
                        $map_sub_array = explode(':', $map);
                        if ($map_sub_array[0] == $group_id_from) {
                            $group_id_array_to[$group_id_from] = $map_sub_array[1];
                            break;
                        }
                    }
                } else {
                    //复制一条源商品分组到目标店铺
                    $group_id_to = $group_service->copyGroup($wid_to, $group_from);
                    $group_id_to && $group_id_array_to[$group_id_from] = $group_id_to;
                }
            }
        }

        //拼接分组ID字符串 id1_from:id1_to;id2_from:id2_to
        $group_id_map = [];
        foreach ($group_id_array_to as $id_from => $id_to) {
            $group_id_map[] = $id_from . ':' . $id_to;
        }

        $updateData = [
            'schedule_status' => WeixinImportService::SCHEDULE_STATUS_GROUP,
            'group_id_from' => $product_from['group_id'],
            'group_id_map' => implode(';', $group_id_map)
        ];
        $import_service->update($import_id, $updateData);

        //更新目标商品的分组 先取详情会存redis 然后更新redis才不会出错
        $product_to = $product_service->getDetail($product_id_to);
        $product_service->update($product_id_to, ['group_id' => implode(',', array_values($group_id_array_to))]);

        return true;
    }

    /**
     * 导入分销模板
     * @param $import_id int 导入记录ID
     * @param $wid_to int 目标店铺ID
     * @param $product_id_to int 目标商品ID
     * @param $product_from array 源商品信息
     * @return bool
     */
    public function _importDistribute($import_id, $wid_to, $product_id_to, $product_from)
    {
        //不是分销商品
        if ($product_from['is_distribution'] == 0) {
            return true;
        }

        //是分销商品
        $product_service = new ProductService();
        $distribute_service = new DistributeTemplateService();

        //先获取源商品的分销模板信息
        $distribute_from = $distribute_service->getRowById($product_from['distribute_template_id']);
        $product_to = $product_service->getDetail($product_id_to);
        if (empty($distribute_from)) {
            //当做无分销模板处理
            $product_service->update($product_id_to, ['is_distribution' => 0, 'distribute_template_id' => 0]);
        } else {
            //判断是否已经导入过该分销模板
            $where = [
                'wid_from' => $product_from['wid'],
                'wid_to' => $wid_to,
                'distribute_id_from' => $product_from['distribute_template_id']
            ];
            $import_service = new WeixinImportService();
            $row = $import_service->getOneByWhere($where);
            if ($row) {
                //已导入该分销模板
                $distribute_id_to = $row['distribute_id_to'];
            } else {
                //复制一条源商品分销模板到目标店铺
                unset($distribute_from['id']);
                $distribute_from['wid'] = $wid_to;
                $distribute_from['created_at'] = $distribute_from['updated_at'] = date('Y-m-d H:i:s');
                empty($distribute_from['deleted_at']) && $distribute_from['deleted_at'] = null;
                $distribute_id_to = $distribute_service->add($distribute_from);
            }

            //更新导入分销模板信息进度
            $updateData = [
                'schedule_status' => WeixinImportService::SCHEDULE_STATUS_COMPLETE,
                'distribute_id_from' => $product_from['distribute_template_id'],
                'distribute_id_to' => $distribute_id_to ?? 0
            ];
            $import_service->update($import_id, $updateData);

            //更新目标商品的分销模板
            $distribute_id_to && $product_service->update($product_id_to, ['is_distribution' => 1, 'distribute_template_id' => $distribute_id_to]);
        }

        return true;
    }

    /**
     * 统计店铺销量等数据 脚本执行
     * 1 会员数
     * 2 销售额
     * @update 许立 2018年09月28日 保存过期时间
     * $shop_from = $shopService->getRowById($wid_from);
     * @update 吴晓平 2018年09月11日 把weixinService中的操作迁移到S/ShopService
     * @update 许立 2018年10月22日 把weixinService中的操作迁移到S/ShopService遗留bug修复
     * @update 许立 2019年01月19日 店铺过期时间处理
     * @update 许立 2019年01月28日 更新店铺角色表的过期时间
     */
    public function shopStatistics()
    {
        //查询所有店铺 统计到昨天为止的数据
        \DB::table('weixin')->select('id', 'uid', 'is_fee')
            ->whereNull('deleted_at')
            ->chunk(100, function ($shops) {
                //$shop_service = new WeixinService();
                $shop_service = new ShopService();
                $member_service = new MemberService();
                $refund_service = new OrderRefundService();
                $shopRoleService = new WeixinRoleService();
                foreach ($shops as $shop) {
                    try {
                        //统计的截止时间
                        $yesterday = date('Y-m-d', strtotime('-1 days')) . ' 23:59:59';
                        $where_member = [
                            'wid' => $shop->id,
                            'created_at' => ['<=', $yesterday]
                        ];

                        //1 统计会员数 2 统计销售额 除去退款商品金额
                        $member_sum = $member_service->model->wheres($where_member)->count();

                        //更新店铺字段
                        //$row = $shop_service->init('uid', $shop->uid)->getInfo($shop->id);
                        $row = $shop_service->getRowById($shop->id);
                        $update = [
                            'member_sum' => intval($member_sum),
                            'sale_sum' => $this->getSaleSum($refund_service, $shop->id),
                            'sale_sum_7days' => $this->getSaleSum($refund_service, $shop->id, 1),
                            'sale_sum_30days' => $this->getSaleSum($refund_service, $shop->id, 2)
                        ];

                        $expireTime = $shopRoleService->getShopExpireTime($shop->id);

                        // 刘三姐新需求：2019年1月1号-2月28号期间到期的店铺（标记为付费及赠送的）  到期时间统一延期到2019年3月10号
                        if (($shop->is_fee == 1 || $shop->is_fee == 2) && ('2019-01-01 00:00:00' <= $expireTime && $expireTime <= '2019-02-28 23:59:59')) {
                            $expireTime = '2019-03-10 23:59:59';
                            $shopRoleService->init()->model->where(['wid' => $shop->id])->update(['end_time' => $expireTime], false);
                        }

                        $expireTime && $update['shop_expire_at'] = $expireTime;
                        $shop_service->update($shop->id, $update);
                    } catch (\Exception $e) {
                        \Log::info($e->getMessage());
                        continue;
                    }
                }
            });

        \Log::info('统计店铺销量等数据脚本执行完成');
    }

    /**
     * 统计销售额
     * @param $refund_service OrderRefundService
     * @param $shop_id int 店铺ID
     * @param $type int 统计类型 0总销量 1最近7天销量 2最近30天销量 3自定义起始时间
     * @param $start string 自定义开始时间
     * @param $end string 自定义结束时间
     */
    public function getSaleSum($refund_service, $shop_id, $type = 0, $start = '', $end = '')
    {
        $yesterday = date('Y-m-d', strtotime('-1 days')) . ' 23:59:59';
        $where = ['wid' => $shop_id];
        switch ($type) {
            case 0:
                $where['created_at'] = ['<=', $yesterday];
                break;
            case 1:
                $where['created_at'] = ['between', [date('Y-m-d', strtotime('-7 days')) . ' 00:00:00', $yesterday]];
                break;
            case 2:
                $where['created_at'] = ['between', [date('Y-m-d', strtotime('-30 days')) . ' 00:00:00', $yesterday]];
                break;
            case 3:
                empty($start) && $start = date('Y-m-d H:i:s');
                empty($end) && $end = date('Y-m-d H:i:s');
                $where['created_at'] = ['between', [$start, $end]];
                break;
            default:
                break;
        }

        $where_order = $where;
        /*$where_order['_closure'] = function ($query) {
            $query->whereIn('status', [1,2,3])->orWhere(function ($query) {
                $query->where('status', 4)->whereNotIn('refund_status', [4,8]);
            });
        };*/
        $where_order['status'] = ['in', [1, 2, 3, 7]];
        //排除退款完成的
        $where_order['refund_status'] = ['not in', [4, 8]];
        $order_sum = OrderService::init('wid', $shop_id)->model->wheres($where_order)->sum('pay_price');
        //退款完成金额
        /*$where_refund = $where;
        $where_refund['status'] = ['in', [4, 8]];
        //退款打款成功时间 算支出时间
        //由于老数据和有时候退款回调不稳定 success_at不准确 目前暂时还是用created_at
        //$where_refund['success_at'] = $where_refund['created_at'];
        //unset($where_refund['created_at']);
        $refund_sum = $refund_service->init()->model->wheres($where_refund)->sum('amount');
        $sale_num = sprintf('%.2f', $order_sum - $refund_sum);*/
        //负数说明退款金额大于当天的订单收入
        //$sale_num >= 0 || $sale_num = - $sale_num;
        $sale_num = sprintf('%.2f', $order_sum);
        return $sale_num;
    }
}
