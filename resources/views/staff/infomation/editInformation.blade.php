@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.1 addNews.css" />    
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <input id="source" type="hidden" value="{{ config('app.source_url') }}staff/hsadmin" />
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>资讯管理-添加资讯</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">添加资讯</a>
                </div>
                <div class="addNews_list">
                    <form id="myForm" action="/staff/addInformation" method="post">

                        <input id="edit_id" type="hidden" name="id" value="@if(!empty($informationData)){{$informationData['id']}}@endif">

                    <!--资讯分类-->
                    <div class="news_classify flex_star">
                        <div class='input-group col-sm-3'>
		                    <span class="input-group-addon">
		                        <span>请选择分类</span>
		                    </span>
                            <select id="one" name="oneCategory" class="form-control">
                                <option value="">一级分类</option>
                                @foreach(json_decode($categoryData,true)[0] as $val)
                                    <option @if(!empty($informationData) && $informationData['path'][0] == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class='input-group col-sm-2'>
                            <select id="sec" name="secCategory" class="form-control">
                                <option value="">二级分类</option>
                                @if(!empty($informationData))
                                    @foreach(json_decode($categoryData,true)[$informationData['path'][0]] as $val)
                                        <option @if($informationData['path'][1] == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class='input-group col-sm-2'>
                            <select id="three" name="infoType" class="form-control">
                                <option value="">三级分类</option>
                                @if(!empty($informationData))
                                    @foreach(json_decode($categoryData,true)[$informationData['path'][1]] as $val)
                                        <option @if($informationData['path'][2] == $val['id'])selected='selected'@endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <hr />
                    <!--标题照片详情-->
                    <div class="news_detail">
                        <div class="inpGroup">
                            <label for="title" class="inpName">标题：</label>
                            <input type="text" name="title" id="title" value="@if(!empty($informationData)){{$informationData['title']}}@endif" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">副标题：</label>
                            <input type="text" name="subTitle" id="subtitle" value="@if(!empty($informationData)){{$informationData['sub_title']}}@endif" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">作者：</label>
                            <input type="text" name="auth" id="auth" value="@if(!empty($informationData)){{$informationData['auth']}}@endif" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">seo关键字：</label>
                            <input type="text" name="keywords" id="keywords" value="@if(!empty($informationData)){{$informationData['keywords']}}@endif" />
                        </div>
                        <div class="inpGroup">
                            <label for="subtitle" class="inpName">seo描述：</label>
                            <textarea name="meta" rows="7" cols="50">@if(!empty($informationData)){{$informationData['meta']}}@endif</textarea>
                        </div>
                        <div class="inpGroup">
                        	<label for="subtitle" class="inpName">推送设置：</label>
                        	<select class="form-control pushsel" name="">
                        		<option value="">全站新闻</option>
                        		<option value="">推荐内容</option>
                        		<option value="">广告展示位</option>
                        	</select>
                        </div>
                        <div class="imgDiv flex_star">
                            <div class="relative upImg">
                            	<div class="imgGroup">
	                                @if(!empty($informationData['source']))
	                                    @foreach($informationData['source'] as $val)
                                            <div class="img_item">
                                                <img class="littleImg" src="{{ imgUrl($val['path']) }}" width="100" height="100"/>
                                                <img class="delImg" data-id="{{$val['id']}}" src="{{ config('app.source_url') }}staff/hsadmin/images/guanbi@2x.png"/>
                                            </div>
	                                        @endforeach
	                                @endif
                            	</div>
                                <img src="{{ config('app.source_url') }}staff/hsadmin/images/tjzp@2x.png" id="btnUp" type="button"  width="100" height="100"/>
                                <input id="attachment" type="hidden" name="attachment" class="filepath absolute" value="@if(!empty($informationData)){{$informationData['attachment']}}@endif" />
                                <span class="hint">最多上传六张图片，大小建议：492×384</span>
                            </div>
                        </div>
                        <div class="inpGroup">
                            <label for="detail" class="inpName">详情：</label>
                            <div id="editor" name="content" type="text/plain" style="width:calc(100% - 60px);height:300px;margin-left: 60px;"></div>
                        </div>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input id="status" type="hidden" name="status" value="1" />
                    </div>
                            <div class="btn_group">
                                <button id="sub" type="button" class="btn btn-primary sure">确认提交</button>
                                <button id="sub1" type="button" class="btn btn-primary sure">提交预览</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
        <div id="content" style="display: none">@if(!empty($informationData)){!! $informationData['content'] !!} @endif</div>
        <script type="text/javascript">
            var categoryData ={!! $categoryData !!};
            @if(!empty($informationData))
                var str = '{!! $informationData['attachment'] !!}';
            @else
                var str = '';
            @endif
            if (str != ''){
                var attachment = str.split(',');
            }else {
                var attachment = new Array();
            }
        </script>
@endsection
@section('foot.js')
    <script type="text/javascript">
        var json = '{!! $categoryData !!}';
        json = JSON.parse(json)[0];
    </script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}staff/static/js/UE/UEditor/ueditor.config.js?t=123"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}staff/static/js/UE/UEditor/ueditor.all.js"> </script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}staff/static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/addNews.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/upImage.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
@endsection