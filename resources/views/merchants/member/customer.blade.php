@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_jn8wm90l.css" />

<!-- layer  -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />

<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection
@section('slidebar')
@include('merchants.member.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="javascript:void(0);">客户管理</a>
            </li>
            <li class="">
                <a href="/merchants/member/blackList">黑名单</a>
            </li>

        </ul>
        <!-- 面包屑导航 结束 -->
    </div>

    <!-- 二级导航三级标题 结束 -->
    <!-- 帮助与服务 开始 -->
    <div id="help-container-open" class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <!--头部筛选部分-->
    <form class="filter_conditions flex_start" action="" method="get">
        <ul>
            <li>
                <label>手机号：</label>
                <input type="text" name="mobile" id="phoneNum" value="{{ request('mobile') }}" placeholder="手机号码" />
            </li>
            <li>
                <label>订单总金额：</label>
                <select name="amount">
                    <option value=''>请选择</option>
                    <option value="1" @if ( request('amount') == '1' ) selected @endif >100以下</option>
                    <option value="2" @if ( request('amount') == '2' ) selected @endif >100-500</option>
                    <option value="3" @if ( request('amount') == '3' ) selected @endif >500-1000</option>
                    <option value="4" @if ( request('amount') == '4' ) selected @endif >1000-2000</option>
                    <option value="5" @if ( request('amount') == '5' ) selected @endif >2000以上</option>
                </select>

            </li>
            <li>
                <label></label>
                <button type="submit" class="btn btn-primary screening">筛选</button>
                <a href="##" class="clear_conditions clear_screen">重置</a>
            </li>
        </ul>
        <ul>
            <li>
                <label>微信昵称：</label>
                    <input type="text" name="nickname" id="weixinName" value="{{ request('nickname') }}" placeholder="微信昵称" />

            </li>
            <li>
                <label>购次：</label>
                    <select name="buy_num">
                        {{--<option value="-1"  @if ( request('buy_num','-1') === -1 ) selected @endif >全部</option>--}}
                        @foreach ( $buyNumList as $key => $value )
                        @if ( request('buy_num',-1) == $key )
                        <option value="{{ $key }}" selected>{{ $value }}</option>
                        @else
                        <option value="{{ $key }}">{{ $value }}</option>
                        @endif
                        @endforeach
                    </select>

            </li>
        </ul>
        <ul>
            <li>
            <label>会员身份：</label>
                <select name="is_member">
                    <option value=''>全部</option>
                    <option value="2" @if ( request('is_member') == '2' ) selected @endif >是</option>
                    <option value="1" @if ( request('is_member') == '1' ) selected @endif >否</option>
                    <option value="3" @if ( request('is_member') == '3' ) selected @endif >已过期</option>
                </select>

            </li>
            <li>

                <label>时间：</label>
                <input type="text" name="latest_visit_time_start"  autoComplete="off"  value="{{ request('latest_visit_time_start') }}" class="js-start-time hasDatepicker" id="startDate" placeholder="查询开始时间">
                <span>至</span>
                <input type="text" name="latest_visit_time_end"  autoComplete="off"  value="{{ request('latest_visit_time_end') }}" class="js-end-time hasDatepicker" id="endDate" placeholder="查询结束时间">

            </li>
        </ul>
    </form>



    <!--添加客户-->
    {{--<a href="##" id="add_customer" class="btn btn-primary" data-toggle="modal" data-target="#myModal_1">添加客户</a>--}}
    <!--添加客户模态框-->
    <div class="modal fade" id="myModal_1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">添加客户</h4>
                </div>
                <form id="defaultForm" method="post" class="form-horizontal" action="{{URL('/merchants/member/customer')}}" >
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="add_name"><i>*</i>姓名：</label>
                            <div class="col-lg-7">
                                <input type="text" name="truename" id="add_name" class="form-control addtxt" value="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="add_phoneNum"><i>*</i>手机号：</label>
                            <div class="col-lg-7">
                                <input type="text" name="mobile" id="add_phoneNum" class="form-control addtxt" value="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="add_weixin">微信号：</label>
                            <div class="col-lg-7">
                                <input type="text" name="nickname" id="add_weixin" class="form-control" value="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label" for="note">备注：</label>
                            <div class="col-lg-7">
                                <textarea name="remark" id="note" rows="3" cols="26" style="resize: vertical;"/></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <button type="button" id="btn_add_client" class="btn btn-primary sure" >确定</button>
                        <button type="button" class="btn btn-default cancle" data-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--主要内容-->
    <div class="main_content">
        <ul class="main_content_title">
            <li><label for="allcheck"><input type="checkbox" name="allcheck" id="allcheck"/>&nbsp;姓名</label></li>
            <li>来源</li>
            <li>手机号</li>
            <li>微信昵称</li>
            <li>
                @if(isset($_GET['orderBy']) && $_GET['orderBy'] =='buy_num' && $_GET['order'] == 'desc')
                <a href="javascript:void(0);" onclick="sort_asc(0,0)">购次 ↓</a>
                @elseif(isset($_GET['orderBy']) && $_GET['orderBy'] =='buy_num' && $_GET['order'] == 'asc')
                <a href="javascript:void(0);" onclick="sort_asc(0,1)">购次 ↑</a>
                @else
                <a href="javascript:void(0);" onclick="sort_asc(0,0)">购次</a>
                @endif
            </li>
            <li>
                @if(isset($_GET['orderBy']) && $_GET['orderBy'] =='amount' && $_GET['order'] == 'desc')
                <a href="javascript:void(0);" onclick="sort_asc(1,0)">消费金额 ↓</a>
                @elseif(isset($_GET['orderBy']) && $_GET['orderBy'] =='amount' && $_GET['order'] == 'asc')
                <a href="javascript:void(0);" onclick="sort_asc(1,1)">消费金额 ↑</a>
                @else
                <a href="javascript:void(0);" onclick="sort_asc(1,0)">消费金额</a>
                @endif
            </li>
            <li>
                @if(isset($_GET['orderBy']) && $_GET['orderBy'] =='score' && $_GET['order'] == 'desc')
                <a href="javascript:void(0);" onclick="sort_asc(3,0)">积分 ↓</a>
                @elseif(isset($_GET['orderBy']) && $_GET['orderBy'] =='score' && $_GET['order'] == 'asc')
                <a href="javascript:void(0);" onclick="sort_asc(3,1)">积分 ↑</a>
                @else
                <a href="javascript:void(0);" onclick="sort_asc(3,0)">积分</a>
                @endif
            </li>
            <li>余额</li>
            <li>
                @if(isset($_GET['orderBy']) && $_GET['orderBy'] =='latest_access_time' && $_GET['order'] == 'desc')
                <a href="javascript:void(0);" onclick="sort_asc(2,0)">最近访问时间 ↓</a>
                @elseif(isset($_GET['orderBy']) && $_GET['orderBy'] =='latest_access_time' && $_GET['order'] == 'asc')
                <a href="javascript:void(0);" onclick="sort_asc(2,1)">最近访问时间 ↑</a>
                @else
                <a href="javascript:void(0);" onclick="sort_asc(2,0)">最近访问时间</a>
                @endif
            </li>
            <li>备注</li>
            <li>操作</li>
        </ul>
        @forelse ( $list as $v )
        <ul class="data_content" data-mid="{{$v['id']}}">
            <li><label><input type="checkbox"/>&nbsp;{{ $v['truename'] }}</label></li>
            <li>{{ $sourceList[$v['source']] or ''}}</li>
            <li>{{ $v['mobile'] }}</li>
            <li>
                @if($v['is_member'] == 1)
                <span class="member-sign is-member">会员</span>
                @elseif($v['is_member'] == 2)
                <span class="member-sign no-member">会员</span>
                @endif
                {{ $v['nickname'] }}
            </li>
            <li style='cursor: pointer' class='go_order_detail' data-id='{{$v['id']}}'>{{ $v['buy_num'] }}</li>
            <li>{{ $v['amount'] }}</li>
            <li><a href="javascript:;" class="integral-detail">{{ $v['score'] }}</a></li>
            <li><a href="javascript:;" class="balance-detail">{{ $v['money']/100 }}</a></li>
            <li>{{ $v['latest_access_time'] or $v['updated_at'] }}</li>
            <li><div>{{$v['remark'] ? $v['remark'] : '-'}}</div><span class="note-show-box">{{$v['remark'] ? $v['remark'] : '暂无备注'}}</span></li>
            @if(isset($v['is_pull_black']) && $v['is_pull_black'])
            <li>已冻结</li>
            @else
            <li>
                <!-- <a href="javascript:void(0);" class="send_msg">加余额&nbsp;-&nbsp;</a>

                <a href="javascript:void(0);" class="give_integral">给积分&nbsp;-&nbsp;</a> -->
                <a href="javascript:void(0);" class="get_more">更多</a>
                <!-- <a href="javascript:void(0);" class="annotate">备注&nbsp;-&nbsp;</a> -->
                <!-- <a href="javascript:void(0);" class="pullBlack">拉黑</a> -->
            </li>
            @endif
        </ul>
        @empty
        <ul class="data_content">暂无数据</ul>
        @endforelse
    </div>
    <!--短信询问框-->
    <div class="confirm hide">
        <div class="angle"></div>
        剩余短信数量不足，请先进行
        <a href="##" class="msg_recharge" style="color: dodgerblue;">短信充值</a>
    </div>

    <!-- 积分收支明细开始 -->
    <div id="integral_detail" class="layer-wrap none integral_detail" style="margin:0;">
        <div class='jifen_box'>
            <div class="jifen_title">积分收支明细 <span id='close_jifen_box'>x</span></div>
            <div class="jifen_table">
                <table class="t-table">
                    <thead>
                    <tr style="background-color:#F2F2F2;">
                        <th>时间</th>
                        <th>原因</th>
                        <th>明细</th>
                        <th>描述</th>
                        {{--<th>余额</th>--}}
                    </tr>
                    </thead>
                    <tbody class='jifen_tbody'>
                    </tbody>
                </table>
            </div>
            <div class="jifen_pageNum mx_pageNum">
            </div>
        </div>
    </div>
    <!-- 积分收支明细结束 -->
    <!--余额收支明细开始 -->
    <div id="balance_detail" class="layer-wrap none integral_detail" style="margin:0;">
        <div class='jifen_box'>
            <div class="jifen_title">余额收支明细 <span id='close_balance_box'>x</span></div>
            <div class="jifen_table balance_table">
                <table class="t-table">
                    <thead>
                    <tr style="background-color:#F2F2F2;">
                        <th>时间</th>
                        <th>类型</th>
                        <th>金额</th>
                        {{--<th>余额</th>--}}
                        <th>描述</th>
                    </tr>
                    </thead>
                    <tbody class='balance_tbody'>
                    </tbody>
                </table>
            </div>
            <div class="balance_pageNum mx_pageNum">
            </div>
        </div>
    </div>
    <!-- 余额收支明细结束 -->
    <!-- 分页 -->
    <div class="pageNum">
        <div class="batch-btn">
            <input type="button" value="批量发卡" class="btn batch-add"/>
        </div>
        <!-- 共 {{ $total }} 条记录 &nbsp;&nbsp;&nbsp; -->
        {{ $pageHtml }}
    </div>
</div>
@endsection
@section('page_js')
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- layer选择时间插件 -->
<script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
<script src="{{ config('app.source_url') }}static/js/require.js" ></script>
<script>
    var _url ='{{ config('app.url') }}';
</script>
<script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
<script src="{{ config('app.source_url') }}mctsource/js/member_saqjzw6z.js"></script>
<script>
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

   //点击排序
   var SORT = [0,0,0,0];
   var ORDER_BY = ['buy_num','amount','latest_access_time','score'];
   var ORDER = ['asc','desc'];
   var FLAG = ['↑','↓'];
   var NAME = ['购次','消费金额','最近访问时间'];
   function sort_asc(index,sort){
       var params = getallparam({order:ORDER[sort],orderBy:ORDER_BY[index]});
       window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
   }
</script>
@endsection