<?php

namespace App\Module;
use App\Lib\Redis\FavoriteRedis;
use App\Model\Favorite;
use App\S\FavoriteService;
use App\S\Groups\GroupsRuleService;
use App\S\Market\SeckillService;
use App\S\Product\ProductService;
use App\S\ShareEvent\ShareEventService;
use DB;

/**
 * 收藏
 * @author 许立 2018年09月04日
 */
class FavoriteModule
{
    /**
     * 构造函数
     * @param FavoriteService $favoriteService 收藏类
     * @param FavoriteRedis $favoriteRedis 收藏redis类
     * @return $this
     * @author 许立 2018年09月04日
     */
    public function __construct(FavoriteService $favoriteService, FavoriteRedis $favoriteRedis)
    {
        $this->favoriteService = $favoriteService;
        $this->favoriteRedis = $favoriteRedis;
    }

    /**
     * 是否收藏
     * @param int $mid 用户id
     * @param int $relativeId 关联元素id
     * @param int $type 收藏类型
     * @return bool
     * @author 许立 2018年09月04日
     */
    public function isFavorite($mid, $relativeId, $type)
    {
        return !!$this->favoriteService->getRow($mid, $relativeId, $type);
    }

    /**
     * 收藏数
     * @param array $relativeIdArray 关联元素id数组
     * @param int $type 收藏类型
     * @return array
     * @author 许立 2018年09月05日
     */
    public function favoriteCount($relativeIdArray, $type)
    {
        $select = $this->favoriteService
            ->model
            ->select(DB::raw("relative_id, count(*) AS favoriteCount"));

        if (count($relativeIdArray) == 1) {
            $select = $select->where('relative_id', (int)$relativeIdArray[0]);
        } else {
            $select = $select->whereIn('relative_id', $relativeIdArray);
        }

        return $select->where('type', $type)
            ->groupBy('relative_id')
            ->get()
            ->toArray();
    }

