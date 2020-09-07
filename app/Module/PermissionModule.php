<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/1/10
 * Time: 9:51
 */

namespace App\Module;


use App\Jobs\openPermission;
use App\Lib\Redis\openPermissionRedis;
use App\Model\MicroPage;
use App\Model\UserFile;
use App\S\Product\ProductService;
use App\S\Staff\AccountService;
use App\S\Staff\StaffPermissionService;
use App\Service\MicroPageService;
use App\Services\Permission\PermissionService;
use DB;

class PermissionModule
{


    public $sourceRole = ['10','11','12','13'];

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180110
     * @desc 获取总后台登陆权限
     */
    public function getStaffPermission($accountId)
    {
        $all = $this->getAllRount();
        $accountData = (new AccountService())->getRowById($accountId);
        if ($accountData['is_super'] == 1) {
            return $all;
        }
        $accountPermission = $this->getAccountPermission($accountId);
        $rounts = [];
        foreach ($accountPermission as $key => $val) {
            $rounts[] = $val['route'];
        }
        foreach ($all as $key => $item) {
            foreach ($item['grandson'] as $k => $val) {
                if (!in_array($val['url'], $rounts)) {
                    unset($all[$key]['grandson'][$k]);
                }
            }
            if (!$all[$key]['grandson']) {
                unset($all[$key]);
            } else {
                $tmp = current($all[$key]['grandson']);
                $all[$key]['name']['url'] = $tmp['url'];
            }
        }
//        show_debug($all);
        return $all;

    }


