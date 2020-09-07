<?php
/**
 * @author 吴晓平 <2018.09.11>
 * 以前店铺WeixinService的数据重构，根据相关页面重写相关方法
 */

namespace App\S\Weixin;

use App\Jobs\CreateCaseXcxQrocde;
use App\Lib\Redis\ShopRedis;
use App\Lib\SendSmsNoticeHandler;
use App\Lib\UpdateBatch;
use App\Model\WeixinCase;
use App\Module\CommonModule;
use App\S\Foundation\RegionService;
use App\S\S;
use App\S\Wechat\WeChatShopConfService;
use App\Services\Permission\WeixinRoleService;
use App\Services\Permission\WeixinUserService;
use App\Services\UserService;
use App\Services\WeixinBusinessService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use QrCodeService;

class ShopService extends S
{
    public function __construct()
    {
        $this->redis = new ShopRedis();
        parent::__construct('Weixin');
    }

    /**
     * 分页获取总后台店铺列表
     * @author 吴晓平 <2018年09月18日>
     * @param  array $where [数组条件]
     * @param  integer $pageSize [每页显示数]
     * @return [type]            [description]
     */
    public function getAllList($where = [], $orderBy = '', $order = '', $pageSize = 15, $isWith = false)
    {
        $input = $this->request->input();
        $page = $input['page'] ?? 1;
        $page = intval($page);
        $perPage = $pageSize;
        $skip = ($page - 1) * $perPage;
        //分页
        $paginator = new LengthAwarePaginator([], $this->count($where), $perPage, null, ['path' => $this->request->url()]);
        //添加参数到分页
        $list = $paginator->appends($input);
        //分页HTML
        $pageHtml = $list->links();
        //分页信息
        $list = $list->toArray();
        //from第几条数据to第几条 不是下标
        $list['from'] = ($page - 1) * $perPage + 1;
        $list['to'] = $page * $perPage;
        $select = $this->model->wheres($where);
        if ($orderBy) {
            $select->order($orderBy . ' ' . $order);
        } else {
            $select->order('id desc');
        }
        $ids = $select->skip($skip)->take($perPage)->pluck('id')->toArray();
        $list['data'] = $this->getListById($ids, $isWith);
        return [$list, $pageHtml];
    }

    /**
     * 不分页获取总后台店铺列表
     * @author 吴晓平 <2018年09月18日>
     * @param  array $where [数组条件]
     * @param  integer $pageSize [每页显示数]
     * @return [type]            [description]
     */
    public function getListWithoutPage($where)
    {
        return $this->getList($where);
    }

    /**
     * 涉及到分页此方法必须有，基类调用了此方法
     * 通过数组id来查询微页面信息
     * @param array $idArr
     * @return array
     * @author WuXiaoPing
     * @date 2017-08-21
     * @update 吴晓平 2019年10月10日 20:16:50 优化总后台店铺管理数据不及时更新问题
     */
    public function getListById($idArr = [], $isWith = false)
    {
        $builder = $this->model->newQuery();
        $builder->withCount(['product' => function ($query) {
            $query->where('status', '!=', -1);
        }]);
        $builder->with(['user' => function ($query) {
            $query->with('saleAchieve');
        }]);
        $builder->with('weixinConfigSub:id,wid');
        $builder->with('wxxcxConfig:id,wid');
        $builder->with('weixinConfigMaster:id,wid');
        $data = $builder->whereIn('id', $idArr)->orderBy('id', 'desc')->get()->toArray();
        return $data;
    }

    /**
     * 获取店铺列表
     * @author 吴晓平 <2018年09月18日>
     * @param  array $input [页面搜索表单input数据]
     * @return [type]        [description]
     * @update 许立 2018年09月27日 处理店铺数据
     * @update 许立 2019年01月17日 导出数据处理
     */
    public function getShopList($input = [], $is_export = false)
    {
        //根据表单input数据，组装成搜索条件
        $shopWhere = $this->buildWhere($input);
        if ($is_export) {
            $returnData = $this->getListWithoutPage($shopWhere);
            $this->dealShopData($returnData, $is_export);
            return $returnData;
        } else {
            $returnData = $this->getAllList($shopWhere, $input['orderby'] ?? '', $input['order'] ?? '', 15, true);
            $this->dealShopData($returnData[0]['data'], $is_export);
            return $returnData;
        }
    }

