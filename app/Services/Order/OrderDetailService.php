<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/31
 * Time: 17:36
 */

namespace App\Services\Order;



use App\Model\OrderDetail;
use App\Services\Service;
use DB;

class OrderDetailService extends Service
{
	/**
	 * 构造方法

	 * @return void
	 */
	public function __construct() {
		// http请求类
		$this->request = app('request');
		$this->with = $this->withAll = ['order'];
	}

	/**
	 * 初始化 设置唯一标识和redis键名
	 *
	 * @return this
	 */
	public function init( $uniqueKey = '', $uniqueValue = '', $idKey = 'id' ) {

		$this->initialize(new OrderDetail(), $uniqueKey, $uniqueValue, $idKey);
		return $this;
	}

	/**
     * 获取一个用户某商品的购买数量
     */
    public function productBuyNum($mid, $productID)
    {
        $num = DB::table('order as o')
            ->leftJoin('order_detail as d','d.oid','=','o.id')
            ->where('o.status', '<', 4)
            ->where('o.mid', $mid)
            ->where('d.product_id', $productID)
            ->sum('d.num');

        return intval($num);
    }

	public function first($wheres = [], $columns = ['*'])
	{
		return OrderDetail::where($wheres)->first($columns);
	}

}
