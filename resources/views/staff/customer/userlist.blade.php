@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/1 index.css" />
    <!--时间插件css引入-->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}staff/static/css/bootstrap-datetimepicker.min.css"/>
    <!--主要内容的css样式文件-->
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/2.2 store_member.css" />

@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>店铺管理</span>
                <a href="/staff/registerUser" type="button" class="btn btn-primary" style="margin-left:10px;">注册会员</a>
            </div>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <h4>不能跳转提示：</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li><i class="glyphicon glyphicon-remove"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="main_content">
                <!-- <div class="sorts">
                    <a href="/staff/userlist">帐号列表</a>
                    <span class="verLine">|</span>
                    <a href="/staff/registerUser" style="color: #333;">新增用户信息</a>
                </div> -->

            <div class="main_content">
                <div class="sorts">
                    <form class="form-inline" method="get" action="/staff/userlist">
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
				                <span>企业会员</span>
				            </span>
                            <input type='text' name="name" class="form-control" placeholder="名字" value="{{request('name')}}" />
                        </div>
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
                                <span>电话</span>
                            </span>
                            <input type='text' name="mphone" class="form-control" placeholder="电话" value="{{request('mphone')}}" />
                        </div>
                        <button type="submit" class="btn btn-primary">搜索</button>
                    </form>
                </div>
                <ul class="sheet table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>企业会员</li>
                    <li>注册手机号</li>
                    <li>商品数</li>
                    <li>会员数</li>
                    <li>销售额</li>
                    <li>店铺数量</li>
                    @if(isset($_GET['orderby']) && $_GET['orderby'] =='logins' && $_GET['order'] == 'desc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(0,0)">登录次数 ↓</a></li>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='logins' && $_GET['order'] == 'asc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(0,1)">登录次数 ↑</a></li>
                    @else
                        <li><a href="javascript:void(0);" onclick="sort_desc(0,0)">登录次数</a></li>
                    @endif
                    <li>创建时间</li>
                    <li>操作</li>
                </ul>
                @forelse($shopData as $val)
                <ul class="sheet table_body  flex-between">
                    <li><label><input type="checkbox" name='' value="" />{{$val['id']}}</label></li>
                    <li>{{$val['name']}}</li>
                    <li>{{$val['mphone']}}</li>
                    <li>{{ $val['productCountTotal'] }}</li>
                    <li>{{ $val['memberCountTotal'] }}</li>
                    <li>{{ $val['SaleCountTotal'] }}</li>
                    <li>{{ $val['shopCountTotal'] }}</li>
                    <li>{{ $val['logins'] }}</li>
                    <li>{{$val['created_at']}}</li>
                    <li style="display:block">
                        | <a href="/staff/getShop/{{ $val['id'] }}" data-id="">查看店铺</a> |
                        <a href="javascript:;" data-account="{{$val['mphone'] or ''}}" class="relieve">解除登录</a> |
                    </li>

                </ul>
                @endforeach
                <div class="main_bottom flex_end">
                    {!! $pageHtml !!}
                </div>

                <!--修改弹出狂-->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">

                </div>
            </div>
        </div>
    </div>

@endsection
@section('foot.js')
    <!--时间插件引入的JS文件-->
    <script src="{{ config('app.source_url') }}staff/static/js/moment.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/static/js/locales.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="{{ config('app.source_url') }}staff/static/js/bootstrap-datetimepicker.min.js" type="text/javascript" charset="utf-8"></script>
    <!--less文件引入-->
    <!--<script src="public/static/js/less.js" type="text/javascript" charset="utf-8"></script>-->
    <!--主要内容的JS-->
    <script src="{{ config('app.source_url') }}staff/hsadmin/js/2.2 store_member.js" type="text/javascript" charset="utf-8"></script>
    <script>
        //点击排序
        var SORT = [0,0,0,0];
        var ORDER_BY = ['logins'];
        var ORDER = ['asc','desc'];
        var FLAG = ['↑','↓'];
        var NAME = ['登录次数'];
        function sort_desc(index,sort){
            var params = getallparam({order:ORDER[sort],orderby:ORDER_BY[index]});
            window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
        }

        /**
         * 获取所有url上的参数
         * 修改 并返回 对应 url的参数值
         */
        function getallparam(obj){
            var sPageURL = window.location.search.substring(1);
            var sURLVariables = sPageURL.split('&');
            var flag = 0;
            for(var i = 0; i< sURLVariables.length; i++){
                var sParameterName = sURLVariables[i].split('=');
                if (undefined != obj[sParameterName[0]]){
                    sParameterName[1] = obj[sParameterName[0]];
                    flag++;
                }
                sURLVariables[i] = sParameterName.join('=');
            }
            var newquery = sURLVariables.join('&');
            for(var key in obj){
                if(-1 === newquery.indexOf(key)){
                    newquery += '&'+key+'='+obj[key];
                }
            }
            return newquery;
        }
    </script>
@endsection