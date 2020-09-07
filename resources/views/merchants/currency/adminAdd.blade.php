@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_e8ww7p9c.css" />
@endsection
@section('slidebar')
@include('merchants.currency.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <!-- 三级导航 开始 -->
    <div class="third_nav">
        <!-- 面包屑导航 开始 -->
        <ul class="crumb_nav">
            <li>
                <a href="{{ URL('/merchants/currency/admin') }}">所有管理员</a>
            </li>
            <li>
                <a href="javascript:void(0);">编辑管理员</a>
            </li>
        </ul>
        <!-- 面包屑导航 结束 -->
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
    <div class="content content_2" style="display: ;">
        <div class="content_top_2">
            <span>请联系会搜云客服开通账号，目前注册功能已关闭，用户无法注册</span>
        </div>
        <form id="myForm">
            <div class="content_center_2">
                <div class="account">
                    <div class="form-group ">
                        <i style="margin-left: 10px">*</i>
                        <label class="control-label" for="inputTel" >会搜云帐号: </label>
                        @if(empty($tagData))
                        <input type="tel" class="form-control" name="phone" id="phone" value=""  placeholder="手机号码">
                            @else
                            <label>{{$tagData['phone']}}</label>
                        @endif
                        <i class="errMsg" style="display: none;">帐号不能为空</i>
                    </div>
                </div>
                <div class="limit">
                    <label for="">拥有的权限：</label>
                    @foreach($roleData as $val)
                        <label for="highUser"><input type="radio" name="roleId" id="highUser" value="{{$val['id']}}" @if(!empty($tagData['roleId']) && ($tagData['roleId'] == $val['id']))checked="checked"@endif   />{{$val['name']}}</label>
                    @endforeach
                </div>
                <div class="_msg highUserMsg showed">
                    高级管理员拥有所有权限——包括删除管理员，店铺、解绑联系人手机号等，对店铺资金、账户存在一定风险，请谨慎操作。<br />
                </div>
                <div class="_msg normalUserMsg">
                    创建或修改后将同时作为客服并［在线］，如果不需要接待客户，请该管理员到<a href="##">客服系统</a>中更改状态为［离线］。
                </div>
                <div class="_msg servePeopleMsg">
                    创建或修改后将同时作为客服并［在线］，如果不需要接待客户，请该管理员到<a href="##">客服系统</a>中更改状态为［离线］。
                </div>
                <div class="_msg checkPeople"></div>
                <button type="button" class="btn btn-primary" id="admin_submit" >确定提交</button>
            </div>
        </form>
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
        
            </table>
        </div>
    </div>
@endsection
@section('page_js')
<script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/currency_12yuqw3p.js"></script>

<script type="text/javascript">
	var flag_adminAdd = true;
    $(function () {
        $("#admin_submit").click(function () {
            @if(empty($tagData))
            if ($('input[name="roleId"]:checked').val() == ''){
                tipshow('请选择权限','warn');
                return false;
            }
            if(flag_adminAdd){
            	flag_adminAdd = false;
            	$.ajax({
                    url:'/merchants/currency/adminAdd',// 跳转到 action
                    data:{ 
                        'phone':$('#phone').val(),
                        'roleId':$('input[name="roleId"]:checked').val(),
                        '_token':$("meta[name='csrf-token']").attr('content')
                    },
                    type:'post',
                    cache:false,
                    dataType:'json',
                    success:function(data) {
                        if (data.status == 0){
                            tipshow(data.info,'warn');
                        }else {
                            $('#phone').attr('value',"");
                            tipshow(data.info);
                        }
                    },
                    error : function() {
                    	flag_adminAdd = true;
                        tipshow("异常！",'warn');
                    }
                });
            }
                
            @else
                if ($('input[name="roleId"]:checked').val() == ''){
                    tipshow('请选择修改的权限','warn');
                    return false;
            }
            	if(flag_adminAdd){
            		flag_adminAdd = false;
            		$.ajax({
		                url:'/merchants/currency/modifyAdmin',// 跳转到 action
		                data:{
		                    'id':'{{$tagData['id']}}',
		                    'roleId':$('input[name="roleId"]:checked').val(),
		                    '_token':$("meta[name='csrf-token']").attr('content')
		                },
		                type:'post',
		                cache:false,
		                dataType:'json',
		                success:function(data) {
		                    if (data.status == 0){
		                        tipshow(data.info,'warn');
		                    }else {
		                        $('#phone').attr('value',"");
		                        tipshow(data.info);
		                    }
		                },
		                error : function() {
		                	flag_adminAdd = true;
		                    tipshow("异常！");
		                }
		            });
            	}
                
            @endif
        })

    })
</script>


@endsection