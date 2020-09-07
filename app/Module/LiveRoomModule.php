<?php
/**
 * Created by PhpStorm.
 * User: jianrongjiao
 * Date: 2020/3/6
 * Time: 3:35 PM
 */
namespace App\Module;

use App\Services\Marketing\Exception;
use Illuminate\Support\Facades\Cache;

/**
 * @desc 获取直播间数据接口
 * Class LiveRoomModule
 * @package App\Module
 * @author 焦建荣【945184949@qq.com】2020年03月07日
 */
class LiveRoomModule
{
    /**
     * @var 公用缓存的key值
     */
    private $baseKey;

    /**
     * @desc 获取直播间数据采取Redis缓存方案
     * @param $mId
     * @param $flush
     * @param $page
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 焦建荣【945184949@qq.com】2020年03月09日
     */
    public function liveRoom($mId, $flush, $page)
    {
        $returnData = ['errCode' => 0, 'errMsg' => '', 'data' => []];

        try {
            $baseKey = 'live_room_mid' . $mId . 'page';

            $this->baseKey = $baseKey;

            // 如果是强制刷新得跳到第一页
            if ($flush) {
                $page = 1;
                // 如果是刷新强制跳转首页
                Cache::forget($baseKey);
                Cache::forget('total_mid_' . $mId);
                $data = $this->dealOriginData($mId, $page, 15);
                // 删除其他页的数据缓存
                $this->delCache($baseKey, $data);
                // 缓存
                Cache::put($baseKey, 1, 15);
                Cache::put($baseKey . '1', json_encode($data), 10);
                $returnData['data'] = (array)$data;
                return $returnData;
            }

            $baseValue = Cache::get($baseKey);
            $basePageValue = Cache::get($baseKey . $page);

            if ($baseValue) {
                // 如果缓存未到期并且该页记录存在直接返回
                if ($basePageValue) {
                    $data = json_decode($basePageValue);
                    $returnData['data'] = (array)$data;
                    return $returnData;
                } else {
                    // 否则去请求数据
                    $data = $this->dealOriginData($mId, $page, 15);
                    Cache::put($baseKey . $page, json_encode($data), 10);
                    $returnData['data'] = (array)$data;
                    return $returnData;
                }
            } else {
                // 如果缓存到期就自动获取数据
                $data = $this->dealOriginData($mId, $page, 15);
                if ($page == 1) {
                    // 如果是第一页得共享总页数，算法的核心问题是所有页面共享总页数否则页面的样式会乱掉
                    // 设置缓存时间
                    Cache::put($baseKey, 1, 15);
                    // 设置缓存的总页数
                    Cache::put('total_mid_' . $mId, $data['total'], 15);
                }
                Cache::put($baseKey . $page, json_encode($data), 15);
                $returnData['data'] = (array)$data;
                return $returnData;
            }
        } catch (Exception $e) {
            if (41026 == $e->getCode()) {
                $returnData['errCode'] = 41026;
                $returnData['errMsg']  = $e->getMessage();
                return $returnData;
            }

            $returnData['errCode'] = -1;
            $returnData['errMsg']  = '未知错误，请稍后再试';
            return $returnData;
        }
    }


    /**
     * @desc 删除所有的缓存
     * @param $baseKey
     * @param $data
     * @return bool
     * @author 焦建荣【945184949@qq.com】2020年03月06日
     */
    private function delCache($baseKey, $data)
    {
        // 获取总页数
        $totalPage = ceil($data['total'] / $data['per_page']);

        // 删除每一页的缓存数据
        for ($i = 1; $i <= $totalPage; $i++) {
            Cache::forget($baseKey . $i);
        }

        return true;
    }

    /**
     * @desc 获取原始数据并且处理数据格式
     * @param $mId
     * @param $page
     * @param $limit
     * @return array
     * @throws \App\Exceptions\CommonException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author 焦建荣【945184949@qq.com】2020年03月09日
     *
     * @update 梅杰 at 2020-03-28 10:18:42 增加企业筛选
     */
    private function dealOriginData($mId, $page, $limit)
    {
        // 起始拉取房间，start=0表示从第1个房间开始拉取 limit 每次拉取的个数上限，不要设置过大，建议100以内
        $start = ($page - 1) * $limit;
        $data = (new XCXModule())->liveRoom($mId, $start, $limit);
        $list = [];
        foreach ($data['room_info'] as $item) {
            $list[] = [
                'name' => $item['name'],
                'cover_img' => $item['cover_img'],
                'start_time' => date('Y-m-d H:i:s', $item['start_time']),
                'end_time' => date('Y-m-d H:i:s', $item['end_time']),
                'anchor_name' => $item['anchor_name'] ?? '',
                'anchor_img' => $item['anchor_img'] ?? '',
                'live_status' => $item['live_status'] ?? '',
                'id' => $item['roomid'],
            ];
        }
        $result = $this->constructPage($data, $page, $mId);
        $result['data'] = $list;
        return $result;
    }

    /**
     * @desc 手动构造分页
     * @param $param
     * @param $page
     * @param $mId
     * @param int $perPage
     * @return array
     * @author 焦建荣【945184949@qq.com】2020年03月06日
     */
    private function constructPage($param, $page, $mId, $perPage = 15)
    {
        // 在缓存的情况下每个页面获取的总页数暂时保持一致【分页核心问题，共享总页数】
        $total = Cache::get('total_mid_' . $mId);

        if (!$total) {
            $total = $param['total'];
            Cache::put('total_mid_' . $mId, $param['total'], 15);
        } elseif ($total != $param['total']) {
            // 检查腾讯服务器数据更新后删除Redis的缓存
            $total = $param['total'];
            Cache::put('total_mid_' . $mId, $param['total'], 15);
            $param['per_page'] = $perPage;
            $this->delCache($this->baseKey, $param);
        }

        // 构造和数据库一样的数据结构
        // 最后一页
        $lastPage = ceil($total / $perPage);
        // 每页的开始数据
        $from = ($page - 1) * $perPage + 1;
        // 没有的结束数据
        $to = $page * $perPage;
        if ($page >= $lastPage) {
            $to = $total;
        }
        $data = [
            'current_page' => (int)$page,
            'from' => (int)$from,
            'per_page' => (int)$perPage,
            'last_page' => (int)$lastPage,
            'to' => (int)$to,
            'total' => (int)$total,
            'data' => []
        ];

        return $data;
    }

}