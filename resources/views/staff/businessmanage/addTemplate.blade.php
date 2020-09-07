@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/5.2.1 admin_type.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <style type="text/css">
        .t-pr{position: relative;}
        .t-pagination{position: absolute;right: 0;top: 0;margin-top: 0;border-bottom: none !important;}
        .main{margin-top:160px;}
        .t-tips{padding: 5px;display:inline-block;}
        /*2017-4-27  田小文*/
        /**
         * 粒度小的样式定义
         * 作用通用 统一前缀 t-
         */
        /*---定位---*/
        .t-pr{position:relative;}
        .t-t0{top:0;}
        .t-b0{bottom:0;}
        .t-r0{right:0;}
        .t-l0{left:0;}
        /*---尺寸---*/
        .t-w-50{width:50px !important;}
        .t-w-60{width:60px !important;}
        .t-w-70{width:70px !important;}
        .t-w-80{width:80px !important;}
        .t-w-90{width:90px !important;}
        .t-w-100{width:100px !important;}
        .t-w-150{width:150px !important;}
        .t-w-200{width:200px !important;}
        /*---隐藏---*/
        .t-none{display: none;}
        /*---边框---*/
        .t-br0{border-radius: 0;}
    </style>
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-默认模板设置</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="/staff/getTemplate">模板列表</a>
                    <span class="verLine">|</span>
                    <a href="javascript:;" style="color: #333;">新增默认店铺模板</a>
                </div>
                <br>
                <form class="form-horizontal" id="myForm">
                    @if(!empty($template))
                        <input type="hidden" name="id" value="{{$template['id']}}" />
                    @endif
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">模板名称：</label>
                        <div class="input-group col-sm-8">
                            <input type="text" class="form-control t-w-200" id="" name="title" placeholder="" value=" @if(!empty($template)){{$template['title']}} @endif">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2  control-label">店铺ID：</label>
                        <div class="input-group col-sm-8">
                            <input type="number" class="form-control  t-w-200" id="" name="qrcode" placeholder="" value="@if(!empty($template)){{$template['qrcode']}}@endif">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">排序：</label>
                        <div class="input-group col-sm-8">
                            <input type="number" class="form-control t-w-100 t-br0" id="" name="sort" placeholder="" value="@if(!empty($template)){{$template['sort']}}@endif">
                            <span class="t-tips">(请输入1~99数值，数值约大，排序越靠前)</span>
                        </div>
                    </div> 
                    <div class="form-group imgDiv flex_star">
                        <label for="thirdClassify" class="col-sm-2  control-label">图片：</label>
                        <div class="relative upImg col-sm-8">
                            <div class="imgGroup"></div> 
                            <img src="@if(!empty($template)){{$template['img']}}@else{{ config('app.source_url') }}staff/hsadmin/images/tjzp@2x.png @endif" id="btnUp" type="button" width="100" height="100" style="margin-left:-15px;">
                            <input id="img" type="hidden" name="img" class="filepath absolute" value="@if(!empty($template)){{$template['img']}}@endif">
                        </div> 
                    </div>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">模板描述：</label>
                        <div class="input-group col-sm-8">
                            <textarea name="desc" class="form-control" rows="3">@if(!empty($template)){{$template['desc']}} @endif</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="thirdClassify" class="col-sm-2 control-label">是否开启：</label>
                        <div class="input-group col-sm-8">
                            <input type="radio" checked="checked" name="status" value="1">开启
                            <input type="radio" name="status" value="0">暂不开启
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button id="sub" type="button" class="btn btn-primary">确定</button>
                            <button id="clear" type="button" class="btn btn-default">取消</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        var _host = '{{ config('app.source_url') }}';
        var imgUrl ="{{ imgUrl() }}";
    </script>
@endsection
@section('foot.js')
    <script src="{{ config('app.source_url') }}static//js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/add_template.js" type="text/javascript" charset="utf-8"></script>
@endsection