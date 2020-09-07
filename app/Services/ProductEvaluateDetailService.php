<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/31
 * Time: 15:05
 */

namespace App\Services;

use App\Model\ProductEvaluateDetail;

class ProductEvaluateDetailService extends Service
{
	/**
	 *
	 *  constructor.
	 * @desc 构造方法
	 */
	public function __construct()
	{
		/* http请求类 */
		$this->request = app('request');

	}
	public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
	{
		$this->with = $this->withAll = ['member','reply'];
		$this->initialize(new ProductEvaluateDetail(), $uniqueKey, $uniqueValue, $idKey);
		return $this;
	}

}