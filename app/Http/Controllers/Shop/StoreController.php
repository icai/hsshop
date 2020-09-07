<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/6/27
 * Time: 9:01
 */

namespace App\Http\Controllers\shop;


use App\Http\Controllers\Controller;
use App\Model\FileInfo;
use App\S\Foundation\RegionService;
use App\S\Store\StoreService;
use App\S\StoreRepositories;
use Illuminate\Http\Request;
use App\S\PublicShareService;
use App\S\Lift\ReceptionService;

class StoreController extends Controller
{

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170627
     * @desc 门店列表
     */
    public function getStore()
    {
        return view('shop.store.getStore',array(
            'title'         => '门店列表',
            'shareData'     => (new PublicShareService())->publicShareSet(session('wid'))
        ));
    }
    public function storeMap(StoreService $storeService,RegionService $regionService,$id,$source=0)
    {
        $storeData = $storeService->getRowById($id);
        if ($source == 1) {
            $storeData = (new ReceptionService())->getRowById($id);
            $storeData['imgs'] = $storeData['images'];
        }
        $regions = $regionService->getAll();
        foreach($regions as $value){
            $regionList[$value['id']] = $value;
        }
        if ($storeData['imgs']){
            $ids = explode(',',$storeData['imgs']);
            $path = FileInfo::select(['id','s_path'])->find($ids[0]);
            if ($path){
                $storeData['imgs'] = $path->s_path;
            }
        }
        $storeData['province'] = $regionList[$storeData['province_id']]['title'];
        $storeData['city'] = $regionList[$storeData['city_id']]['title'];
        $storeData['area'] = $regionList[$storeData['area_id']]['title'];

        return view('shop.store.storeMap',array(
            'title'     => '线下门店地图',
            'store'     => $storeData,
            'storeJson' => json_encode($storeData),
            'shareData' => (new PublicShareService())->publicShareSet(session('wid'))
        ));
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20170628
     * @desc 获取门店列表
     * @param Request $request
     */
    public function getStoreList(Request $request,StoreRepositories $storeRepositories)
    {
        $word = $request->input('word')??'';
        $store = $storeRepositories->search($word);
        return mysuccess('操作成功','',$store);
    }

    /**
     * 获取自提点列表
     * @return [type] [description]
     */
    public function getShopZiti(Request $request,StoreRepositories $storeRepositories)
    {
        $wid = session('wid');
        $keyword = $request->input('word') ?? '';
        $list = $storeRepositories->search($keyword,1);
        return view('shop.store.shopZiti',[
            'title' => '商户自提点列表',
            'list'  =>  $list,
        ]);
    }

}