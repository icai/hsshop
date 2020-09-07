<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * StatisticsController constructor.
     */
    public function __construct()
    {
        $this->leftNav = 'statistics';
    }

    /**
     * 页面转换数据
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function shopAnalysis() {
        return view('merchants.statistics.shops', array(
            'title'    => '数据分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'shops',
            'wid'      => session('wid')
        ));
    }


     /**
	 * 店铺分析---页面转化数据
	 * @return [type] [description]
	 */
    public function dailyData() {
        return view('merchants.statistics.dailyData', array(
            'title'    => '数据分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'shops',
            'wid'      => session('wid')
        ));
    }

    /**
     * Desc:数据概括
     * Author: 梅杰 20180627
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index() {
        return view('merchants.statistics.index', array(
            'title'    => '数据概况',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'index',
            'wid'      => session('wid')
        ));
    }

    /**
     * 页面流量
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: 梅杰 2018年6月27日
     */
    public function pageData(){
        return view('merchants.statistics.page_data',array(
            'title'    => '页面流量',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'pagedata',
            'wid'      => session('wid')
        ));
    }


    /**
     * 客户分析
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function customer(){
        return view('merchants.statistics.customer',array(
            'title'    => '客户分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'customer',
            'wid'      => session('wid')
        ));
    }

    /**
     * 粉丝分析
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function fans()
    {
        return view('merchants.statistics.fans',array(
            'title'    => '粉丝分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'fans',
            'wid'      => session('wid')
        ));
    }


    /**
     * 粉丝分层
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function fansLayering()
    {
        return view('merchants.statistics.fansLayering',array(
            'title'    => '粉丝分层',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'fansLayering',
            'wid'      => session('wid')
        ));
    }

    /**
     * 粉丝信息
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function fansInfo()
    {
        return view('merchants.statistics.fansInfo',array(
            'title'    => '粉丝分层',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'fansInfo',
            'wid'      => session('wid')
        ));
    }

    /**
     * 粉丝互动
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function fansInteract()
    {
        return view('merchants.statistics.fansInteract',array(
            'title'    => '粉丝分层',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'fansInteract',
            'wid'      => session('wid')
        ));
    }

    /**
     * 按每天流量分析
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function daysTraffic(){
    	return view('merchants.statistics.days_traffic',array(
            'title'    => '按每天流量分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'pagedata',
            'wid'      => session('wid')
        ));
    }

    /**
     * 商品分析
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function goods(){
        return view('merchants.statistics.goods',array(
            'title'    => '商品分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'goods',
            'wid'      => session('wid')
        ));
    }

    /**
     * 交易分析
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function transaction(){
        return view('merchants.statistics.transaction',array(
            'title'    => '交易分析',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'transaction',
            'wid'      => session('wid')
        ));
    }

    /**
     * 交易分析导出
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function export()
    {
        return view('merchants.statistics.export',array(
            'title'    => '交易统计导出',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'export',
        ));
    }




    /**
     * 卡券统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View 视图
     * @author: 梅杰 2018年6月27日
     */
    public function coupons(){
        return view('merchants.statistics.coupons',array(
            'title'    => '卡券统计',
            'leftNav'  => $this->leftNav,
            'slidebar' => 'coupons',
            'wid'      => session('wid')
        ));
    }
}
