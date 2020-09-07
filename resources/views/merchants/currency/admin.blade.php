@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_rm37no4u.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 普通导航 开始 -->
        <ul class="common_nav">
            <li class="hover">
                <a href="{{ URL('/merchants/currency/admin') }}">所有管理员</a>
            </li>
            {{--<li>--}}
                {{--<a href="{{ URL('/merchants/currency/partner') }}">我的拍档</a>--}}
            {{--</li>--}}
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
<div class="content content_1">
    <div class="content_top">
        <div class="content_top_left">
            <a href="{{ URL('/merchants/currency/adminAdd') }}" type="button" class="btn btn-success">添加管理员</a>
        </div>
        <div class="content_top_right">
            <a href="/home/index/detail/32" target="_blank">查看管理员相关教程</a>
        </div>
    </div>
    <div class="content_center">
        <ul class="title">
            <li class="admin_grid_spc">个人帐号</li>
            <li class="admin_grid_nick">昵称</li>
            <li class="admin_grid_wechat">微信昵称</li>
            <li class="admin_grid_shop">所属网点</li>
            <li class="admin_grid_mob">手机号</li>
            <li class="admin_grid_qq">QQ</li>
            <li class="admin_grid_add">添加人</li>
            <li class="admin_grid_time">加入时间</li>
            <li class="admin_grid_pow">帐号权限</li>
            <li class="admin_grid_wechat">核销员</li>
            <li class="admin_grid_op">操作</li>
        </ul>
        {{--@php--}}
            {{--$roleArr = array('未知','高级管理员','普通管理员','客服','核销员');--}}
        {{--@endphp--}}


    {{--@foreach($userRole as $r)--}}

        {{--@php--}}
            {{--$createAt = date('Y-m-d H:i:s',$r['create_at']);--}}
            {{--if(!empty($r['userInfo'])){--}}
                {{--foreach($r['userInfo'] as $info){--}}
                    {{--$nickname = !empty($info['nickname'])?trim($info['nickname']):'';--}}
                    {{--$mobile = !empty($info['mobile'])?trim($info['mobile']):'';--}}
                    {{--$offline_name = !empty($info['offline_name'])?trim($info['offline_name']):'';--}}
                    {{--$qq = !empty($info['qq'])?trim($info['qq']):'';--}}
                {{--}--}}
            {{--}--}}
        {{--@endphp--}}
        @foreach($manager[0]['data'] as $val)

        <ul class="tatilMsg">
            <li class="admin_grid_spc">{{$val['user']['mphone']}}</li>
            <li class="admin_grid_nick">{{ $val['user']['name']}}</li>
            <li class="admin_grid_wechat">{{ $val['nick_name'] or '未绑定'}}</li>
            <li class="admin_grid_shop">店铺 </li>
            <li class="admin_grid_mob">{{$val['user']['mphone']}}</li>
            <li class="admin_grid_qq">qq</li>
            <li class="admin_grid_add">{{$val['oper']['name']}}</li>
            <li class="admin_grid_time">{{ $val['created_at']}}</li>
            <li class="admin_grid_pow">{{$val['role']['name']}}</li>
            <li class="admin_grid_wechat">
                @if(isset($val['member']['truename']) && $val['member']['truename'])
                    {{ $val['member']['truename'] }}
                    <br />
                    <a href="javascript::void(0);" class="unset_hexiao_user" data-id="{{$val['uid']}}" style="color:#f60;">解绑</a>
                @else
                    <a href="javascript::void(0);" class="hexiao_user" data-id="{{$val['uid']}}">去绑定</a>
                @endif
            </li>
            <li class="admin_grid_op">
                <a href="/merchants/currency/adminAdd?id={{$val['id']}}">编辑</a>
                @if($val['uid']!= $val['oper_id'])
                    - <a  class="del" id="{{$val['id']}}"  >删除</a>
                @endif
                @if($val['open_id'])
                    - <a  class="unbind" id="{{$val['uid']}}">解绑</a>
                    @else
                    - <a class="bind" id="{{$val['uid']}}">绑定</a>
                @endif
            </li>
        </ul>
        @endforeach
    </div>
    <!-- 添加分页 -->
    
    <div class="list_page">{{ $manager[1] }}</div>
    <div class="content_bottom">
        <p>会搜云每家店铺都可设置多个管理员帐号来共同管理店铺，目前管理员权限分别是：高级管理员、普通管理员和客服权限，具体场景如下：</p>
        <span id="">
            1. 需更换会搜云店铺登录账号；<br />
            2. 之前员工离职，需更换其他员工来管理店铺；<br />
            3. 店铺规模太大，需让多人管理，且要求每个人有独立账号；<br />
            4. 店铺过户给其他人；<br />
            5. 代运营/拍档需帮别人装修管理店铺；<br />
            <a href="/home/index/detail/32" target="_blank">查看管理员相关教程</a>
        </span>
    </div>
