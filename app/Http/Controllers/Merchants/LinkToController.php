<?php

namespace App\Http\Controllers\Merchants;

use App\Http\Controllers\Controller;
use App\Services\Foundation\LinkToService;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;

/**
 * 链接到
 * 
 * @author 黄东 406764368@qq.com
 * @version 2017年3月24日 13:45:26
 */
class LinkToController extends Controller {
    /**
     * 获取链接到数据
     * 
     * @param  Request       $request       [http请求类对象]
     * @param  LinkToService $linkToService [链接到 服务类对象]
     * @return json
     */
    public function get( LinkToService $linkToService ) {
        $list = $linkToService->getDatas();
        if ( isset($list[1]) && ( $list[1] instanceof HtmlString ) ) {
            $list[1] = $list[1]->toHtml();
        }

        success('', '', $list);
    }

    /**
     * 解析数据获取url
     * 
     * @return string [解析出来的url]
     */
    public function url( LinkToService $linkToService ) {
        dd($linkToService->parseUrl());
        return $linkToService->parseUrl();
    }
}