    /**
     * 收藏
     * @param int $wid 店铺id
     * @param int $mid 用户id
     * @param array $input 参数数组
     * @return bool
     * @author 许立 2018年09月04日
     * @update 许立 2018年09月06日 保存收藏内容的图片, 保存享立减商品id
     */
    public function favorite($wid, $mid, $input)
    {
        // 判断是否已收藏
        if ($this->isFavorite($mid, $input['relativeId'], $input['type'])) {
            return true;
        }

        // 新增记录
        $data = [
            'relative_id' => $input['relativeId'],
            'mid' => $mid,
            'type' => $input['type'],
            'wid' => $wid,
            'title' => $this->_handleFavoriteTitle($input['type'], $input['title']),
            'price' => $input['price'],
            'image' => $input['image'],
            'share_product_id' => $input['share_product_id'] ?? 0
        ];

        // 入库
        if ($this->favoriteService->insertRow($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 取消收藏
     * @param int $mid 用户id
     * @param int $relativeId 关联元素id
     * @param int $type 收藏类型
     * @return bool
     * @author 许立 2018年09月05日
     */
    public function cancelFavorite($mid, $relativeId, $type)
    {
        // 判断是否已取消收藏
        $row = $this->favoriteService->getRow($mid, $relativeId, $type);
        if (!$row) {
            return true;
        }

        // 取消收藏
        $dbDeleteResult = $this->favoriteService->deleteRow($row->id);
        $dbDeleteResult && (new FavoriteRedis())->delete($row->id);
        return !!$dbDeleteResult;
    }

    /**
     * 遍历数组赋值收藏数
     * @param array $array 数据列表
     * @param int $type 收藏类型
     * @return array
     * @author 许立 2018年09月05日
     */
    public function handleListFavoriteCount($array, $type)
    {
        // 获取关联元素id
        $relativeIdArray = array_column($array, 'id');
        if (empty($relativeIdArray)) {
            return [];
        }

        // 获取收藏数
        $countArray = $this->favoriteCount($relativeIdArray, $type);
        $countArray && $countArray = array_column($countArray, null, 'relative_id');

        // 遍历赋值
        foreach ($array as $k => $v) {
            $array[$k]['favoriteCount'] = $countArray[$v['id']]['favoriteCount'] ?? 0;
        }   

        return $array;
    }

    /**
     * 获取商品的有效性(是否删除或下架)
     * @param array $array 收藏列表
     * @return array
     * @author 许立 2018年09月06日
     */
    public function handleProductValidity($array)
    {
        if (empty($array)) {
            return [];
        }

        // 商品列表
        $idArray = array_column($array, 'relative_id');
        $products = (new ProductService())->getListById($idArray);
        if (empty($products)) {
            return [];
        }

        // 判断有效性
        $validityArray = [];
        foreach ($products as $k => $v) {
            $validityArray[$v['id']] = $v['status'] == 1 ? Favorite::FAVORITE_VALID : Favorite::FAVORITE_INVALID;
        }
        foreach ($array as $k => $v) {
            $array[$k]['validity'] = $validityArray[$v['relative_id']] ?? Favorite::FAVORITE_INVALID;
        }

        return $array;
    }

    /**
     * 获取活动的有效性(是否实效删除或过期)
     * @param array $array 收藏列表
     * @return array
     * @author 许立 2018年09月06日
     */
    public function handleActivityValidity($array)
    {
        if (empty($array)) {
            return [];
        }

        // 活动分类
        $typeSeckill = Favorite::FAVORITE_TYPE_SECKILL;
        $typeGroup = Favorite::FAVORITE_TYPE_GROUP;
        $typeShare = Favorite::FAVORITE_TYPE_SHARE;
        $seckillIdArray = $groupIdArray = $shareIdArray = [];
        foreach ($array as $v) {
            if ($v['type'] == $typeSeckill) {
                $seckillIdArray[] = $v['relative_id'];
            } elseif ($v['type'] == $typeGroup) {
                $groupIdArray[] = $v['relative_id'];
            } else {
                $shareIdArray[] = $v['relative_id'];
            }
        }

        // 处理秒杀有效性
        $seckillValidityArray = $this->_handleValidity($seckillIdArray, $typeSeckill);

        // 处理拼团有效性
        $groupValidityArray = $this->_handleValidity($groupIdArray, $typeGroup);

        // 处理享立减有效性
        $shareValidityArray = $this->_handleValidity($shareIdArray, $typeShare);

        // 遍历判断有效性
        foreach ($array as $k => $v) {
            $validity = '';
            if ($v['type'] == $typeSeckill) {
                $validity = $seckillValidityArray[$v['relative_id']];
            } elseif ($v['type'] == $typeGroup) {
                $validity = $groupValidityArray[$v['relative_id']];
            } else {
                $validity = $shareValidityArray[$v['relative_id']];
            }
            $array[$k]['validity'] = $validity;
        }

        return $array;
    }

    /**
     * 获取秒杀活动的有效性
     * @param array $idArray 活动id
     * @return array
     * @author 许立 2018年09月06日
     */
    private function _handleValidity($idArray, $type)
    {
        $validityArray = [];
        if ($type == Favorite::FAVORITE_TYPE_SECKILL) {
            $service = new SeckillService();
        } elseif ($type == Favorite::FAVORITE_TYPE_GROUP) {
            $service = new GroupsRuleService();
        } else {
            $service = new ShareEventService();
        }
        $list = $service->getListById($idArray);
        foreach ($list as $k => $v) {
            // 判断是否失效
            $invalidCondition = false;
            if ($type == Favorite::FAVORITE_TYPE_SECKILL) {
                $invalidCondition = $v['invalidate_at'] > '0000-00-00 00:00:00' || $v['end_at'] <= date('Y-m-d H:i:s');
            } elseif ($type == Favorite::FAVORITE_TYPE_GROUP) {
                $invalidCondition = $v['status'] < 0 || $v['end_time'] <= date('Y-m-d H:i:s');
            } else {
                $invalidCondition = $v['status'] == 1 || $v['type'] == 1 || $v['end_time'] <= time();
            }

            // 赋值
            $validityArray[$v['id']] = $invalidCondition ? Favorite::FAVORITE_INVALID : Favorite::FAVORITE_VALID;
        }

        return $validityArray;
    }

    private function _handleFavoriteTitle($type, $title)
    {
        switch ($type) {
            case Favorite::FAVORITE_TYPE_SECKILL:
                $title = '【秒杀】' . $title;
                break;
            case Favorite::FAVORITE_TYPE_GROUP:
                $title = '【拼团】' . $title;
                break;
            case Favorite::FAVORITE_TYPE_SHARE:
                $title = '【享立减】' . $title;
                break;
            default:break;
        }

        return $title;
    }
}