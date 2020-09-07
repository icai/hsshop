@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/1 index.css" />
    <!--时间插件css引入-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css"/>
    <!--主要内容的css样式文件-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/2.2.1 member_modify.css" />
    <style type="text/css">
        #account,#password{
            height: 34px;
            width: 120px;
            padding: 6px 12px;
            font-size: 14px;
            border: 1px solid rgba(0,0,0,.0001);
            border-radius: 4px;
        }
        .account_ul{
            padding: 20px;
        }
        .account_ul li{
            margin-bottom: 25px;
        }
    </style>

@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理-店铺会员修改</span>
            </div>
            <div class="main_content">
                <form id="myForm" class="form-horizontal">
                    <input type="hidden" name="id" value="{{$shopData['id']}}" />
                    <div class="form-group">
                        <label for="storeName" class="col-sm-2 control-label">帐号：</label>
                        <div class="col-sm-3">
                            <input type="text" id="account" name="mphone" value="{{$userInfo['mphone']}}" disabled="disabled" />
                            <input type="text" style="visibility:hidden;width:1px">
                            <input type="button" class="btn editAccount" value="修改" style="color:blue;" data-uid="{{ $shopData['uid'] }}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="storeName" class="col-sm-2 control-label">密码：</label>
                        <div class="col-sm-3">
                            <input type="password" id="password" name="password" value="{{ substr($userInfo['password'],0,10) }}" disabled="disabled" />
                            <input type="password" style="visibility:hidden;width:1px">
                            <input type="button" class="btn editPasswd" value="修改" style="color:blue;" data-uid="{{ $shopData['uid'] }}"/>
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="storeName" class="col-sm-2 control-label">店铺名称：</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="storeName" name="shop_name" placeholder="请输入店铺名称" value="{{$shopData['shop_name']}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mainGoods" class="col-sm-2 control-label">主营商品：</label>
                        <div class="col-sm-1">
                            <select id="one" name="business_id">
                                <option value="">请选择主营商品分类</option>
                                @foreach(json_decode($categoryData,true)[0] as $val)
                                <option @if($shopData['oneCategory'] == $val['id'])selected=selected @endif value="{{$val['id']}}">{{$val['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <select id="sec" name="business_id">
                                <option value="0">子分类</option>
                                @if(isset(json_decode($categoryData,true)[$shopData['oneCategory']]))
                                @foreach(json_decode($categoryData,true)[$shopData['oneCategory']] as $val)
                                    <option @if($shopData['secCategory'] == $val['id'])selected=selected @endif value="{{$val['id']}}">{{$val['title']}}</option>
                                @endforeach
                                    @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="address" class="col-sm-2 control-label">联系地址：</label>
                        <div class="col-sm-3">
                            <div class="change-addr">
                                <select name="province_id" class="js-province">
                                    <option value="">选择省份</option>
                                    @foreach($provinceList as $p)
                                    <option value="{{ $p['id'] }}" @if($shopData['province_id'] == $p['id']) selected="selected" @endif>{{ $p['title'] }}</option>
                                    @endforeach
                                </select>
                                <select name="city_id" class="js-city">
                                    <option value="">选择城市</option>
                                    @if($cityList)
                                    @foreach($cityList as $c)
                                    <option value="{{ $c['id'] }}" @if($shopData['city_id'] == $c['id']) selected="selected" @endif>{{ $c['title'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <select name="area_id" class="js-area">
                                    <option value="">选择区县</option>
                                    @if($areaList)
                                    @foreach($areaList as $r)
                                    <option value="{{ $r['id'] }}" @if($shopData['area_id'] == $r['id']) selected="selected" @endif>{{ $r['title'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <textarea id="address" name="address" type="text" placeholder="请输入详细地址">{{$shopData['address']}}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="companyName" class="col-sm-2 control-label">公司名称：</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="companyName" name="company_name" placeholder="请输入公司名称" value="{{$shopData['company_name']}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="creatTime" class="col-sm-2 control-label">绑定小程序数量：</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="blandXcx" name="xcx_num"  value="{{$shopData['xcx_num']}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="creatTime" class="col-sm-2 control-label">创建时间：</label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control" id="creatTime" disabled value="{{$shopData['created_at']}}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">店铺分组：</label>
                        <div class="col-sm-3">
                            <select name="role_id">
                                <option value="">请选择店铺分组</option>
                                @forelse($shopData['roleData'] as $val)
                                    <option @if($shopData['shopRole']['admin_role_id'] == $val['id'])selected=selected @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                    @endforeach
                            </select>
                        </div>
                    </div>
                    @if($shopData['is_sms'])
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">关闭短信验证账号打通：</label>
                            <div class="col-sm-3">
                                <select name="is_sms">
                                    <option value="2">是否关闭</option>
                                    <option value="1">是</option>
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">费用：</label>
                        <div class="col-sm-3">
                            <select name="is_fee">
                                <option @if($shopData['is_fee']==0) selected="selected" @endif value="0">免费</option>
                                <option @if($shopData['is_fee']==1) selected="selected" @endif value="1">付费</option>
                                <option @if($shopData['is_fee']==2) selected="selected" @endif value="2">赠送</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="Btime" class="col-sm-2 control-label">开始时间：</label>
                        <div class="col-sm-3">
                            <div class="input-group" id="datetimepicker1">
                                <input type="text" name="start_time" id="Btime" class="form-control" placeholder="请选择开始时间" value="{{$shopData['shopRole']['start_time']}}">
                                <span class="input-group-addon">
					                        <span class="glyphicon glyphicon-calendar"></span>
					                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="Ctime" class="col-sm-2 control-label">结束时间：</label>
                        <div class="col-sm-3">
                            <div class="input-group" id="datetimepicker2">
                                <input type="text" name="end_time" id="Ctime" class="form-control" placeholder="请选择结束时间" value="{{$shopData['shopRole']['end_time']}}">
                                <span class="input-group-addon">
					                        <span class="glyphicon glyphicon-calendar"></span>
					                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">是否显示底部logo：</label>
                        <div class="col-sm-3">
                            <select name="is_logo_show">
                                <option @if($shopData['is_logo_show']==0) selected="selected" @endif value="0">不显示</option>
                                <option @if($shopData['is_logo_show']==1) selected="selected" @endif value="1">显示</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">店铺底部logo：</label>
                        <div class="col-sm-8">
                            <input type="radio" @if(empty($shopData['logo_type'])) checked="checked" @endif  name="logo_type" value="0">默认
                            <input type="radio" @if(!empty($shopData['logo_type'])) checked="checked" @endif  name="logo_type" value="1">自定义logo
                        </div>
                        <div class="relative upImg col-sm-8" style="@if(empty($shopData['logo_type'])) display: none; @endif">
                            <div class="imgGroup"></div>
                            <img src="@if(!empty($shopData['logo_path'])) {{ imgUrl() }}{{$shopData['logo_path']}} @else{{ imgUrl() }}staff/hsadmin/images/tjzp@2x.png @endif" id="btnUp" type="button" @if(empty($shopData['logo_path'])) width="100" height="100"@else width="330" height="90"  @endif>
                            <input id="logo_path" type="hidden" name="logo_path" class="filepath absolute" value="@if(!empty($shopData['logo_path'])){{$shopData['logo_path']}}@endif">
                            <span>建议：图片尺寸330*90</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">是否开启底部logo链接：</label>
                        <div class="col-sm-3">
                            <select name="is_logo_open">
                                <option @if($shopData['is_logo_open']==0) selected="selected" @endif value="0">不开启</option>
                                <option @if($shopData['is_logo_open']==1) selected="selected" @endif value="1">开启</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="Ctime" class="col-sm-2 control-label"></label>
                        <button id="sub" type="button" class="btn btn-lg btn-primary sure">确认提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="div_modify_account" style="display: none;" data-uid="{{ $shopData['uid'] }}">
        <div class="form-group" style="text-align:center;">
            <ul class="account_ul">
                <li>帐号：<input type="text" name="modify_phone" placeholder="修改登录手机号" value="" /><input type="text" style="visibility:hidden;width:1px"></li>
            </ul>
        </div>
    </div>
    <div class="div_modify_password" style="display: none;" data-uid="{{ $shopData['uid'] }}">
        <div class="form-group" style="text-align:center;">
            <ul class="account_ul">
                <li>
                    密码：<input type="password" name="modify_password" placeholder="请输入8-18位字母数字" value="" /><input type="password" style="visibility:hidden;width:1px">
                </li>
            </ul>
        </div>
    </div>
<script type="text/javascript">
    var imgUrl = "{{ imgUrl() }}";
    var categoryData ={!! $categoryData !!};
    var is_logo_open = {{$shopData['is_logo_open']}}
    console.log(categoryData);
</script>
@endsection
@section('foot.js')
    <!--时间插件引入的JS文件-->
    <script src="{{ config('app.source_url') }}staff/static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}static//js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
    <!--less文件引入-->
    <!--<script src="public/static/js/less.js" type="text/javascript" charset="utf-8"></script>-->
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/2.2.1 member_modify.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        //省市区地址
        var json = {!! $regions !!};
    </script>
@endsection