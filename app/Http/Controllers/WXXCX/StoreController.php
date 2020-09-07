<?php
/**
 * 店铺控制器
 * User: Herry
 * Date: 2018/06/26
 * Time: 9:39
 */

namespace App\Http\Controllers\WXXCX;

use App\Http\Controllers\Controller;
use App\Model\FileInfo;
use App\S\Foundation\RegionService;
use App\S\Store\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * 联系我们接口
     * @param Request $request 请求参数
     * @param StoreService $storeService 门店类
     * @return json
     * @author 许立 2018年6月26日
     * @update 许立 2018年08月09日 地址返回省市区
     */
    public function contact(Request $request, StoreService $storeService)
    {
        // 获取门店列表
        $list = $storeService->listWithoutPage(['wid' => $request->input('wid')]);
        $list = $list[0]['data'];

        $regionService = new RegionService();
        foreach ($list as $k => $store) {
            // 获取图片地址
            $store['file'] = FileInfo::whereIn('id', explode(',', $store['imgs']))->get()->toArray();

            // 返回省市区
            $province = $regionService->getRowById($store['province_id'])['title'] ?? '';
            $city = $regionService->getRowById($store['city_id'])['title'] ?? '';
            $area = $regionService->getRowById($store['area_id'])['title'] ?? '';
            $store['address'] = $province . $city . $area . $store['address'];

            // 处理运营时间
            $list[$k] = $storeService->dealWithOpenTime($store);
        }

        xcxsuccess('', $list);
    }

}