    /**
     * 根据表单的input数据组成数组条件
     * @author 吴晓平 <2018年09月18日>
     * @param  array $input [description]
     * @return [type]        [description]
     */
    public function buildWhere($input = [])
    {
        if (empty($input)) {
            return [];
        }
        $where = [];
        $data = $this->dealInputData($input);
        if ($data) {
            foreach ($data as $key => $value) {
                switch ($key) {
                    case 'id':
                        $where['id'] = ['in', $value];
                        break;
                    case 'uid':
                        $where['uid'] = $value;
                        break;
                    case 'shop_name':
                        $where['shop_name'] = ['like', '%' . $value . '%'];
                        break;
                    case 'business_id':
                        $where['business_id'] = ['in', $value];
                        break;
                    case 'created_at':
                        if (is_array($value)) {
                            if (isset($value['startTime']) && !isset($value['endTime'])) {
                                $where['created_at'] = ['>=', $value];
                            } else if (isset($value['endTime']) && !isset($value['startTime'])) {
                                $where['created_at'] = ['<=', $value];
                            } else {
                                $where['created_at'] = array('between', [$value[0], $value[1]]);
                            }
                        }
                        break;
                    case 'expire_at':
                        if (is_array($value)) {
                            if (isset($value['expireFrom']) && !isset($value['expireTo'])) {
                                $where['shop_expire_at'] = ['>=', $value];
                            } else if (isset($value['expireTo']) && !isset($value['expireFrom'])) {
                                $where['shop_expire_at'] = ['<=', $value];
                            } else {
                                $where['shop_expire_at'] = array('between', [$value[0], $value[1]]);
                            }
                        }
                        break;
                    case 'province_id':
                        $where['province_id'] = $value;
                        break;
                    case 'city_id':
                        $where['city_id'] = $value;
                        break;
                    case 'area_id':
                        $where['area_id'] = $value;
                        break;
                    case 'sale_sum':
                        if (is_array($value)) {
                            if (isset($value['sum_from']) && !isset($value['sum_to'])) {
                                $where['sale_sum'] = ['>=', $input['sum_from']];
                            } else if (isset($value['sum_to']) && !isset($value['sum_from'])) {
                                $where['sale_sum'] = ['<=', $input['sum_to']];
                            } else {
                                $where['sale_sum'] = array('between', [$value[0], $value[1]]);
                            }
                        }
                        break;
                    case 'is_recommend':
                        $where['is_recommend'] = $value;
                        break;
                    case 'is_ignore':
                        $where['is_ignore'] = $value;
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
        return $where;
    }

    /* 获取单条数据
    * @param  [int] $id   [主键id]
    * @return [type]     [description]
    * @update 何书哲 2019年06月28日 存入redis由setRow改为add
    */
    public function getRowById($id, $with = [])
    {
        if (empty($id)) {
            error('数据异常');
        }
        $data = [];
        $data = $this->redis->getRow($id);
        if (empty($data)) {
            $withAll = ['weixinConfigMaster', 'weixinConfigSub', 'weixinPayment'];
            $obj = $this->model->wheres(['id' => $id])->with($withAll)->first();
            if ($obj) {
                $data = $obj->toArray();
                isset($data['weixinConfigMaster']) && $data['weixinConfigMaster'] = json_encode($data['weixinConfigMaster']);
                isset($data['weixinConfigSub']) && $data['weixinConfigSub'] = json_encode($data['weixinConfigSub']);
                isset($data['weixinPayment']) && $data['weixinPayment'] = json_encode($data['weixinPayment']);
                // update 何书哲 2019年06月28日 存入redis由setRow改为add
                $this->redis->add($data);
            }
        }
        return $data;
    }

    /**
     * 根据条件获取单条记录
     * @author 吴晓平 <2018年09月12日>
     * @return [type] [description]
     */
    public function getInfoByCondition($whereData = [])
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];
        $obj = $this->model->select('id')->where($whereData)->first();
        if ($obj) {
            $info = $obj->toArray();
            $data = $this->getRowById($info['id']);
            $returnData['data'] = $data;
        }
        return $returnData;
    }

    /**
     * 判断店铺是否已存在
     * @author 吴晓平 <2018年09月12日>
     * @param  [array] $where [数组条件]
     * @return [type]        [description]
     */
    public function checkStoreIsExist($where = [])
    {
        $return = false;
        $res = $this->model->where($where)->select('id')->count();
        if ($res) {
            $return = true;
        }
        return $return;
    }

    /**
     * 根据uid获取总共的店铺数
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function getShopsCount($uid)
    {
        return $this->model->where(['uid' => $uid])->select('id')->count();
    }

    //添加数据
    public function add($data)
    {
        return $this->model->insertGetId($data);
    }

    /**
     * 处理编辑
     * @param  [int] $id   [主键id]
     * @param  [array] $data [要更新的数组数据]
     * @return [type]       [description]
     */
    public function update($id, $data)
    {
        $rs = $this->model->wheres(['id' => $id])->update($data);
        if ($rs) {
            $this->redis->updateHashRow($id, $data);
            return true;
        }

        return false;
    }

    /**
     * 删除数据
     * @param  [int] $id   [主键id]
     * @return [type]     [description]
     */
    public function del($id)
    {
        $rs = $this->model->wheres(['id' => $id])->delete();
        if ($rs) {
            $this->redis->del($id);
            return true;
        }

        return false;
    }


    /**
     * 根据登录的商家id获取店铺
     * @author 吴晓平 <2018年09月12日>
     * @param  integer $uid [用户id]
     * @return [type]       [description]
     */
    public function getMyShops($uid = 0)
    {
        if (empty($uid)) {
            $uid = session('userInfo')['id'];
        }
        $weixinUserService = new WeixinUserService();
        $weixinUserData = $weixinUserService->init()->where(['uid' => $uid])->getList(false)[0]['data'];
        $wids = [];
        if ($weixinUserData) {
            foreach ($weixinUserData as $val) {
                $wids[] = $val['wid'];
            }
        }
        $whereData['id'] = ['in', $wids];
        return $this->getAllList($whereData, '', '', 9, true);
    }

    /**
     * 总后台判断是否有权限限制店铺
     * @param  integer $id [店铺id]
     * @param  integer $uid [用户id]
     * @return [type]      [description]
     */
    public function delShopWithRole($id, $uid = 0)
    {
        $result = ['success' => 0, 'message' => ''];
        if (empty($uid)) {
            $uid = session('userInfo')['id'];
        }
        $weixinData = $this->getRowById($id);
        if (!$weixinData) {
            $result['message'] = '店铺不存在';
            return $result;
        }
        if ($weixinData['uid'] != $uid) {
            $result['message'] = '该店铺不属于你！你无权限删除';
            return $result;
        } else {
            //删除关联表信息
            if ($this->del($id)) {
                $weixinUserService = new WeixinUserService();
                $weixinUserData = $weixinUserService->init()->where(['wid' => $id])->getList(false)[0]['data'];
                if ($weixinUserData) {
                    $weixinUserIds = [];
                    foreach ($weixinUserData as $val) {
                        $weixinUserService->init()->where(['wid' => $id])->delete($val['id'], false);
                    }

                    $weixinRoleService = new WeixinRoleService();
                    $weixinRoleData = $weixinRoleService->init()->model->where(['wid' => $id])->get()->toArray();
                    if ($weixinRoleData) {
                        foreach ($weixinRoleData as $val) {
                            $weixinRoleService->init()->where(['wid' => $id])->delete($val['id'], false);
                        }
                    }
                    $weChatShopConfService = new WeChatShopConfService();
                    if (!empty($weChatShopConfService->getRowByWid($id))) {
                        $weChatShopConfService->delData(['wid' => $id]);
                    }
                }
            }
            $result['success'] = 1;
            return $result;
        }
    }

    /**
     * 把搜索表单input数据处理成key_value的关联数组形式
     * @author 吴晓平 <2018年09月18日>
     * @param  [type] $input [description]
     * @return [type]        [description]
     * @update 许立 2018年09月26日 手机号搜索店铺功能修复, 是否推荐过滤
     */
    public function dealInputData($input)
    {
        $data = [];

        if (!empty($input['mphone'])) {
            $userData = (new UserService())->init()->model->where('mphone', $input['mphone'])->get(['id', 'mphone'])->toArray();
            $userData = array_pop($userData);
            $data['uid'] = $userData['id'] ?? 0;
        }

        if ((isset($input['startTime']) && !empty($input['startTime'])) && (isset($input['endTime']) && !empty($input['endTime']))) {
            $data['created_at'] = [$input['startTime'], $input['endTime']];
        } else {
            if (isset($input['startTime']) && !empty($input['startTime'])) {
                $data['created_at'] = ['startTime' => $input['startTime']];
            }
            if (isset($input['endTime']) && !empty($input['endTime'])) {
                $data['created_at'] = ['endTime' => $input['endTime']];
            }
        }

        if ((isset($input['expireFrom']) && !empty($input['expireFrom'])) && (isset($input['expireTo']) && !empty($input['expireTo']))) {
            $data['expire_at'] = [$input['expireFrom'], $input['expireTo']];
        } else {
            if (isset($input['expireFrom']) && !empty($input['expireFrom'])) {
                $data['expire_at'] = ['expireFrom' => $input['expireFrom']];
            }
            if (isset($input['expireTo']) && !empty($input['expireTo'])) {
                $data['expire_at'] = ['expireTo' => $input['expireTo']];
            }
        }

        if (isset($input['category']) && !empty($input['category'])) {
            $weixinBusinessService = new WeixinBusinessService();
            $businessData = $weixinBusinessService->init()->where(['pid' => $input['category']])->getList(false)[0]['data'];
            $businessIds = [$input['category']];
            if ($businessData) {
                foreach ($businessData as $val) {
                    $businessIds[] = $val['id'];
                }
            }
            $data['business_id'] = $businessIds;
        }

        //总销售额
        if ((isset($input['sum_from']) && !empty($input['sum_from'])) && (isset($input['sum_to']) && !empty($input['sum_to']))) {
            $data['sale_sum'] = [$input['sum_from'], $input['sum_to']];
        } else {
            if (isset($input['sum_from']) && !empty($input['sum_from'])) {
                $data['sale_sum'] = ['sum_from' => $input['sum_from']];
            }
            if (isset($input['sum_to']) && !empty($input['sum_to'])) {
                $data['sale_sum'] = ['sum_to' => $input['sum_to']];
            }
        }
        !empty($input['shopName']) && $data['shop_name'] = $input['shopName'];
        !empty($input['province_id']) && $data['province_id'] = $input['province_id'];
        !empty($input['city_id']) && $data['city_id'] = $input['city_id'];
        !empty($input['area_id']) && $data['area_id'] = $input['area_id'];
        !empty($input['uid']) && $data['uid'] = $input['uid'];

        if (isset($input['is_recommend']) && $input['is_recommend'] != 'all' && $input['is_recommend'] != '') {
            $data['is_recommend'] = $input['is_recommend'];
        }

        isset($input['is_ignore']) && is_numeric($input['is_ignore']) && in_array($input['is_ignore'], [0, 1]) && $data['is_ignore'] = $input['is_ignore'];
        !isset($input['is_ignore']) && $data['is_ignore'] = 0;

        return $data;
    }

    /**
     * 处理店铺数据
     * @param array $data 店铺数据
     * @param bool $is_export 许立 2018年04月19日 导出则不分页 且可能需要导出列表中没显示的字段 如 订单数等
     * @return array
     * @author 张永辉 2017年03月16日
     * @update 许立   2018年07月13日 增加导出店铺过期时间
     */
    public function dealShopData(&$data, $is_export)
    {
        $regionService = new RegionService();
        $weixinBusinessService = new WeixinBusinessService();
        $weixinBusinessData = $weixinBusinessService->init()->getList(false)[0]['data'];
        $category = [];
        $regionTmp = [];
        $ids = [];
        $uid = [];
        $wid_array = [];
        foreach ($data as $val) {
            $ids[$val['province_id']] = $val['province_id'];
            $ids[$val['city_id']] = $val['city_id'];
            $ids[$val['area_id']] = $val['area_id'];
            $uid[] = $val['uid'];
            $wid_array[] = $val['id'];
        }
        unset($ids[0]);
        $ids = array_keys($ids);
        $ids = array_filter($ids);
        $regionData = $regionService->getListById($ids);
        foreach ($weixinBusinessData as $val) {
            $category[$val['id']] = $val['title'];
        }
        foreach ($regionData as $val) {
            $regionTmp[$val['id']] = $val['title'];
        }

        $paid_order_count_array = [];
        $role_array = [];
        // 过期时间
        $end_time_array = [];
        if ($is_export && $wid_array) {
            //获取订单数 Herry
            $connect = \DB::connection('mysql_dc_log');
            $orders = $connect->select("select wid, sum(order_payed_count) as paid_order_count from dc_order where wid in ( " . implode(',', $wid_array) . ") group by wid");
            if (!empty($orders)) {
                foreach ($orders as $v) {
                    $paid_order_count_array[$v->wid] = $v->paid_order_count;
                }
            }

            //获取店铺角色 Herry
            $roles = \DB::table('weixin as w')
                ->leftJoin('weixin_role as wr', 'w.id', '=', 'wr.wid')
                ->leftJoin('admin_role as ar', 'wr.admin_role_id', '=', 'ar.id')
                ->whereIn('w.id', $wid_array)
                ->whereNull('wr.deleted_at')
                ->get(['w.id', 'ar.name', 'wr.end_time'])
                ->toArray();
            if (!empty($roles)) {
                foreach ($roles as $v) {
                    $role_array[$v->id] = $v->name;
                    // 过期时间
                    $end_time_array[$v->id] = $v->end_time;
                }
            }
        }
        foreach ($data as $key => &$value) {
            $value['province_name'] = $regionTmp[$value['province_id']] ?? '';
            $value['city_name'] = $regionTmp[$value['city_id']] ?? '';
            $value['area_name'] = $regionTmp[$value['area_id']] ?? '';
            $value['category_name'] = $category[$value['business_id']] ?? '';
            $value['paid_order_count'] = $paid_order_count_array[$value['id']] ?? 0;
            $value['role'] = $role_array[$value['id']] ?? '测试店铺';
            // 过期时间
            $value['end_time'] = $end_time_array[$value['id']] ?? '不过期';
        }
    }


    /**
     * 设置忽略
     * @param $wid
     * @param $ignore
     * @return bool
     * @author: 梅杰 2018年9月26日
     */
    public function ignoreShop($wid, $ignore)
    {
        if ($data = $this->model->find(['id' => $wid])) {
            return $this->model->where(['id' => $wid])->update(['is_ignore' => $ignore]) && $this->redis->updateHashRow($wid, $data[0]->toArray());
        }
        return false;
    }

    /**
     * 批量设置忽略
     * @return bool
     * @author: 梅杰 2018年9月26日
     */
    public function batchIgnore()
    {
        return $this->model->withCount(['product'])->chunk(100, function ($shop) {
            $update = [];
            foreach ($shop as $v) {
                if ($v->is_fee == 0 && $v->productCount <= 2) {
                    $update[] = [
                        'id' => $v->id,
                        'is_ignore' => 1
                    ];
                    $v->is_ignore = 1;
                    $this->redis->updateHashRow($v->id, $v->toArray());
                }
            }
            //批量更新
            if ($update) {
                $re = UpdateBatch::updateBatch($update, 'weixin');
                \Log::info('店铺忽略脚本运行结果' . $re);
            }
        });
    }

    /**
     * 同步到商家案例
     * @author 吴晓平 <2018.11.22>
     * @return [type] [description]
     */
    public function syncWeixinCase()
    {
        $saveData = [];
        $result = $this->model->where('is_recommend', 1)->select(['id', 'business_id', 'logo', 'shop_name', 'shop_expire_at'])->withCount(['weixinConfigSub', 'wxxcxConfig'])->with('ShopRole')->chunk(50, function ($data) use ($saveData) {
            foreach ($data as $key => $value) {
                //已存在的商家案例跳过
                if (WeixinCase::where('wid', $value->id)->count()) {
                    continue;
                }
                //权限
                if (empty($value['ShopRole'])) {
                    continue;
                }
                // 已授权微商城
                if ($value->weixinConfigSub && (in_array($value['ShopRole']['admin_role_id'], [1, 2, 3, 4, 5, 8, 9]))) {
                    $weixinItem['type'] = 1;
                    $weixinItem['wid'] = $value->id;
                    $weixinItem['title'] = $value->shop_name;
                    $weixinItem['business_id'] = $value->business_id;
                    $weixinItem['shop_expire_at'] = $value->shop_expire_at;
                    $redirectUrl = config('app.url') . 'shop/index/' . $value->id;
                    //生成商家公众号二维码
                    if (file_exists($value->logo)) {
                        $water_logo = '/public/' . $value->logo;
                        QrCodeService::create($redirectUrl, $water_logo, 300, 'case_' . $value->id, 20);
                    } else {
                        QrCodeService::create($redirectUrl, '', 300, 'case_' . $value->id);
                    }
                    $weixinItem['qrcode'] = 'hsshop/image/qrcodes/case_' . $value['id'] . '/qrcode.png';
                    array_push($saveData, $weixinItem);
                }
                // 已授权小程序
                if ($value->wxxcxConfig && (in_array($value['ShopRole']['admin_role_id'], [3, 4, 5, 6, 7, 9]))) {
                    $xcxItem['type'] = 2;
                    $xcxItem['wid'] = $value->id;
                    $xcxItem['title'] = $value->shop_name;
                    $xcxItem['business_id'] = $value->business_id;
                    $xcxItem['shop_expire_at'] = $value->shop_expire_at;
                    $xcxItem['qrcode'] = '';
                    // 使用队列对小程序的二维码进行异步更新
                    $job = new CreateCaseXcxQrocde($value->id);
                    dispatch($job->onQueue('CreateCaseXcxQrocde'));
                    array_push($saveData, $xcxItem);
                }
            }
            if ($saveData) {
                if (WeixinCase::insert($saveData)) {
                    return true;
                }
            } else {
                return false;
            }
        });
        return $result;
    }

    /**
     * 更新小程序二维码
     * @author 吴晓平 <2018.11.27>
     * @param  [integer] $wid [店铺id]
     * @return [type]      [description]
     */
    public function updateXcxQrcode($wid)
    {
        $res = (new CommonModule())->qrCode($wid, 'pages/index/index', 1);
        if ($res['errCode'] <> 0) {
            WeixinCase::where('wid', $wid)->where('type', 2)->delete();
        }
        $data['qrcode'] = $res['data'];
        if (!WeixinCase::where('wid', $wid)->where('type', 2)->update($data)) {
            return false;
        }
        \Log::info('店铺--' . $wid . ' 小程序案例二维码更新成功');
        return true;
    }

    /**
     * 获取即将到期的店铺列表，并处理发送短信提醒
     * 主要是筛选出离到期时间还有45天，15天，3天
     * @author 吴晓平 <2019.04.30>
     * @return [type] [description]
     */
    public function getBeExpireList()
    {
        // 构造查询构造器
        $builder = $this->model->newQuery();
        // 当前日期
        $currentDate = Carbon::now()->toDateString();
        // 到时时间等于45天
        $reminder45Date = Carbon::now()->addDays(45)->toDateString();
        // 到时时间等于15天
        $reminder15Date = Carbon::now()->addDays(15)->toDateString();
        // 到时时间等于3天
        $reminder03Date = Carbon::now()->addDays(3)->toDateString();

        // 只筛选付费未到期的店铺
        $builder->whereDate('shop_expire_at', '>=', $currentDate);
        $builder->where(function ($query) use ($reminder45Date, $reminder15Date, $reminder03Date) {
            $query->whereDate('shop_expire_at', $reminder45Date)
                ->orWhere(function ($query) use ($reminder15Date) {
                    $query->whereDate('shop_expire_at', $reminder15Date);
                })->orWhere(function ($query) use ($reminder03Date) {
                    $query->whereDate('shop_expire_at', $reminder03Date);
                });
        });
        $result = $builder->with('user:id,mphone')
            ->select(['id', 'shop_expire_at', 'uid'])
            ->chunk(200, function ($list) use ($reminder45Date, $reminder15Date, $reminder03Date) {
            if (!$list->isEmpty()) {
                // 根据到期时间进行分组
                $newList = $list->groupBy('shop_expire_at')->toArray();
                // 分别对对应的到期时间提取手机号
                foreach ($newList as $key => $value) {
                    $date = Carbon::parse($key)->toDateString();
                    // 拼接手机号用于批量发送
                    $phones = collect($value)->pluck('user.mphone')->unique()->implode(',');
                    switch ($date) {
                        case $reminder45Date:
                            $this->expireRemindSendSms(45, $phones);
                            break;
                        case $reminder15Date:
                            $this->expireRemindSendSms(15, $phones);
                            break;
                        case $reminder03Date:
                            $this->expireRemindSendSms(3, $phones);
                            break;
                        default:
                            break;
                    }
                }
                return true;
            }
        });
        return $result;
    }

    /**
     * 执行发送短信提醒 （优化不用队列处理）
     * @author 吴晓平 <2019.04.30>
     * @return [type] [description]
     */
    public function expireRemindSendSms($day, $phones)
    {
        $smsData = [$day];
        try {
            app(SendSmsNoticeHandler::class)->sendNotice($phones, $smsData, 1);
            // 暂停0.5秒执行 （按照upyun的接口说明，1分钟最多可请求300次，即1秒最多5次，1次0.2秒，这里取个中间值暂停0.5秒执行）
            usleep(500000);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }
    }
}
