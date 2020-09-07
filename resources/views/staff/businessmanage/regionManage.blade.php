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

            <div class="sorts">
                <form class="form-inline" method="get" action="/staff/BusinessManage/regionManage">
                    <div class='input-group col-sm-2'>
		                    <span class="input-group-addon">
		                        <span>地区</span>
		                    </span>
                        <input type='text' name="title" class="form-control" placeholder="地区" value="{{request('title')}}" />
                    </div>

                    <button type="submit" class="btn btn-primary">搜索</button>
                    <button type="reset" class="btn btn-primary">重置</button>
                </form>
            </div>

            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">地区</a>
                    <span class="verLine">|</span>
                    <a href="##" class="addNewClassify"  data-toggle="modal" data-target="#myModal">新增地区</a>
                </div>
                <ul class="table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>区</li>
                    <li>市</li>
                    <li>省</li>
                    <li>操作</li>
                </ul>
                @forelse($data['0']['data'] as $val)
                    <ul class="table_body  flex-between">
                        <li><input type="checkbox" name='' value="" /></li>
                        <li>{{$val['title']}}</li>
                        <li>{{$val['pname']}}</li>
                        <li>{{$val['gpname']}}</li>
                        <li>
                            <a href="##"  id="{{$val['id']}}" data-id="{{$val['id']}}" data-status="{{$val['status']}}" class="hide_region">@if($val['status'] == -2) 显示 @else 隐藏@endif</a>

                        </li>
                    </ul>
                    @endforeach

                <div class="main_bottom flex_end">
                    {{$data['1']}}
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
                                <label class="col-sm-4 control-label">省：</label>
                                <div class="col-sm-6">
                                    <select name="gid" class="firstClass js-province">
                                        <option value="">请选择省</option>
                                        @forelse($regionData[-1] as $val)
                                        <option value="{{$val['id']}}">{{$val['title']}}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 control-label">市：</label>
                                <div class="col-sm-6">
                                    <select name="pid" class="firstClass js-city">

                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="className" class="col-sm-4 control-label">区：</label>
                                <div class="col-sm-6">
                                    <input name="title" type="text" class="form-control classify" id="className" placeholder="请填写区名" value="">
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
<script>
    var json = {!! json_encode($regionData) !!};
</script>
    <!-- 主体 结束 -->
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}/staff/hsadmin/js/2.1 store_management2.js" type="text/javascript" charset="utf-8"></script>
@endsection