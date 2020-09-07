@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}/staff/hsadmin/css/2.1 store_management.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-分类管理</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">分类列表</a>
                    <span class="verLine">|</span>
                    <a href="##" class="addNewClassify"  data-toggle="modal" data-target="#myModal">新增分类</a>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>分类</li>
                    <li>二级分类</li>
                    <li>店铺数/已忽略店铺数</li>
                    <li>排序</li>
                    <li>操作</li>
                </ul>
                @forelse($weixinBusinessData['0']['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$val['p_name']}}</li>
                        <li>{{$val['title']}}</li>
                        <li><a href="{{url('/staff/getShop?category=' . $val['id'])}}">{{$val['shopCount'] .'/'.$val['ignoreCount']}}</a></li>
                        <li>{{$val['sort']}}</li>
                        <li>
                            <a href="##"  id="{{$val['id']}}" class="modify">修改</a>
                            <a href="##" id="{{$val['id']}}" class="del">删除</a>
                        </li>
                    </ul>
                    @endforeach

                <div class="main_bottom flex_end">
                    {{$weixinBusinessData['1']}}
                </div>
            </div>
        </div>
        <!--新增弹出狂-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog bs-example-modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">新增分类</h4>
                    </div>
                    <div class="modal-body">
                        <form id="add" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-4 control-label">选择分类：</label>
                                <div class="col-sm-6">
                                    <select name="tag" class="firstClass">
                                        <option value="2">一级分类</option>
                                        <option value="1">二级分类</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">选择所属一级分类：</label>
                                <div class="col-sm-6">
                                    <select name="pid" id="secondClass">
                                        @forelse($pCategory as $val)
                                            <option value="{{$val['id']}}">{{$val['title']}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="className" class="col-sm-4 control-label">填写分类名称：</label>
                                <div class="col-sm-6">
                                    <input name="title" type="text" class="form-control classify" id="className" placeholder="请输入分类名称" value="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="className" class="col-sm-4 control-label">填写分类排序值：</label>
                                <div class="col-sm-6">
                                    <input name="sort" type="text" class="form-control classify" id="className" placeholder="请输入排序值" value="0">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sub">提交</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
        <!--修改弹出狂-->
        <div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog bs-example-modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">修改分类</h4>
                    </div>
                    <div id="modify" class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary sub">提交</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 主体 结束 -->
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}/staff/hsadmin/js/2.1 store_management.js" type="text/javascript" charset="utf-8"></script>
@endsection