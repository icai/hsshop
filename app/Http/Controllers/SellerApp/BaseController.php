<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2018/2/23
 * Time: 9:33
 */

namespace App\Http\Controllers\SellerApp;


use App\Http\Controllers\Controller;
use App\Module\BaseModule;
use App\Module\RSAModule;
use App\S\Foundation\RegionService;
use Illuminate\Http\Request;

class BaseController extends Controller
{

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180223
     * @desc 获取App连接基础信息
     */
    public function index(Request $request)
    {
        $input = (new RSAModule())->decrypt($request->input('parameter'));
        $result = (new BaseModule())->getBase($input);
        appsuccess('操作成功',$result);
    }

    public function puEncr(Request $request,RSAModule $RSAModule)
    {
        echo $RSAModule->pubEncrypt(json_encode($request->input()));
    }

    public function puDecr(Request $request,RSAModule $RSAModule)
    {
        $res = $RSAModule->pubDecrypt($request->input('parameter'));
        echo json_encode($res);
    }

    public function priEncr(Request $request,RSAModule $RSAModule)
    {
        $res = $RSAModule->encrypt(json_encode($request->input()));
        echo $res;
    }

    public function priDEcr(RSAModule $RSAModule,Request $request)
    {
        $res = $RSAModule->decrypt($request->input('parameter'));
        echo json_encode($res);
    }


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20180316
     * @desc 获取地址信息
     */
    public function getRegion(RegionService $regionService)
    {
        $regions = $regionService->getAll();
//        foreach ($regions as $value) {
//            $regionList[$value['pid']][] = $value;
//        }
        $regionList = $this->listToTree($regions,'id','pid','child',-1);
        appsuccess('操作成功',$regionList);
    }

    public function listToTree($list, $pk = 'id', $pid = 'parent_id', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = [];

        if (is_array($list) && count($list)) {
            // 创建基于主键的数组引用
            $refer = [];
            foreach ($list as $key => $value) {
                $refer[$value[$pk]]         =& $list[$key];
                $refer[$value[$pk]][$child] = [];
            }
            foreach ($list as $key => $value) {
                // 判断是否存在parent
                $parentId = $value[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent           =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }












}