</div>
<!--添加管理员页面-->
<div class="content content_2" style="display: none;">
    <div class="content_top_2">
        <span>你需要让新管理员先去注册一个“会搜云账号”<a href="##">（注册链接）</a>，再把他添加进来。</span>
    </div>
    <div class="content_center_2">
        <div class="account">
            <div class="form-group ">
                <i style="margin-left: 10px">*</i>
                <label class="control-label" for="inputTel">会搜云帐号: </label>
                <input type="tel" class="form-control" id="inputTel" placeholder="手机号码">
                <i class="errMsg" style="display: none;">帐号不能为空</i>
            </div>
        </div>
        <div class="limit">
            <label for="">拥有的权限：</label>
            <label for="highUser"><input type="radio" name="people" id="highUser" value="高级管理员" checked="checked" />高级管理员</label>
            <label for="normalUser"><input type="radio" name="people" id="normalUser" value="普通管理员" />普通管理员</label>
            <label for="servicePeople"><input type="radio" name="people" id="servicePeople" value="客服" />客服</label>
            <label for="checkPeople"><input type="radio" name="people" id="checkPeople" value="核销员" />核销员</label>
        </div>
        <div class="_msg highUserMsg showed">
            高级管理员拥有所有权限——包括删除管理员，店铺、解绑联系人手机号等，对店铺资金、账户存在一定风险，请谨慎操作。<br />
            在添加服务商帮您运营店铺时，请尽可能设置他为普通管理员，以防产生风险，我们推荐您寻找会搜云认证的代理商或拍档提供服务。<a href="##">去服务市场</a><br />
            对于骚扰商家甚至干扰商家运营的服务商，会搜云将严厉处罚，也欢迎您向会搜云举报违规的服务商。<a href="##">查看公告</a><br />
            创建或修改后将同时作为客服并［在线］，如果不需要接待客户，请该管理员到<a href="##">客服系统</a>中更改状态为［离线］。
        </div>
        <div class="_msg normalUserMsg">
            创建或修改后将同时作为客服并［在线］，如果不需要接待客户，请该管理员到<a href="##">客服系统</a>中更改状态为［离线］。
        </div>
        <div class="_msg servePeopleMsg">
            创建或修改后将同时作为客服并［在线］，如果不需要接待客户，请该管理员到<a href="##">客服系统</a>中更改状态为［离线］。
        </div>
        <div class="_msg checkPeople"></div>
        <button type="button" class="btn btn-primary">确定提交</button>
    </div>
    <div class="content_bottom_2">
        <table border="2" cellspacing="0" cellpadding="10">
            <tr align="left"><th>管理员类型</th><th>权限</th></tr>
            <tr>
                <td width="20%">高级管理员</td>
                <td width="80%">具备店铺所有管理的权限</td>
            </tr>
            <tr>
                <td>普通管理员</td>
                <td>具备除了“添加管理员”、“删除管理员”、“删除店铺”、“微信账号解绑”之外，店铺所有管理的权限</td>
            </tr>
            <tr>
                <td rowspan="11">客服</td>
                <td>进入在线多客服</td>
            </tr>
            <tr>
                <td>查看微信概况</td>
            </tr>
            <tr>
                <td>查看微信实时信息 <i>（仅绑定了认证服务号的店铺具备该功能）</i></td>
            </tr>
            <tr>
                <td>访问“客户”频道 <i>（仅绑定了认证服务号的店铺具备该功能）</i></td>
            </tr>
            <tr>
                <td>可以新建客户标签、可以给客户打标签、可以给客户设置等级、可以发放积分 <i>（仅绑定了认证服务号的店铺具备该功能）</i></td>
            </tr>
            <tr>
                <td>不能删除、修改标签，不能新建或编辑等级规则，不能新建或修改积分规则 <i>（仅绑定了认证服务号的店铺具备该功能）</i></td>
            </tr>
            <tr>
                <td>查看商品列表、查看商品分组</td>
            </tr>
            <tr>
                <td>不能编辑商品、上下架商品，不能编辑商品分组</td>
            </tr>
            <tr>
                <td>查看订单概况</td>
            </tr>
            <tr>
                <td>查看所有订单、维权订单、加星订单等订单列表</td>
            </tr>
            <tr>
                <td>能对订单改价、填写备注、加星、标记退款、发货、关闭订单</td>
            </tr>
            <tr>
                <td rowspan="3">核销员</td>
                <td>可以通过会搜云微商城手机客户端进行扫码核销</td>
            </tr>
            <tr>
                <td>可以通过会搜云微商城手机客户端查看自己的扫码核销记录</td>
            </tr>
            <tr>
                <td>核销员没有权限查看对应店铺的会搜云网页后台</td>
            </tr>
            

        </table>
    </div>
