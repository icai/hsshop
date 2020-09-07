@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_jn8wm90l.css" />

<!-- layer  -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/layer.css" />

<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
<style type="text/css">
    .popover{
        max-width: 500px;
    }
</style>
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
            <li class="">
                <a href="/merchants/member/customer">客户管理</a>
            </li>
            <li class="hover">
                <a href="javascript:void(0);">黑名单</a>
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
    <form class="filter_conditions flex_between" action="" method="get">
        <ul>
            <li>
                <label>手机号：</label>
                <input type="text" name="mobile" id="phoneNum" value="{{ request('mobile') }}" placeholder="手机号码" />
            </li>
            <li><label>会员身份：</label>
                <select name="is_member">
                    <option value=''>全部</option>
                    <option value="2" @if ( request('is_member') == '2' ) selected @endif >是</option>
                    <option value="1" @if ( request('is_member') == '1' ) selected @endif >否</option>
                </select>
            </li>
            <li>
                <label></label>
                <button type="submit" class="btn btn-primary screening">筛选</button>
                <a href="##" class="clear_conditions clear_screen">清空筛选条件</a>
            </li>
        </ul>
        <ul>
            <li>
                <label>微信昵称：</label>
                    <input type="text" name="nickname" id="weixinName" value="{{ request('nickname') }}" placeholder="微信昵称" />
                
            </li>
            <li><label>购次：</label>
                <select name="buy_num">
                    @foreach ( $buyNumList as $key => $value )
                    @if ( request('buy_num') == $key )
                    <option value="{{ $key }}" selected>{{ $value }}</option>
                    @else
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endif
                    @endforeach
                </select>
            </li>
        </ul>
        <ul>
          
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
            <li>姓名</li>
            <li>来源</li>
            <li>手机号</li>
            <li>微信昵称</li>
            <li>积分</li>
            <li>余额</li>
            <li>备注</li>
            <li>操作</li>
        </ul>
        @forelse ( $list as $v )
        <ul class="data_content" data-mid="{{$v['id']}}">
            <li>{{ $v['truename'] }}</li>
            <li>{{ $sourceList[$v['source']] or ''}}</li>
            <li>{{ $v['mobile'] }}</li>
            <li>{{ $v['nickname'] }}</li>
            <li><a href="javascript:;" class="integral-detail">{{ $v['score'] }}</a></li>
            <li><a href="javascript:;" class="balance-detail">{{ $v['money']/100 }}</a></li>
            <li>{{$v['remark'] ? $v['remark'] : '-'}}<span class="note-show-box">{{$v['remark'] ? $v['remark'] : '暂无备注'}}</span></li>
            <li><a href="javascript:void(0);" class="move_out">移出黑名单</a></li>
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
    <div id="integral_detail" class="layer-wrap none" style="margin:0;"> 
        <table class="t-table">
            <thead>
                <tr style="background-color:#F2F2F2;">
                    <th>时间</th>
                    <th>原因</th>
                    <th>明细</th>
                    <th>描述</th>
                    <th>余额</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2017-05-11<br />&nbsp;&nbsp;&nbsp;15:30:12</td>
                    <td>积分抵现</td>
                    <td style="color:#FFE5A7;">-20</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>2017-05-11<br />&nbsp;&nbsp;&nbsp;15:30:12</td>
                    <td>积分抵现</td>
                    <td style="color:#FFE5A7;">-20</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>2017-05-11<br />&nbsp;&nbsp;&nbsp;15:30:12</td>
                    <td>积分抵现</td>
                    <td style="color:#FFE5A7;">-20</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>2017-05-11<br />&nbsp;&nbsp;&nbsp;15:30:12</td>
                    <td>积分抵现</td>
                    <td style="color:#FFE5A7;">-20</td>
                    <td>20</td>
                </tr>
                <tr>
                    <td>2017-05-11<br />&nbsp;&nbsp;&nbsp;15:30:12</td>
                    <td>积分抵现</td>
                    <td style="color:#FFE5A7;">-20</td>
                    <td>20</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- 积分收支明细结束 -->
    <!-- 分页 -->
    <div class="pageNum">
        共 {{ $total }} 条记录 &nbsp;&nbsp;&nbsp;
        {{ $pageHtml }}
    </div>
</div>
@endsection
@section('page_js')
<script src="{{ config('app.source_url') }}static/js/require.js" ></script>
<script src="{{ config('app.source_url') }}mctsource/static/js/config.js"></script> 
<script type="text/javascript">
    $(function(){
        //移出黑名单
        $('.move_out').click(function(e){
            e.stopPropagation();
            var _this = $(this);
            var id = _this.parents('.data_content').data('mid');
            var type = 0; //移出黑名单设置为0
            showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/member/setMemberType',
                data:{
                    type:type,
                    id:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    if(res.errCode===0){
                        tipshow('移出黑名单成功,2秒后跳转。','info');
                        setTimeout(function(){//两秒后跳转  
                            window.location.href='/merchants/member/customer';
                        },2000);  
                    }else{
                        tipshow('移出黑名单失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            }); 
        },'是否将此用户移出黑名单？')

        });

    })

</script>>
@endsection