    public function getAllRount()
    {
        $result = [
            '商家活跃度' => [
                'name' => [
                    'name' => '商家活跃度',
                    'url' => '/staff/get/weixin/statistic'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '数据统计', 'url' => '/staff/get/weixin/statistic'],
                ]
            ],
            'Banner管理' => [
                'name' => [
                    'name' => 'Banner管理',
                    'url' => '/staff/banner/index'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => 'Banner列表', 'url' => '/staff/banner/index'],
                    ['name' => '广告列表', 'url' => '/staff/banner/ad'],
                    ['name' => 'APP广告列表', 'url' => '/staff/banner/sellerappad'],
                ]
            ],
            '店铺管理' => [
                'name' => [
                    'name' => '店铺管理',
                    'url' => '/staff/BusinessCategory'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '分类管理', 'url' => '/staff/BusinessCategory'],
                    ['name' => '企业会员', 'url' => '/staff/userlist'],
                    ['name' => '店铺会员', 'url' => '/staff/getShop'],
                    ['name' => '默认模板设置', 'url' => '/staff/getTemplate'],
                    ['name' => '上传微信公众号文件', 'url' => '/staff/uploadFile'],
                    ['name' => '店铺访客', 'url' => '/staff/BusinessManage/customer'],
                    ['name' => '店铺公告', 'url' => '/staff/BusinessManage/affiche'],
                    ['name' => '地址管理', 'url' => '/staff/BusinessManage/regionManage'],
                    ['name'=>'续费订购','url'=>'/staff/fee/order/list'],
                    ['name'=>'发票管理','url'=>'/staff/fee/invoice/list'],
                ]
            ],
            '商品管理' => [
                'name' => [
                    'name' => '商品管理',
                    'url' => '/staff/product/category'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '商品品类', 'url' => '/staff/product/category'],
                ]
            ],
            '资讯管理' => [
                'name' => [
                    'name' => '资讯管理',
                    'url' => '/staff/getInformation'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '资讯列表', 'url' => '/staff/getInformation'],
                    ['name' => '添加资讯', 'url' => '/staff/editInformation'],
                    ['name' => '资讯分类', 'url' => '/staff/getInfoType'],
                    ['name' => '推荐管理', 'url' => '/staff/getRecomment'],
                    //['name' => '帮助管理', 'url' => '/staff/getRecomment'],
                ]
            ],
            '权限管理' => [
                'name' => [
                    'name' => '权限管理',
                    'url' => '/staff/getAdminRole'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '总后台权限管理', 'url' => '/staff/getAdminRole'],
                    ['name' => '店铺权限管理', 'url' => '/staff/getRole'],
                    ['name' => '账号管理', 'url' => '/staff/account'],
                ]
            ],
            '潜在客户管理' => [
                'name' => [
                    'name' => '潜在客户管理',
                    'url' => '/staff/customer/reserveManage'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '全部', 'url' => '/staff/customer/reserveManage'],
                    ['name' => '加星客户', 'url' => '/staff/customer/reserveManage?status=1'],
                ]
            ],
            '客服管理' => [
                'name' => [
                    'name' => '客服管理',
                    'url' => '/staff/CustomerServiceManage'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '客服列表', 'url' => '/staff/CustomerServiceManage'],
                ]
            ],
            '分享统计' => [
                'name' => [
                    'name' => '分享统计',
                    'url' => '/staff/shareIncome'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '分享统计列表', 'url' => '/staff/shareIncome'],
                ]
            ],
            '案例管理' => [
                'name' => [
                    'name' => '案例管理',
                    'url' => '/staff/example/index'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '案例列表', 'url' => '/staff/example/index'],
                    ['name' => '行业分类', 'url' => '/staff/example/industry'],
                ]
            ],
            'SEO管理' => [
                'name' => [
                    'name' => 'SEO管理',
                    'url' => '/staff/seo/index'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => 'SEO列表', 'url' => '/staff/seo/index'],
                ]
            ],
            '友情链接' => [
                'name' => [
                    'name' => '友情链接',
                    'url' => '/staff/link/index'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '友链管理', 'url' => '/staff/link/index'],
                    ['name' => '网站地图', 'url' => '/staff/link/mapIndex'],
                ]
            ],
            '小程序' => [
                'name' => [
                    'name' => '小程序',
                    'url' => '/staff/customer/searchXCX'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '查询记录', 'url' => '/staff/customer/searchXCX'],
                    ['name' => '小程序数据库', 'url' => '/staff/customer/liteapp'],
                    ['name' => '小程序列表', 'url' => '/staff/xcx/list'],
                     ['name' => '更新失败小程序统计', 'url' => '/staff/xcx/updateErrorStatistics'],
                ]
            ],
            '业务员跟单' => [
                'name' => [
                    'name' => '业务员跟单',
                    'url' => '/staff/seller/index'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '业务员列表', 'url' => '/staff/seller/index'],
                    ['name' => '业务员邀请人', 'url' => '/staff/seller/getSaleManDetail'],
                    ['name' => '分组统计', 'url' => '/staff/seller/sellerIndex'],
                ]
            ],
            '行业解决方案' => [
                'name' => [
                    'name' => '行业解决方案',
                    'url' => '/staff/weixin/case_list'
                ],
                'son' => [],
                'grandson' => [
                    ['name' => '行业案例列表', 'url' => '/staff/weixin/case_list'],
                ]
            ],
        ];

        return $result;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180110
     * @desc
     */
    public function getAccountPermission($accountId)
    {
        $sql = 'SELECT p.route,p.id FROM ds_staff_permission as sp LEFT JOIN ds_permission as p ON sp.permission_id=p.id WHERE sp.account_id=' . $accountId;
        $res = DB::select($sql);
        $res = json_decode(json_encode($res), true);
        return $res;
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180111
     * @desc
     */
    public function getPermission($id)
    {
        $permission = (new PermissionService())->init()->model->where('type', 2)->get()->toArray();
        $res = $this->getAccountPermission($id);
        $accountPermission = [];
        foreach ($res as $item) {
            $accountPermission[] = $item['id'];
        }
        return [
            'permission' => $permission,
            'accountPermission' => $accountPermission,
        ];
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180111
     * @desc 绑定权限
     * @param $data
     */
    public function bindStaffPermission($data)
    {
        $staffPermissionService = new StaffPermissionService();
        $res = $staffPermissionService->getList(['account_id' => $data['id']]);
        foreach ($res as $val) {
            $staffPermissionService->del($val['id']);
        }
        foreach ($data['permissionIds'] ?? [] as $item) {
            $temp = [
                'permission_id' => $item,
                'account_id' => $data['id'],
            ];
            $staffPermissionService->add($temp);
        }
        return true;
    }


    /**
     * 一键开通权限
     * @param array $input
     * @return bool
     * @author: 梅杰 2018年7月31日
     * @update 梅杰2018年9月10日 手机号码未输入或者不正确时错误信息处理
     */
    public function openPermission($input = [])
    {
        //验证电话号
        $phone = explode("\r\n", trim($input['phone']));
        if (!$phone) {
            error('请按格式输入手机号');
        }
        foreach ($phone as $value) {
            if (!preg_match("/^1[345789]\d{9}$/", trim($value))) {
               error('录入的电话号码格式有误');
            }
            $data = $input;
            $data['phone'] = $value;
            $job = new openPermission($data);
            dispatch($job->onQueue('openPermission'));
        }
        return true;
    }

    /**
     * 一键开通权限缓存日志
     * @return array
     * @author: 梅杰 20180731
     */
    public function openPermissionLog()
    {
        return  (new openPermissionRedis())->getAll();
    }


    /**
     * 整理返回信息
     * @param $message
     * @author 张永辉 2018年10月23日
     */
    public function returnInfo($message)
    {
        if (app('request')->expectsJson()) {
            error($message);
        } else {
            $str = <<<EOF
         <script type="text/javascript">
            alert("$message");
            window.history.go(-1);
         </script>
EOF;
            echo $str;
            exit();
        }
    }


    /**
     * 验证秘钥
     * @author 张永辉 2018年10月23日
     */
    public function checkPermission($role_id, $wid, $type)
    {
        switch ($type) {
            case 'create_product' :
                $productService = new ProductService();
                $productNum     = $productService->model->where('wid', $wid)->count();
                if ($productNum >= 20) {
                    return false;
                } else {
                    return true;
                }
                break;
            case 'create_page':
                $pageNum = MicroPage::where('wid', $wid)->count();
                if ($pageNum >= 20) {
                    return false;
                } else {
                    return true;
                }
                break;
            case 'up_video':
                $videoNum = UserFile::where('weixin_id',$wid)->where('file_mine','2')->count();
                if ($videoNum >= 20) {
                    return false;
                } else {
                    return true;
                }
                break;
            case 'up_file':
                $fileNum = UserFile::where('weixin_id',$wid)->where('file_mine','1')->count();
                if ($fileNum >= 200) {
                    return false;
                } else {
                    return true;
                }
                break;
        }
    }




}