</div>

@endsection
@section('page_js')

    <script type="text/javascript">
        $(function () {
            $(".del").click(function () {

                var a = $(this);

                var id = $(this).attr('id');
                $.ajax({
                    url:'/merchants/currency/delManger',// 跳转到 action
                    data:{
                        'id':id,
                        '_token':$("meta[name='csrf-token']").attr('content')
                    },
                    type:'post',
                    cache:false,
                    dataType:'json',
                    success:function(data) {
                        if (data.status == 0){
                            tipshow(data.info);
                        }else {
                            a.parent().parent().remove();
                            tipshow(data.info,'warn');
                        }
                    },
                    error : function() {
                        // view("异常！");
                        tipshow("异常！",'warn');
                    }
                });
                return false;
            });

            $(".bind").click(function () {
                var uid = $(this).attr('id');
                var url = '/merchants/currency/bindAdmin';
                var data = {
                    'uid':uid,
                    '_token':$("meta[name='csrf-token']").attr('content')
                };
                $.post(url,data,function (data) {
                    if(data.status == 1){
                        hstool.open({
                            title:"店铺管理员绑定邀请二维码（有效期5分钟，过期请重新获取）",
                            area:["430px","430px"],
                            content: '<div><img src="'+data.data+'" ></div>'
                        })

                    }else {
                        layer.msg(data.info, {icon: 5});
                        setTimeout(function(){
                            window.location.reload();
                        },2000)
                    }
                })
            });


            $(".unbind").click(function () {
                var uid = $(this).attr('id');
                var url = '/merchants/currency/unbindAdmin';
                $.get(url,{ uid: uid },function (data) {
                    if(data.status == 1){
                        setTimeout(function(){
                            window.location.reload();
                        },2000)
                        layer.msg('解绑成功', {icon: 4});
                    }else {
                        layer.msg(data.info, {icon: 5});
                    }
                })
            });

            $(".hexiao_user").click(function () {
                var uid = $(this).data('id');
                var url = '/merchants/currency/hexiaoQrcode';
                var data = {
                    'uid':uid,
                    '_token':$("meta[name='csrf-token']").attr('content')
                };
                $.post(url,data,function (data) {
                    hstool.open({
                        title:"绑定店铺核销员",
                        area:["230px","230px"],
                        content: '<div>'+data.qrcode+'</div>'
                    })
                })
            });

            $(".unset_hexiao_user").click(function () {
                var uid = $(this).data('id');
                var url = '/merchants/currency/unsetHexiaoUser';
                $.get(url,{ uid: uid },function (data) {
                    if(data.status == 1){
                        setTimeout(function(){
                            window.location.reload();
                        },2000)
                        layer.msg('成功解除绑定店铺核销员', {icon: 1});
                    }else {
                        layer.msg(data.info, {icon: 5});
                    }
                })
            });
        });
    </script>

    <!-- 搜索插件 -->
<script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
<!-- 弹框插件 -->
<script src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_rm37no4u.js"></script>
@endsection


