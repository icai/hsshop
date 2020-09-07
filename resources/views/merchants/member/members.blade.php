@extends('merchants.default._layouts')
@section('head_css')
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <!-- <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css"> -->
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_tkaol5f3.css" />
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
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 普通导航 开始 -->
            <ul class="common_nav">
                <li class="hover">
                    <a href="##">会员管理</a>
                </li>
                <li>
                    {{--<a href="{{URL('/merchants/member/import')}}">导入会员</a>--}}
                </li>
            </ul>
            <!-- 普通导航 结束  -->
        </div>
        <!-- 三级导航 结束 -->
        <!-- 帮助与服务 开始 -->
        <div id="help-container-open" class="help_btn">
            <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
        </div>
        <!-- 帮助与服务 结束 -->
    </div>
@endsection
@section('content')
    <div class="content">
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
                    {{--<a href="##" class="btn btn-primary screening">筛选</a>--}}
                    <button type="submit" class="btn btn-primary screening">筛选</button>
                    {{--<a href="##" class="clear_conditions clear_screen">清空筛选条件</a>--}}
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
                        <option value="" selected>全部</option>
                        <option value="0" @if ( request('buy_num') == '0' ) selected @endif>0</option>
                        <option value="1" @if ( request('buy_num') == '1' ) selected @endif>1+</option>
                        <option value="2" @if ( request('buy_num') == '2' ) selected @endif>2+</option>
                        <option value="3" @if ( request('buy_num') == '3' ) selected @endif>3+</option>
                        <option value="4" @if ( request('buy_num') == '4' ) selected @endif>4+</option>

                    </select>
                </li>
            </ul>
            <ul>
                <li>
                    <label class="control-label">时间：</label>
                    <input type="text" name="latest_visit_time_start"  autoComplete="off"  value="{{ request('latest_visit_time_start') }}" class="js-start-time hasDatepicker" id="startDate" placeholder="查询开始时间">
                    <span>至</span>
                    <input type="text" name="latest_visit_time_end"  autoComplete="off"  value="{{ request('latest_visit_time_end') }}" class="js-end-time hasDatepicker" id="endDate" placeholder="查询结束时间">
                </li>
                <li>
                    <label>来源：</label>
                    <select name="source">
                        <option value=''>全部</option>
                        <option value="6" @if ( request('source') == '6' ) selected @endif >微信小程序</option>
                        <option value="2" @if ( request('source') == '2' ) selected @endif >公众号</option>
                        <option value="7" @if ( request('source') == '7' ) selected @endif >支付宝小程序</option>
                    </select>
                    <label style="margin-left:100px">会员卡名称：</label>
                    <select name="card_id" class="member-list">
                        <option value=0> 全部 </option>
                        @foreach($allMemberCard as $card)
                            <option value={{ $card['id'] }}>{{ $card['title'] }}</option>
                        @endforeach
                    </select>
                </li>

            </ul>
        </form>
        <!--主要内容-->
        <div class="main_content">
            <ul class="main_content_title">
                <li><label for="allcheck"><input type="checkbox" name="allcheck" id="allcheck"/>&nbsp;微信昵称</label></li>
                <li>手机号</li>
                <li> {{ request('card_id') ? '会员卡名称' : '默认会员卡名称' }}</li>
                <li>
                    @if(isset($_GET['orderBy']) && $_GET['orderBy'] =='created_at' && $_GET['order'] == 'desc')
                        <a href="javascript:void(0);" onclick="sort_asc(3,0)">成为会员时间 ↓</a>
                    @elseif(isset($_GET['orderBy']) && $_GET['orderBy'] =='created_at' && $_GET['order'] == 'asc')
                        <a href="javascript:void(0);" onclick="sort_asc(3,1)">成为会员时间 ↑</a>
                    @else
                        <a href="javascript:void(0);" onclick="sort_asc(3,0)">成为会员时间</a>
                    @endif
                </li>
                <li>会员卡到期时间</li>
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
                <li>积分</li>
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
            @forelse ( $memberCardList as $v )
                <ul class="data_content" data-mid="{{$v->mid }}" data-cardId="{{ $v->card_id }}">
                    <li title="{{ $v->nickname  }}"><label><input type="checkbox"/>&nbsp;{{ $v->nickname }}</label></li>
                    <li>{{ $v->mobile  }}</li>

                    <li class="default-member">
                        <div class="member-tit"><span class="member-name">{{ $v->title }}</span><span style="color:#FF4343;font-size:12px">{{  $v->count > 1 ? "($v->count 张)" : '' }}</span></div>
                        @if($v->count > 1)
                            <div class="member-card">
                                <div>

                                    @foreach( $v->card as $card)
                                        <span title="{{ $card['title'] }}">{{ $card['title'] }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </li>
                    <li>{{ (isset($v->in_card_at) && !empty($v->in_card_at)) ? $v->in_card_at : $v->created_at }}</li>

                    <li>

                        {{ $v->default['expire_time'] }}

                    </li>
                    <li>{{$v->buy_num }}</li>
                    <li>{{ $v->amount }}</li>
                    <li><a href="javascript:;" class="integral-detail">{{ $v->score or '' }}</a></li>
                    <li><a href="javascript:;" class="balance-detail">{{ isset($v->money) && $v->money ? $v->money/100 : 0 }}</a></li>
                    <li>{{ $v->latest_access_time or $v->updated_at }}</li>
                    <li><div>{{ isset($v->remark) &&  $v->remark ? $v->remark : '-'}}</div><span class="note-show-box">{{isset($v->remark) &&  $v->remark ? $v->remark : '暂无备注'}}</span></li>
                    @if(isset($v->is_pull_black) && $v->is_pull_black)
                        <li>已冻结</li>
                    @else
                        <li>
                            <!-- <a href="javascript:void(0);" class="send_msg">加余额&nbsp;-&nbsp;</a><a href="javascript:void(0);" class="give_integral">给积分&nbsp;-&nbsp;</a> -->
                            <a href="javascript:void(0);" class="get_more">更多</a>
                        </li>
                    @endif
                </ul>
            @empty
                <ul class="data_content">暂无数据</ul>
            @endforelse
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
                <input type="button" value="批量删除" class="btn batch-delete"/>
            </div>
        <!-- &nbsp;共 {{ $memberCardList->total() }} 条记录 &nbsp;&nbsp;&nbsp; -->
            {{  $memberCardList->appends([
                    'mobile' => request('mobile'),
                    'nickname' => request('nickname'),
                    'buy_num' => request('buy_num'),
                    'latest_visit_time_start' => request('latest_visit_time_start'),
                    'latest_visit_time_end' => request('latest_visit_time_end'),
                    'source' => request('source'),
                    'card_id' => request('card_id'),
                    'be_member_time' => request('be_member_time')
            ])->links() }}
        </div>
    </div>
    <!-- add by zhoabin 2018-9-12 -->
    <!-- 删除会员卡弹窗 -->
    <div class="modal export-modal" id="del_model" style="height:100%">
        <div class="modal-dialog" id="del_model-dialog">
            <form class="form-horizontal">
                <div class="modal-content">
                    <div class="modal-header">
                        删除会员卡
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-striped ui-table ui-table-list">
                            <thead>
                            <tr>
                                <th>会员卡名称</th>
                                <th>领取时间</th>
                                <th>类型</th>
                                <th>权益</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="del_pagenavi"></div>
                        <div style="text-align:center" class="js-confirm-choose">
                            <input type="button" class="btn btn-primary btn-del" style="background:#3197FA;border:0" value="确定删除">
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- end -->
@endsection
@section('page_js')
    <!-- layer -->
    <script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
    <!-- layer选择时间插件 -->
    <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
    <!-- ajax分页js -->
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/extendPagination.js"></script>
    <script src="{{ config('app.source_url') }}static/js/require.js" ></script>
    <script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script>
    <!-- 当前页面js -->
    <script src="{{ config('app.source_url') }}mctsource/js/member_hcbwnvdo.js"></script>
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
        var ORDER_BY = ['buy_num','amount','latest_access_time','created_at'];
        var ORDER = ['asc','desc'];
        var FLAG = ['↑','↓'];
        var NAME = ['购次','消费金额','最近访问时间','成为会员时间'];
        function sort_asc(index,sort){
            var params = getallparam({order:ORDER[sort],orderBy:ORDER_BY[index]});
            window.location.href = 'http://'+ location.host + location.pathname + '?'+ params;
        }
    </script>
@endsection
