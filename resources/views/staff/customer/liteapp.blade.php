@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/liteapp.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>小程序查重-小程序数据库</span>
                <span><a href="/staff/customer/exportLiteapp" target="_blank">导出全部</a></span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <form id="myForm" class="form-inline">
                        <div class="nav">
                            <div class="item co_000">小程序列表</div>
                            <div class="line"></div>
                            <div class="item" data-toggle="modal" data-target="#addXCX_model">新增小程序</div>
                            <div class="line"></div>
                            <div class="item addHistory">新增案例轮播</div>
                        </div>
                        <div class='input-group col-sm-2'>
                            <input type='text' name="title" class="form-control search" placeholder="请输入小程序名称" value="{{request('title')}}" />
                        </div>
                        <button type="submit" class="btn btn-primary">搜索</button>
                    </form>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>小程序名称</li>
                    <li>操作</li>
                </ul>
                <form class="listForm">
                    @forelse($data[0]['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='ids[]' value="{{$val['id']}} " /></li>
                        <li>{{$val['title']}}</li>
                        <li data-id="{{$val['id']}}">
                            <a href="##" class="del">删除</a>
                        </li>
                    </ul>
                    @endforeach
                </form>
                <div class="btn_group">
                    <a class="addDelete btn btn-danger" href="javascript:void(0);">批量删除</a>
                </div>
                <div class="main_bottom flex_end">
                   {{$data[1]}}
                </div>
            </div>
        </div>
    </div>
    <!-- 模态框（Modal） -->
    <div class="modal" id="addXCX_model" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">
                        新增小程序
                    </h4>
                </div>
                <div class="modal-body">
                    <textarea class="xcx_add"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">
                        提交
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        重置
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- 新增案例轮播 -->
    <div class="modal" id="addData" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">
                        新增案例轮播
                    </h4>
                </div>
                <div class="modal-body">
                    <ul class="dataList">
                        <li class="item">
                            <div class="itemLeft">
                                <span>用户：</span>
                                <input class="prev" type="number" oninput="if(value.length>3)value=value.slice(0,3)">
                                <span>****</span>
                                <input class="next" type="number" oninput="if(value.length>4)value=value.slice(0,4)">
                            </div>
                            <div class="itemRight">
                                <span>小程序：</span>
                                <input class="title" type="text" maxlength="4">
                                <span>****</span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="itemLeft">
                                <span>用户：</span>
                                <input class="prev" type="number" oninput="if(value.length>3)value=value.slice(0,3)">
                                <span>****</span>
                                <input class="next" type="number" oninput="if(value.length>4)value=value.slice(0,4)">
                            </div>
                            <div class="itemRight">
                                <span>小程序：</span>
                                <input class="title" type="text" maxlength="4">
                                <span>****</span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="itemLeft">
                                <span>用户：</span>
                                <input class="prev" type="number" oninput="if(value.length>3)value=value.slice(0,3)">
                                <span>****</span>
                                <input class="next" type="number" oninput="if(value.length>4)value=value.slice(0,4)">
                            </div>
                            <div class="itemRight">
                                <span>小程序：</span>
                                <input class="title" type="text" maxlength="4">
                                <span>****</span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="itemLeft">
                                <span>用户：</span>
                                <input class="prev" type="number" oninput="if(value.length>3)value=value.slice(0,3)">
                                <span>****</span>
                                <input class="next" type="number" oninput="if(value.length>4)value=value.slice(0,4)">
                            </div>
                            <div class="itemRight">
                                <span>小程序：</span>
                                <input class="title" type="text" maxlength="4">
                                <span>****</span>
                            </div>
                        </li>
                    </ul>
                    <div class="add">新增</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">
                        提交
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        重置
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/5.2.1 admin_type.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
    <script src="{{ config('app.source_url') }}static/js/bootstrap.min.js"></script>
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/liteapp.js" type="text/javascript" charset="utf-8"></script>
@endsection