<?php
/**
 * Created by zhangyh.
 * User: zhangyh
 * Date: 2017/3/31
 * Time: 15:05
 */

namespace App\Services;


use App\Model\ProductEvaluate;
use DB;

class ProductEvaluateService extends Service
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
		$this->with = $this->withAll = ['member','product','order'];

	}
	public function init($uniqueKey = '', $uniqueValue = '', $idKey = 'id')
	{
		$this->initialize(new ProductEvaluate(), $uniqueKey, $uniqueValue, $idKey);
		return $this;
	}


    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 根据分类获取评论数量
     * @desc
     */
	public function getCountByClassify($pid)
    {
        $sql = 'SELECT COUNT(*) AS  num FROM ds_product_evaluate WHERE pid='.$pid.' and deleted_at IS NULL';
        list($res) = DB::select($sql);
        $result[0] = [
            'name'  => '全部',
            'num'   => $res->num,
        ];
        $sql = 'SELECT classify_name AS classify,COUNT(*)  AS num FROM ds_product_evaluate_classify WHERE pid='.$pid.' GROUP BY classify_name';
        $res = DB::select($sql);
        if ($res){
            foreach ($res as $val){
                if ($val->classify){
                    $tmp['name'] = $val->classify;
                    $tmp['num'] = $val->num;
                    $result[] = $tmp;
                }
            }
        }
        return $result;
    }

    /**
     * @author zhangyh
     * @Email zhangyh_private@foxmail.com
     * @date 20171012
     * @desc 根据pid获取评论数量
     * @param $pid
     */
    public function getEvaluateNumByPid($pid)
    {
        $sql = 'SELECT COUNT(*) AS  num FROM ds_product_evaluate WHERE pid='.$pid.' and deleted_at IS NULL';
        list($res) = DB::select($sql);
        return $res->num;
    }




}