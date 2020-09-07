@extends('merchants.default._layouts')

@section('title',$title)

@section('head_css')
    <!-- 上传插件样式 -->
    <link rel="stylesheet" type="text/css" href="{{config('app.source_url')}}static/css/webuploader.css">
    <!-- 微信公众号公共样式 -->
    <!-- <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/wechat_base.css" /> -->
    <link href="{{ config('app.source_url') }}mctsource/static/css/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/member_sg24wpsw.css">

    <!-- 自定义layer皮肤css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
    <style>
        .active_msg{
            display: none;
        }
        .file-select-button{width:200px;}
        .reply_cap{display: none}
        .reply_cap .imgs img{display: inline-block;width:100%;height:100%}
        .reply_cap .imgs{position: relative}
        .reply_cap .imgs div{position: absolute;bottom: 0;left:100px;color:#3388ff;cursor: pointer}
        .reply_cap .imgs div span{padding-right:5px;}
        .myModal-adv .modal-content{width:880px;height:715px}
    </style>
@endsection

@section('slidebar')
    @include('merchants.member.slidebar')
@endsection
@section('middle_header')
    <div class="middle_header">
        <!-- 三级导航 开始 -->
        <div class="third_nav">
            <!-- 面包屑导航 开始 -->
            <ul class="crumb_nav">
                <li>
                    <a href="/merchants/member/membercard">会员卡管理</a>
                </li>
                <li>
                    <a href="javascript:;">新建会员卡</a>
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
    <div class="content">
    <!-- onsubmit="return checkForm()" action="{{URL('/merchants/member/membercard')}}" -->
        <form role="form" class="form-horizontal" name="cardForm" method="post" id="cardForm"  > <!-- -->
            <div class="card">
                <div class="card_left">
                    <div class="left_content">
                        <h1><span>会员卡</span></h1>
                  
                        <div class="membercard-area">
                            <div class="card-region @if(isset($memberCard['cover']) && $memberCard['cover']==1) {{$memberCard['cover_value'] or Color010}} @else  Color010  @endif">
                                <div class="card-header">
                                    <h4 class="shop-name">
                                        <span class="shop-logo" style="background-image:url({{ imgUrl($weixinInfo->logo) }})"></span>
                                        <span>{{$weixinInfo->shop_name}}</span>
                                    </h4>
                                    <div class="qr-code"></div>
                                </div>
                                <h3 class="member-type"></h3>
                                <div class="card-content">
                                    <p class="expiry-date">有效期：
                                        <span>无限期</span>
                                    </p>
                                </div>
                            </div>
                            <div class="membership-region block js-show-sub-info">
                                <h3 class="membership-header border_none">
                                    <span class="icon info-icon-member"></span>会员权益</h3>
                                @if(isset($memberCard['member_power']) && !empty($memberCard['member_power']))
                                    <ul class="membership">
                                        <li class="membership-item" @if(in_array(1,explode(',',$memberCard['member_power']))) style="display: inline-block;" @endif>
                                            <p class="item-name free-shipping">包邮</p></li>
                                        <li class="membership-item" @if(in_array(2,explode(',',$memberCard['member_power']))) style="display: inline-block;" @endif>
                                            <p class="item-name discount">{{$memberCard['discount'] or ''}}折</p></li>
                                        <li class="membership-item coupon-icon" @if($memberCard['coupon_conf'] &&in_array(3,explode(',',$memberCard['member_power']))) style="display: inline-block;" @endif>
                                            <p class="item-name coupon" >优惠券</p></li>
                                        <li class="membership-item" @if(in_array(4,explode(',',$memberCard['member_power']))) style="display: inline-block;" @endif>
                                            <p class="item-name score" >积分</p>
                                        </li>
                                    </ul>
                                @else
                                    <ul class="membership">
                                        <li class="membership-item"">
                                            <p class="item-name free-shipping">包邮</p></li>
                                        <li class="membership-item">
                                            <p class="item-name discount">{{$memberCard['discount'] or ''}}折</p></li>
                                        <li class="membership-item">
                                            <p class="item-name coupon">优惠券</p></li>
                                        <li class="membership-item">
                                            <p class="item-name score">积分</p>
                                        </li>
                                    </ul>
                                 @endif
                            </div>
                            <div class="block block-list">
                                <a href="javascript:;" class="block-item js-show-sub-info">
                                    <p class="arrow-right">
                                        <span class="icon info-icon-description"></span>使用须知</p>
                                    <p class="block-sub-desc js-block-sub-desc" style="display:none">会员折扣9折， 免邮费， 更多权益请查看使用须知</p>
                                </a>
                                <a href="javascript:;" class="block-item">出示会员凭证</a>
                            </div>
                            <a href="javascript:;" class="single-block block">店铺主页</a>
                        </div>
                    </div>
                </div>
                <div class="card_right">
                    <div class="arrow"></div>
                    <div class="edit_list">
                        <div class="right_title">会员卡基本信息</div>
                        <div class="edit_content">
                            <div class="r1 text-right">店铺名称：</div>
                            <div class="r2">{{$weixinInfo->shop_name}}</div>
                        </div>
                        <div class="edit_content">
                            <div class="r1 text-right">店铺Logo：</div>
                            <div class="r2">
                                <div class="logo_img">
                                    <img class="lazy" data-original="{{ imgUrl($weixinInfo->logo) }}">
                                </div>
                                <div class="info">
                                    如需修改店铺信息,请在<a>店铺设置</a>中更新
                                </div>
                            </div>
                        </div>

                        <div class="edit_content">
                            <div class="r1 text-right">卡片封面：</div>
                            <div class="r2 form-group div_flex pad5-0">
                                @php
                                    $bgchart = !empty($memberCard['bgchart'])?trim($memberCard['bgchart']):null;
                                    if($bgchart){
                                        $chooseChecked = false;
                                        $bgChecked = true;
                                        $srcBg = imgUrl().'img/'.$bgchart;
                                    }else{
                                        $chooseChecked = true;
                                        $bgChecked = false;
                                    }
                                @endphp
                                <input type="radio" name="cover" id='bg_color' value="0" @if(isset($memberCard['cover']) && $memberCard['cover']==0) checked @endif checked>
                                <span class="bg_color malef05">背景色</span>
                                <input type="hidden" name="bg_color" value="{{$memberCard['cover_value'] or 'Color010'}}">
                                <div class="controls">
                                    <div class="cover_bg_color malef05">
                                        <div class="bgColor @if(isset($memberCard['cover']) && $memberCard['cover']==0) {{$memberCard['cover_value'] or Color010}} @else  Color010  @endif"></div>
                                    </div>
                                    <ul class="bgColor_cap">
                                    </ul>
                                </div>
                            </div>
                            <div class="r2 form-group">
                                <input type="radio" name="cover" id="bg_image"  value="1" @if(isset($memberCard['cover']) && $memberCard['cover']==1) checked @endif>
                                <span class="bg_color">封面图片</span>
                                <div class="file-select-button">
                                    <div class="reply_cap"  @if(isset($memberCard['cover']) && $memberCard['cover']==1) style="display: block;"@endif>
                                        <div class="ctts">
                                            <div class="imgs" id=''>
                                                <img class="lazy" src="{{ $memberCard['cover_value'] or '' }}"/>
                                                <div>
                                                    <span class="co_3197FA" id='btn-close'>删除</span>
                                                    <span>|</span>
                                                    <span class="xiugai">修改</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="javascript:;" data-toggle-text="重新选择..." class="control-bgchartaction bg_color" @if(isset($memberCard['cover']) && $memberCard['cover']==1) style="display: none; @endif">选择图片</a>
                                </div>
                                <!-- 上传图片显示 -->
                                <span class="bg_image">
                                    <input type="hidden" name="bg_img" value="{{ $memberCard['cover_value'] or '' }}">
                                </span>
                                <div class="info">推荐尺寸710x360px</div>
                                <div class="info fenmian" style="color: red;margin-top:5px;display:none">请选择封面图片</div>
                            </div>
                        </div>

                        <!-- 会员卡名称 -->
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label"><em>*</em> 会员卡名称：</label>
                            <div class="col-sm-8 padding_0">
                                <input type="text" name="title" class="form-control" value="{{$memberCard['title'] or ''}}">
                            </div>
                        </div>
                        <!-- 会员特权 -->
                        <?php
                        $power = [];
                        if(isset($memberCard['member_power']))
                        {
                            $power = explode(',',$memberCard['member_power']);
                        }
                        ?>
                        <div class="form-group member_limit">
                            <label for="inputPassword3" class="col-sm-3 control-label"><em>*</em> 会员特权：</label>
                            <div class="col-sm-8 inline">
                                <input type="checkbox" name="member_power[]" value="1" @if(in_array(1,$power)) checked @endif>
                                <span>包邮</span>
                            </div>
                        </div>

                        <div class="form-group member_limit">
                            <label for="inputPassword3" class="col-sm-3 control-label"></label>
                            <div class="col-sm-8 inline">
                                <input type="checkbox" name="member_power[]" value="2" @if(in_array(2,$power)) checked @endif>
                                <span>会员折扣</span>
                                &nbsp;
                                <input type="text" name="discount" class="form-control  width_100" value="{{$memberCard['discount'] or ''}}">
                                &nbsp;
                                <span>折</span>
                            </div>
                        </div>
                        <div class="member_power">
                            @if(in_array(3,$power) && !empty($memberCard['coupon_conf']))
                                @forelse(json_decode($memberCard['coupon_conf'],true) as $key=>$value)
                                    <div class="form-group member_limit">
                                        <label for="inputPassword3" class="col-sm-3 control-label"></label>
                                        <div class="col-sm-9 inline @if($key!=0) padleft10 @endif">
                                            @if($key == 0 && in_array($value['coupon_id'],array_column($conponList,'id')))
                                                <input type="checkbox" class="member_p" name="member_power[]" value="3" checked>
                                                @else
                                                <input type="checkbox" class="member_p" name="member_power[]" value="3" >
                                            @endif
                                            <span>优惠券 开卡赠送</span>
                                            &nbsp;
                                            <select class="form-control width_130 coupons_select" name="coupon_type[]">
                                                <option value="0">请选择优惠券</option>
                                                @foreach($conponList as $vo)
                                                    <option @if($vo['id'] && $value['coupon_id'] == $vo['id']) selected @endif value="{{$vo['id']}}">{{$vo['title']}}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="coupon_num[]" value="@if (!empty($vo['id']) && in_array($value['coupon_id'],array_column($conponList,'id')))  {{  $value['num']}} @endif" class="form-control  width_40">
                                            &nbsp;
                                            <span>张</span>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                        <div class="form-group member_limit">
                                            <label for="inputPassword3" class="col-sm-3 control-label"></label>
                                            <div class="col-sm-9 inline">
                                                <input type="checkbox" class="member_p"  name="member_power[]" value="3">
                                                <span>优惠券 开卡赠送</span>
                                                &nbsp;
                                                <select class="form-control width_130 coupons_select" name="coupon_type[]">
                                                    <option value="0">请选择优惠券</option>
                                                    @foreach($conponList as $vo)
                                                        <option value="{{$vo['id']}}">{{$vo['title']}}</option>
                                                    @endforeach
                                                </select>
                                                <input type="text" name="coupon_num[]" class="form-control  width_40">
                                                &nbsp;
                                                <span>张</span>
                                            </div>
                                        </div>
                                    @endif

                        </div>
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label"></label>
                            <div class="col-sm-8 padd_0">
                                <a href="javascript:void(0);" class="color_bule add_coupons" style="color:#3197FA;">添加</a>
                                <p class="help-desc radio">优惠券过期、被删除、或库存为0时，系统不再送券</p>
                            </div>
                        </div>
                        <div class="form-group member_limit">
                            <label for="inputPassword3" class="col-sm-3 control-label"></label>
                            <div class="col-sm-9 inline">
                                <input type="checkbox" name="member_power[]" value="4" @if(in_array(4,$power)) checked @endif>
                                <span>送积分</span>
                                <span class="card_give">开卡赠送</span>
                                <input type="text" name="score" class="form-control width_100" value="{{$memberCard['score'] or ''}}">
                                &nbsp;
                                <span>积分</span>
                            </div>
                        </div>
                        <!-- 使用须知 --> 
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label"><em>*</em> 使用须知：</label>
                            <div class="col-sm-8 padd_0">
                            <textarea name="description" onKeyUp="if(this.value.length > 1000) this.value=this.value.substr(0,1000)" class="form-control" rows="7" cols="20" style="resize:auto" placeholder="会员特权会根据您上文的勾选情况系统自动生成，此处可填写其它补充信息以便会员知晓，最多可输入1000个字符"
                            >{{$memberCard['description'] or ''}}</textarea>
                            </div>
                        </div>
                        <!-- 客服电话 -->
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">客服电话：</label>
                            <div class="col-sm-8 inline">
                                <input type="text" name="telephone" class="form-control" value="{{ $memberCard['service_phone'] or '' }}" placeholder="请输入手机号或固定电话">
                            </div>
                        </div>
                    </div>
                    <div class="edit_list">
                        <div class="right_title">领取设置</div>
                    @php
                        $over_day_to = isset($memberCard['over_day_to'])?$memberCard['over_day_to']:0;
                    @endphp

                    @if(empty($card_status))
                        <!-- 会员期限开始 -->
                            <div class="form-group member_limit">
                                <label for="inputPassword3" class="col-sm-3 control-label"><em>*</em> 会员期限：</label>
                                <div class="col-sm-8 inline">
                                    <input type="radio" name="date_limit" value="0" @if(isset($memberCard['limit_type']) &&  $memberCard['limit_type']== 0) checked @else(!isset($memberCard['limit_type'])) checked  @endif>
                                    <span>无期限</span>
                                </div>
                            </div>
                            <div class="form-group member_limit">
                                <label for="inputPassword3" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8 inline">
                                    <input type="radio" name="date_limit" value="1" @if(isset($memberCard['limit_type']) &&  $memberCard['limit_type']== 1) checked @endif>
                                    <input type="text" name="limit_days" class="form-control  width_100" value="{{$memberCard['limit_days'] or ''}}">
                                    <span>天</span>
                                </div>
                            </div>
                            <div class="form-group member_limit">
                                <label for="inputPassword3" class="col-sm-3 control-label"></label>
                                <div class="col-sm-8 inline">
                                    <input type="radio" name="date_limit" value="2" @if(isset($memberCard['limit_type']) &&  $memberCard['limit_type']== 2) checked @endif>
                                    <input type="text" name="startAt" class="form-control margin10" value="{{$memberCard['limit_start'] or ''}}" placeholder="开始时间" id="start_time">
                                    <input type="text" name="endAt" class="form-control" value="{{$memberCard['limit_end'] or ''}}"  placeholder="结束时间" id="end_time">
                                </div>
                            </div>
                            <!-- 会员期限结束 -->
                    @endif

                    @if(isset($card_status) && $card_status == 2)
                        <!-- 需购买的会员卡会员期限 -->
                            <div class="form-group member_limit">
                                <label for="inputPassword3" class="col-sm-3 control-label"><em>*</em> 会员期限：</label>
                                <div class="col-sm-9 inline">
                                    <div class="member_date_wrap">
                                        <div>
                                            <ul>
                                                <li class="sku-atom">
                                                    <span></span>
                                                    <div class="close-modal small js-remove-sku-atom">×</div>
                                                </li>
                                            </ul>
                                            <a href="javascript:;" class="js-add-sku-atom add-sku" style="display: inline;">+添加
                                                <span class="sku-tip">请填写会员期限，如“年费会员”或“季度会员”</span>
                                            </a>
                                        </div>
                                        <div class="popover popover-link-wrap bottom">
                                            <div class="arrow"></div>
                                            <div class="popover-inner popover-link">
                                                <div class="popover-content">
                                                    <div class="form-inline">
                                                        <input type="text" class="link-placeholder js-link-placeholder form-control" placeholder="">
                                                        <button type="button" class="btn btn-primary js-btn-confirm" data-loading-text="确定">确定</button>
                                                        <button type="reset" class="btn js-btn-cancel">取消</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 需购买的会员卡会员期限 -->
                    @endif
                    @if(empty($card_status) || $card_status == 2)
                       
                    @endif

                    @if(isset($card_status) && $card_status == 1)
                        <?php
                        $conditionData = [];
                        if(isset($memberCard['up_condition'])){
                            $conditionData = explode('||',$memberCard['up_condition']);
                        }
                        ?>
                        <!-- 按规则发放会员卡 -->
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">升级条件：</label>
                                <div class="col-sm-8 inline">
                                    <span>累计支付成功</span>
                                    &nbsp;
                                    <input type="text" class="form-control width_100" placeholder="" name="cumulative_pay" value="@if(isset($conditionData[0])){{$conditionData[0]}}@endif">
                                    &nbsp;
                                    <span>笔</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">或</label>
                                <div class="col-sm-8 inline">
                                    <span>累计消费金额</span>
                                    &nbsp;
                                    <input type="text" class="form-control width_100" placeholder="" name="cumulative_amount" value="@if(isset($conditionData[1])){{$conditionData[1]}}@endif">
                                    &nbsp;
                                    <span>元</span>
                                </div>
                            </div>
                            <!-- 升级条件设置开始 -->
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label">或</label>
                                <div class="col-sm-8 inline">
                                    <span>累计积分达到</span>
                                    &nbsp;
                                    <input type="text" class="form-control width_100" placeholder="" name="cumulative_score" value="@if(isset($conditionData[2])){{$conditionData[2]}}@endif">
                                    &nbsp;
                                    <span>分</span>
                                </div>
                            </div>
                            <!-- 升级条件设置结束 -->
                        <?php
                        $options = array(1,2,3,4,5);
                        ?>
                        <!-- 等级设置开始 -->
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label"><em>*</em> 等级：</label>
                                <div class="col-sm-8 padding_0">
                                    <select class="form-control" name="card_rank">
                                        <option value="0">请选择</option>
                                        @foreach($options as $op)
                                            <option value="{{$op}}" @if(isset($memberCard['card_rank']) && $memberCard['card_rank']==$op) selected @endif>{{$op}}</option>
                                        @endforeach
                                    </select>
                                    <p class="help-desc">数字越大等级越高，当会员满足条件时会自动发放上一等级对应的会员卡</p>
                                </div>
                            </div>
                            <!-- 等级设置结束 -->
                    @endif
                    <!-- 激活设置开始 -->
                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">激活设置：</label>
                            <div class="col-sm-8 padding_top7">
                                <div class="line">
                                    <input type="radio" name="active" class="is_active" value="0" checked>
                                    <span class="no_active">无需激活</span>
                                    <input type="radio" name="active" class="is_active malef05" value="1" @if(isset($memberCard['is_active']) && $memberCard['is_active']==1) checked @endif>
                                    <span class="yes_active">需要激活</span>
                                </div>
                                
                                <p class="help-desc">如需在线下门店使用，建议设置为“需要激活”</p>
                            </div>
                        </div>
                        <!-- 激活设置结束 -->
                        <!-- 分享设置开始 -->
                        @if($card_status != 1)
                            
                        @endif
                        <!-- 分享设置结束 -->
                        <!-- 按规则发放会员卡 -->
                    </div>
                   
                </div>
                <div class="btn_grounp">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="id" value="{{ $memberCard['id'] or 0 }}" />
                    <input type="hidden" name="card_status" value="{{$card_status or 0}}">
                    @if(!empty($memberCard))
                        <a class="btn btn-primary dele_form" data-id="{{ $memberCard['id']}}">删除</a>
                        @if($memberCard['state'] == 1)
                            <a class="btn btn-default disabled_form" data-id="{{ $memberCard['id']}}">禁用</a>
                        @else
                            <a class="btn btn-success enable_form" data-id="{{ $memberCard['id']}}">启用</a>
                        @endif
                    @endif
                    <button class="btn btn-primary save_form">保存</button>
                    <a class="btn btn-default" href="javascript:history.go(-1);">返回</a>
                </div>
        </form>
    </div>
    <!--图片弹框开始-->
    <div class="modal in export-modal myModal-adv" id="myModal-adv" onselectstart="return false;" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" id="modal-dialog-adv">
            <form class="form-horizontal">
                <div class="modal-content content_first">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                                <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
                            </li>
                        </ul>
                        
                    </div>
                    <div class="modal-body" style="height:598px">
                        <div class="category-list-region">
                            <ul class="category-list">

                            </ul>
                            <div class='add_group'>
                                <div class="add_group_list" data-id='1'>+添加分组</div>
                                <div class="add_group_box hide">
                                    <div class='add_group_title'>添加分组</div>
                                    <input class='add_group_input' placeholder='不超过6个字' type="text" maxlength='6'  style="font-size:14px">
                                    <div class='clearfix add_group_btn'>
                                        <div class="btn_left">确定</div>
                                        <div class="btn_right">取消</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-region" style="display:none">
                            <div class="ui-search-box">
                                <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                            </div>
                        </div>
                        <div class="attachment-list-region">
                            <div class="imgData">
                                <ul class="image-list"><!--图片列表-->

                                </ul>
                                <div class="attachment-pagination">
                                    <div class= "picturePage"></div><!-- 分页 -->
                                </div>
                                <a href="##" class="ui-btn ui-btn-success js-show-upload-view" style="position: absolute; left:196px; bottom: 24px;">上传图片</a>
                            </div>
                            <!--列表中的图片个数为0的时候显示这个模态框  no隐藏数据-->
                            <div id="layerContent_right" class="no">
                                <a class="js_addImg" href="#uploadImg">+</a>
                                <p>暂无数据，点击添加</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="text-center">
                            <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                            <a class="ui-btn ui-btn-primary no">确认</a>
                        </div>
                    </div>
                </div>
                <div class="modal-content content_second">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <div class="cap_head clearfix">
                            <a class="co_38f js_prev" href="javascript:void(0);"><选择图片 </a>
                            <span>上传图片</span>
                        </div>

                    </div>
                    <div class="modal-body">
                        <div id="uploadLayerContent_botm">
                            <div id="wrapper">
                                <div id="container">
                                    <!--头部，相册选择和格式选择-->
                                    <div id="uploader">
                                        <div class="queueList">
                                            <div id="dndArea" class="placeholder">
                                                <label id="filePicker"></label>
                                                <!-- <p>或将照片拖到这里，单次最多可选300张</p> -->
                                            </div>
                                        </div>
                                        <div class="statusBar" style="display:none;">
                                            <div class="progress">
                                                <span class="text">0%</span>
                                                <span class="percentage"></span>
                                            </div>
                                            <div class="info"></div>
                                            <div class="btns">
                                                <div id="filePicker2"></div>
                                                <div class="uploadBtn">开始上传</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="text-center">
                            <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                            <a class="ui-btn ui-btn-primary no">确认</a>
                        </div>
                    </div>
                </div>
                <div class="modal-content content_third">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <ul class="module-nav modal-tab">
                            <li class="active">
                                <a href="#js-module-goods" class="js-modal-tab">我的图片</a>
                                <!-- <a href="#uploadImgLayer" class="asian" style="color: #27f;">图标库</a> -->
                            </li>
                        </ul>
                        <div class="search-region">
                            <div class="ui-search-box">
                                <input class="txt js-search-input" type="text" placeholder="搜索" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-body">
                        <ul id="iconStyleSelect">
                            <li id="style">风格:
                                <a href="##" class="selected">全部</a>
                                <a href="##">普通</a>
                                <a href="##">简约</a>
                            </li>
                            <li id="color">颜色:
                                <a href="##" class="selected">全部</a>
                                <a href="##">白色</a>
                                <a href="##">灰色</a>
                            </li>
                            <li id="type">类型:
                                <a href="##" class="selected">全部</a>
                                <a href="##">常规</a>
                                <a href="##">购物</a>
                                <a href="##">交通</a>
                                <a href="##">食物</a>
                                <a href="##">商务</a>
                                <a href="##">娱乐</a>
                                <a href="##">美妆</a>
                            </li>
                        </ul>
                        <div id="iconImgShow">
                            <ul id="iconImgSelect">
                                <li>
                                    <img class="lazy" data-original="https://upx.cdn.huisou.cn/wscphp/public/mctsource/images/bb3503203766425965b7517336df979d.png?imageView2/2/w/160/h/160/q/75/format/png" />
                                    <div class="attachment-selected no">
                                        <i class="icon-ok icon-white"></i>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div id="pageNum">
                            共<span>270</span>条，每页27条&nbsp;&nbsp;
                        </div>
                    </div>
                    <div class="modal-footer clearfix">
                        <div class="selected-count-region hide">
                            已选择<span class="js-selected-count">2</span>张图片
                        </div>
                        <div class="text-center">
                            <a class="ui-btn js-confirm" disabled="disabled">确认</a>
                            <a class="ui-btn ui-btn-primary no">确认</a>
                        </div>
                    </div>
                </div>
            </form>
            <input type="hidden" value="{{ imgUrl() }}" id='souce'/>
        </div>
    </div>
    <!--图片弹框结束-->
@endsection
@section('page_js')
    @parent
    <!-- 搜索插件 -->
    <script src="{{ config('app.source_url') }}static/js/chosen.jquery.min.js"></script>
    <!-- 图表插件 -->
    <script src="{{ config('app.source_url') }}static/js/echarts.min.js"></script>
    <!-- datePicker -->
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/moment-with-locales.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/bootstrap-datetimepicker.min.js"></script>
    <!-- webuploader上传插件引入 -->
    <script src="{{config('app.source_url')}}static/js/webuploader.js"></script>
    <script src="{{config('app.source_url')}}mctsource/js/wechat_upload.js"></script>
    <!-- 分页插件 -->
    <script src="{{config('app.source_url')}}static/js/extendPagination.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}static/js/layer/layer.js"></script>
    <!-- 表单验证 -->
    <script src="{{config('app.source_url')}}static/js/bootstrapValidator.min.js"></script>
    <!-- 当前页面js -->
    {{--<script src="{{ config('app.source_url') }}static/js/jquery.js"></script>--}}
    <script src="{{ config('app.source_url') }}mctsource/static/js/cropper.js"></script>
    <script src="{{ config('app.source_url') }}mctsource/js/cropper_img.js"></script>
    <script src="{{config('app.source_url')}}mctsource/js/member_gomuduzw.js"></script>
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}static/js/jquery.lazyload.js"></script>
    <script>
    	//懒加载
        // $("img.lazy").lazyload({
			// threshold : 200,
			// effect : "fadeIn"
        // });
       
        // var code='{{config('app.source_url')}}';
        $(function(){
            $('.is_active').click(function(){
                if($(this).val()==1){
                    $('.active_msg').show();
                }else{
                    $('.active_msg').hide();
                }
            });
        });

    </script>
@endsection