@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.2 news_list.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>资讯管理-资讯列表</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <form id="myForm" class="form-inline">
                        <div class='input-group col-sm-2'>
				                    <span class="input-group-addon">
				                        <span>请选择分类</span>
				                    </span>
                            <select id="one" name="oneCategory" class="form-control">
                                <option value="">一级分类</option>
                                @foreach(json_decode($categoryData,true)[0] as $val)
                                <option @if(request('oneCategory') == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                            </select>
                        </div>
                        <div class='input-group col-sm-1'>
                            <select id="sec" name="secCategory" class="form-control">
                                <option value="">二级分类</option>
                                <?php $oneId = request('oneCategory')?>
                                @if(!empty(request('oneCategory')) && isset(json_decode($categoryData,true)[$oneId]))
                                    @foreach(json_decode($categoryData,true)[$oneId] as $val)
                                        <option @if(request('secCategory') == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class='input-group col-sm-1'>
                            <select id="three" name="threeCategory" class="form-control">
                                <option value="">三级分类</option>
                                <?php $secId = request('secCategory')?>
                                @if(!empty(request('secCategory')) && isset(json_decode($categoryData,true)[$secId]))
                                    @foreach(json_decode($categoryData,true)[$secId] as $val)
                                        <option @if(request('threeCategory') == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class='input-group col-sm-1'>
                            <select  name="status" class="form-control">
                                <option value="">资讯状态</option>
                                    <option @if(request('status') == '0') selected @endif value="0">全部</option>
                                    <option @if(request('status') == '1') selected @endif value="1">已发布</option>
                                    <option @if(request('status') == '2') selected @endif value="2">草稿箱</option>
                                </select>
                            </select>
                        </div>
                        <div class='input-group col-sm-2'>
				                    <span class="input-group-addon">
				                        <span>请输入ID</span>
				                    </span>
                            <input type='text' name="id" class="form-control" placeholder="请输入ID" value="{{request('id')}}" />
                        </div>
                        <div class='input-group col-sm-2'>
                            <input type='text' name="keywords" class="form-control" placeholder="请输入关键词" value="{{request('keywords')}}" />
                        </div>
                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button id="reset" type="reset" class="btn btn-primary">重置</button>
                    </form>
                </div>
                <ul class="sheet table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />选择</label>
                    </li>
                    <li>ID</li>
                    <li>标题</li>
                    <li>资讯分类</li>
                    <li>发布人</li>
                    <li>排序</li>
                    <li>状态</li>
                    <li>发布时间</li>
                    <li class="fun">操作</li>
                </ul>
                @foreach($infoData[0]['data'] as $val)
                <ul class="sheet table_body  flex-between">
                    <li class="fun"><label><input type="checkbox" name='' value="" /></label></li>
                    <li class="sheet-li-id">{{$val['id']}}</li>
                    <li class="sheet-li-title">{{$val['title']}}</li>
                    <li>{{$val['type_path'] or ''}}</li>
                    <li>{{$val['account']['name']}}</li>
                    <li class="sheet-li">{{ $val['sort'] }}</li>
                    <li>@if($val['status'] == 1)已发布@elseif($val['status'] == 2) 草稿@endif</li>
                    <li>{{$val['created_at']}}</li>
                    <li class="fun">
                        <a href="/staff/editInformation?id={{$val['id']}}" class="modify1">修改</a>
                        <a href="##" id="{{$val['id']}}" data-id="{{$val['id']}}" class="recommend" data-toggle="modal" data-target="#myModal">推送</a>
                        <!--推送弹框-->
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog">
                        	<div class="modal-dialog" role="document">
                        		<div class="modal-content">
                        			<div class="modal-header">
                        				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        				<h4 class="modal-title">Modal title</h4>
                        			</div>
                        			<div class="modal-body">
                        				<label class="modal-lab">
                        					推送位置：
                        					<select class="modal-sel heig30">
                        						<option>全站新闻</option>
                        						<option>推荐内容页</option>
                        						<option>广告展示位</option>
                        					</select>                        					
                        				</label>
                        				<label class="modal-lab">
                        					填写分类名称：
                        					<input class="modal-sel heig30" type="" name="" id="" value="" />                        					
                        				</label>
                        			</div>
                        			<div class="modal-footer">
                        				<button type="button" class="btn btn-primary">提交</button>
                        				<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        			</div>
                        		</div>
                        	</div>
                        </div>
                        <a href="##" id="{{$val['id']}}" class="del">删除</a>
                    </li>
                </ul>
                @endforeach
                <div class="flex-left">
                	<a class="btn btn-primary">删除</a>
                	<a class="btn btn-primary">启用</a>
                	<a class="btn btn-primary">禁用</a>
                </div>
                <div class="main_bottom flex_end">
                    {{$infoData[1]}}
                </div>
				
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var categoryData ={!! $categoryData !!};
        console.log(categoryData);
    </script>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/3.2 news_list.js" type="text/javascript" charset="utf-8"></script>
@endsection