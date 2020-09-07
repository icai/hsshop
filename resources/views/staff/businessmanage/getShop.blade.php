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
                <span>店铺管理-店铺会员</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <form id="shop_form" class="form-inline" method="get" action="">
                        <div class='input-group col-sm-2'>
                                <span class="input-group-addon">
                                    <span>主营产品</span>
                                </span>
                                <select name="category" class="form-control">
                                    <option value="">请选择行业名称</option>
                                    @forelse($category as $cate)
                                    <option value="{{ $cate['id'] }}" @if(request('category') == $cate['id']) selected='selected' @endif>{{ $cate['title'] }}</option>
                                    @empty
                                    @endforelse
                                </select>
                        </div>
                        <div class='input-group col-sm-2'>
				                    <span class="input-group-addon">
				                        <span>店铺名称</span>
				                    </span>
                            <input type='text' name="shopName" class="form-control" placeholder="请输入店铺名称" value="{{request('shopName')}}" />
                        </div>
                        <div class='input-group col-sm-2'>
				                    <span class="input-group-addon">
				                        <span>电话</span>
				                    </span>
                            <input type='text' name="mphone" class="form-control" placeholder="电话" value="{{request('mphone')}}" />
                        </div>
                        <div class='input-group col-sm-2 date' id='datetimepicker1'>
                            <input type='text' name="startTime" class="form-control" placeholder="请选择创建时间" value="{{request('startTime')}}" />
                            <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
                        </div>
                        至&nbsp;
                        <div class='input-group col-sm-2 date' id='datetimepicker2'>
                            <input type='text' name="endTime" class="form-control" placeholder="请选择创建时间" value="{{request('endTime')}}" />
                            <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
                        </div><br><br>
                        <div class="input-group col-sm-2">
                            <span class="input-group-addon">
                                <span>推荐</span>
                            </span>
                            <select name="is_recommend" class="form-control">
                                <option value="all" @if(request('is_recommend') == 'all') selected='selected' @endif>全部</option>
                                <option value="1" @if(request('is_recommend') == '1') selected='selected' @endif>已推荐</option>
                                <option value="0" @if(request('is_recommend') == '0') selected='selected' @endif>未推荐</option>
                            </select>
                        </div>
                        <div class='input-group col-sm-2'>
                            <span class="input-group-addon">
                                <span>地址</span>
                            </span>
                            <select name="province_id" class="form-control js-province">
                                <option value="">选择省份</option>
                                @foreach($provinceList as $v)
                                    <option @if(request('province_id') == $v['id']) selected='selected' @endif value="{{$v['id']}}">{{$v['title']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class='input-group col-sm-1'>
                            <select name="city_id" class="form-control js-city">
                                <option value="">选择城市</option>
                                @if (!empty($regionList[request('province_id')]))
                                @foreach($regionList[request('province_id')] as $v)
                                    <option @if(request('city_id') == $v['id']) selected='selected' @endif value="{{$v['id']}}">{{$v['title']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class='input-group col-sm-1'>
                            <select name="area_id" class="form-control js-area">
                                <option value="">选择区县</option>
                                @if (!empty($regionList[request('city_id')]))
                                @foreach($regionList[request('city_id')] as $v)
                                    <option @if(request('area_id') == $v['id']) selected='selected' @endif value="{{$v['id']}}">{{$v['title']}}</option>
                                @endforeach
                                @endif
                            </select>
                        </div>

                        <div class='input-group col-sm-2' style="width: 16%;">
                            <span class="input-group-addon">
                                <span>销售额</span>
                            </span>
                            <input type='text' name="sum_from" class="form-control" placeholder="请输入销售额" value="{{request('sum_from')}}" />
                        </div>至&nbsp;
                        <div class='input-group col-sm-2' style="width: 11%;">
                            <input type='text' name="sum_to" class="form-control" placeholder="请输入销售额" value="{{request('sum_to')}}" />
                        </div>
                        <br><br>
                        <div class="input-group col-sm-2">
                            <span class="input-group-addon">
                                <span>忽略</span>
                            </span>
                            <select name="is_ignore" class="form-control">
                                <option value="-1"  @if (request('is_ignore',0) == -1 )selected='selected'  @endif>全部</option>
                                <option value="1" @if (request('is_ignore',0) == 1 ) selected='selected'  @endif>已忽略</option>
                                <option value="0" @if (request('is_ignore',0) == 0 )  selected='selected'   @endif >未忽略</option>
                            </select>
                        </div>

                        <div class='input-group col-sm-2 date' id='datetimepicker3'>
                            <input type='text' name="expireFrom" class="form-control" placeholder="请选择过期时间" value="{{request('expireFrom')}}" />
                            <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
                        </div>
                        至&nbsp;
                        <div class='input-group col-sm-2 date' id='datetimepicker4'>
                            <input type='text' name="expireTo" class="form-control" placeholder="请选择过期时间" value="{{request('expireTo')}}" />
                            <span class="input-group-addon">
				                        <span class="glyphicon glyphicon-calendar"></span>
				                    </span>
                        </div>

                        <button type="submit" class="btn btn-primary">搜索</button>
                        <button id="reset" type="reset" class="btn btn-primary">重置</button>
                        <a id="shop_export" class="btn btn-primary">数据导出</a>
                        <a id="all_open" class="btn btn-primary js-open">全部开启底部logo链接</a>
                        <a class="btn btn-primary" id="shop_ignore">忽略</a>
                        <!-- <a class="btn btn-primary" id="shop_case">同步到案例</a> -->
                    </form>
                </div>
                <ul class="sheet table_title flex-between">
                    <li>
                        <label><input type="checkbox" name="" class="allSel" />全选</label>
                    </li>
                    <li>用户名字</li>
                    <li>店铺名称</li>
                    <li>店铺属性</li>
                    <li>商品数</li>
                    @if(isset($_GET['orderby']) && $_GET['orderby'] =='member_sum' && $_GET['order'] == 'desc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(0,0)">会员数 ↓</a></li>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='member_sum' && $_GET['order'] == 'asc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(0,1)">会员数 ↑</a></li>
                    @else
                        <li><a href="javascript:void(0);" onclick="sort_desc(0,0)">会员数</a></li>
                    @endif
                    @if(isset($_GET['orderby']) && $_GET['orderby'] =='sale_sum' && $_GET['order'] == 'desc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(1,0)">销售额 ↓</a></li>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='sale_sum' && $_GET['order'] == 'asc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(1,1)">销售额 ↑</a></li>
                    @else
                        <li><a href="javascript:void(0);" onclick="sort_desc(1,0)">销售额</a></li>
                    @endif
                    <li>具体地址</li>
                    <li>公司名称</li>
                    <li>创建手机号</li>
                    <li>创建时间</li>
                    @if(isset($_GET['orderby']) && $_GET['orderby'] =='shop_expire_at' && $_GET['order'] == 'desc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(2,0)">过期时间 ↓</a></li>
                    @elseif(isset($_GET['orderby']) && $_GET['orderby'] =='shop_expire_at' && $_GET['order'] == 'asc')
                        <li><a href="javascript:void(0);" onclick="sort_desc(2,1)">过期时间 ↑</a></li>
                    @else
                        <li><a href="javascript:void(0);" onclick="sort_desc(2,0)">过期时间</a></li>
                    @endif
                    <li>操作</li>
                </ul>
                @foreach($shopData as $key=>$val)
                <ul class="sheet table_body  flex-between">
                    <li><label><input type="checkbox" name='' value="{{$val['id']}}" />{{$val['id']}}</label></li>
                    <li>{{ $val['user']['name'] or '' }}</li>
                    <li class="Fimg">
                        <span>{{$val['shop_name'] or '' }}</span>
                        @if(isset($val['is_fee']) && $val['is_fee']=='0')
                        <img class="logo" src= "{{ config('app.source_url') }}staff/hsadmin/images/mian@2x.png"/>
                        @elseif(isset($val['is_fee']) && $val['is_fee']=='1')
                        <img class="logo" src="{{ config('app.source_url') }}staff/hsadmin/images/fu@2x.png"/>
                        @elseif(isset($val['is_fee']) && $val['is_fee']=='2')
                        <img class="logo " src="{{ config('app.source_url') }}staff/hsadmin/images/zeng@2x.png"/>
                        @endif
                    </li>

                    @if((isset($val['weixinConfigSub']) && $val['weixinConfigSub']) && (isset($val['wxxcxConfig']) && $val['wxxcxConfig']))
                    <li>公众号，小程序<a href="javascript:;" data-id="{{$val['id']}}" class="qrcode">查看</a></li>
                    @elseif((isset($val['weixinConfigSub']) && $val['weixinConfigSub']) && empty($val['wxxcxConfig']))
                    <li>公众号<a href="javascript:;" data-id="{{$val['id']}}" class="qrcode">查看</a></li>
                    @elseif(empty($val['weixinConfigSub']) && (isset($val['wxxcxConfig']) && $val['wxxcxConfig']))
                    <li>小程序<a href="javascript:;" data-id="{{$val['id']}}" class="qrcode">查看</a></li>
                    @else
                    <li>未绑定<a href="javascript:;" data-id="{{$val['id']}}" class="qrcode">查看</a></li>
                    @endif

                    <li>{{ $val['productCount'] or 0 }}</li>
                    <li>{{ $val['member_sum'] or 0 }}</li>
                    <li>{{ $val['sale_sum'] or 0 }}</li>
                    <li>{{ $val['address'] or '' }}</li>
                    <li>{{ $val['company_name'] or '' }}</li>
                    <li>{{ $val['user']['mphone'] or '' }}</li>
                    <li>{{ $val['created_at'] or '' }}</li>
                    <li>{{ $val['shop_expire_at'] }}
                        @if( $val['dueFlag'] =='2')
                        <img src="{{ config('app.source_url') }}staff/hsadmin/images/be_expire.png"/>
                        @elseif( $val['dueFlag'] =='1')
                        <img src="{{ config('app.source_url') }}staff/hsadmin/images/is_expire.png"/>
                        @endif
                    </li>
                    <li>
                        <div class="operate">
                            <a href="javascript:;" data-uid="{{$val['uid'] or 0}}" data-id="{{ $key }}"  data-wid="{{$val['id']}}" class="remarks">备注</a>
                            <a href="##" id="{{$val['id']}}" class="recommend">@if(isset($val['is_recommend']) && $val['is_recommend']==1)已推荐@else推荐@endif</a>
                            <a href="/staff/showEditShop?id={{$val['id']}}" class="modify">修改</a>
                            <!-- 备注信息 -->
                            <div class="remarks_tip" style="display:none">
                                <div class="remarks-info" style="padding: 20px 40px 20px 10px;">
                                    <div class="remarks-item">
                                        <span class="remarks-left">业务员：</span>
                                        <input type="text" value="{{ $val['user']['saleAchieve']['salesman'] ?? '' }}" name="salesman" class="sales-man">
                                    </div>
                                    <div class="remarks-item">
                                        <span class="remarks-left">商城销售：</span>
                                        <input type="text" value="{{ $val['achievement'] ?? '' }}" class="achievement">
                                    </div>
                                    <div class="remarks-item">
                                        <span class="remarks-left">备注：</span>
                                        <input type="text" value="{{ $val['remark'] ?? ''}}" class="trade">
                                    </div>
                                    <div class="btn-box">
                                        <a class="btn btn-primary success_remarks">确认</a>
                                        <a class="btn btn-cancle">取消</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="operate">
                            <a href="javascript:;" class="more">更多</a>
                        </div>
                        <div class="more-operate">
                            <a href="javascript:;" data-id="{{$val['id']}}" class="export">导出</a>
                            <a href="javascript:;" data-id="{{$val['id']}}" data-ignore="{{$val['is_ignore']}}" class="ignore">@if(isset($val['is_ignore']) && $val['is_ignore']==1)已忽略@else忽略@endif</a>
                            <a href="##" id="{{$val['id']}}_{{$val['uid'] or 0}}" class="del">删除</a>
                            <a href="##" data-wid="{{$val['id']}}" id="clean" class="clean">清空分销关系</a>
                        </div>




                    </li>
                </ul>
                @endforeach
                <div class="main_bottom flex-between">
                    <div class="free-charge-btn">
                        <input type="button" class="btn-charge btn btn-primary" value="标记付费" id="isFee"/>
                        <input type="button" class="btn-free btn" value="标记免费" id="isFree"/>
                        <input type="button" class="btn-given btn" value="标记赠送" id="isGive"/>
                        <input type="button" class="btn-open btn" value="开启底部logo链接" id="isOpen"/>
                        <input type="button" class="btn-close btn" value="关闭底部logo链接" id="isClose"/>
                    </div>
                    {!! $pageHtml !!}
                </div>

                <!--修改弹出狂-->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">

                </div>
            </div>
        </div>

        <!-- 导出店铺数据 -->
        <div class="change_tip" style="display:none">
            <div class="change_code" style="padding: 0 40px;">
                <div class="change_flex">
                    <div class="change_left">源店铺ID：</div>
                    <span class="change_span wid_from">0</span>
                </div>
                <div class="change_flex">
                    <div class="change_left">目标店铺ID：</div>
                    <input type="text" class="wid_to">
                </div>
            </div>
        </div>
        <!-- add by zhaobin 2018-9-25 -->
        <!-- 二维码 -->
        <div class="qrcode-tip" style="display:none;">
            <div class="qrcode-content">
                <!-- <ul>
                    <li class="wsc-code active">微商城二维码</li>
                    <li class="xcx-code">小程序二维码</li>
                </ul> -->
                <div class="qrcode-img">
                    <div class="qrcode-img-wsc">
                        <p>微商城二维码</p>
                        <div class="wxc-qrcode"></div>
                    </div>
                    <div class="qrcode-img-xcx">
                        <p>小程序二维码</p>
                        <div class="xcx-qrcode"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end -->
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
    <script type="text/javascript">
        //省市区地址
        var json = {!! $regions_data !!};

        //点击排序
        var SORT = [0,0,0,0];
        var ORDER_BY = ['member_sum','sale_sum', 'shop_expire_at'];
        var ORDER = ['asc','desc'];
        var FLAG = ['↑','↓'];
        var NAME = ['会员数','销售额', '过期时间'];
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