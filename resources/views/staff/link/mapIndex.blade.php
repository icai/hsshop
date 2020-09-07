@extends('staff.base.head') @section('head.css')
<link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/7 bannerlist.css" /> @endsection @section('slidebar') @include('staff.base.slidebar'); @endsection @section('content')
<div class="main">
    <div class="content">
        <div class="content_top">
            <button type="button" class="btn btn-primary">当前位置</button>
            <span>网站管理-网站地图</span>
            <a href="/staff/link/save" type="submit" class="btn btn-primary btn-right">新建</a>
        </div>
        <div class="main_content">
            <ul class="sheet table_title flex-between">
                <li>
                    <label>
                        <input type="checkbox" name="" class="allSel" />
                    </label>
                </li>
                <li>功能标题</li>
                <li>链接网址</li>
                <li>排序</li>
                <li>时间</li>
                <li class="fun">操作</li>
            </ul>
            <ul class="sheet table_body  flex-between">
                <li class="fun">
                    <label>
                        <input type="checkbox" name='' value="" />
                    </label>
                </li>
                <li>APP定制</li>
                <li>www.tallkindata.com</li>
                <li>1</li>
                <li>2017-08-15 15:00:15</li>
                <li class="fun">
                    <a href="##" class="edit">修改</a>
                    <a href="##" class="see">查看</a>
                    <a href="##" class="del">删除</a>
                    <a href="#" class="copy">复制链接</a>
                </li>
            </ul>
            <div class="main_bottom flex_end">
                <ul class="pagination">
                    <li class="disabled"><span>«</span></li>
                    <li class="active"><span>1</span></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=2">2</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=3">3</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=4">4</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=5">5</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=6">6</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=7">7</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=8">8</a></li>
                    <li><a href="http://192.168.0.200/staff/customer/reserveManage?page=2" rel="next">»</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection @section('foot.js')
<script src="{{ config('app.source_url') }}staff/hsadmin/js/7 bannerlist.js" type="text/javascript" charset="utf-8"></script>
@endsection