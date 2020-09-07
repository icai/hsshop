@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/order_0ev45wgr.css" />
<!-- 自定义layer皮肤css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/js/layer/skin/tskin/style.css" />
@endsection
@section('slidebar')
@include('merchants.order.slidebar')
@endsection
@section('middle_header')
<!-- 中间 开始 -->
<div class="middle_header">
    <!-- 二级导航三级标题 开始 -->
    <div class="third_title">维权详情</div>
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

    <!-- 隐藏域 -->
    <input type="hidden" id="refundID" value="{{$refund['id']}}"/>
    <input type="hidden" id="orderID" value="{{$order['id']}}"/>
    <input type="hidden" id="productID" value="{{$refund['pid']}}"/>

    <div class="step_progress  ">
        <div class="order_progress">
            <ul>
                <li></li>
                <li></li>
                <li></li>
                <li></li>
            </ul>
            <div class="stepIco stepIco1">1
                <div class="stepText pd">{{$refund['created_at']}}</div>
                <div class="stepText step" id="createText">买家申请维权</div>
            </div>
            <div class="stepIco stepIco2">2
                <div class="stepText pd">
                    @if ($refund['status'] == 2 || $refund['status'] == 3)
                        {{$refund['updated_at']}}
                    @endif
                </div>
                <div class="stepText step" id="checkText" >商家处理退款申请</div>
            </div>
            <div class="stepIco stepIco3">3
                <div class="stepText pd">
                    @if ($refund['status'] == 4 || $refund['status'] == 8)
                        {{$refund['updated_at']}}
                    @endif
                </div>
                <div class="stepText step" id="produceText">退款完成</div>
            </div>
        </div>
    </div>
    <div class="content-region clearfix">
        <div class="info-region">
            <h3>
                售后维权
            </h3>
            <div class="mt10">
                <img src="{{imgUrl($product['img'])}}" class="goods-img fl" alt="{{$product['title']}}">
                <span class="goods-info fl">{{$product['title']}}</span>
                <i class="clear"></i>
            </div>
            <div class="dashed-line mt10"></div>
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>
                            期望结果：
                        </th>
                        <td>
                            <span class="info-table-red">
                                @if ($refund['type'] == 0)
                                    仅退款
                                @elseif ($refund['type'] == 1)
                                    退货退款
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>退款金额：</th>
                        <td><span class="info-table-red">{{$refund['amount']}}</span>元</td>
                    </tr>
                    <tr>
                        <th>维权原因：</th>
                        <td>
                            @if ($refund['reason'] == 1)
                                配送信息错误
                            @elseif ($refund['reason'] == 2)
                                买错商品
                            @elseif ($refund['reason'] == 3)
                                不想买了
                            @elseif ($refund['reason'] == 4)
                                未按承诺时间发货
                            @elseif ($refund['reason'] == 5)
                                快递无跟踪记录
                            @elseif ($refund['reason'] == 6)
                                空包裹
                            @elseif ($refund['reason'] == 7)
                                快递一直未送达
                            @elseif ($refund['reason'] == 8)
                                缺货
                            @else
                                其他
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="dashed-line"></div>
            <table class="info-table">
                <tbody>
                    <tr>
                        <th>订单编号：</th>
                        <td><span class="blue">{{$order['oid']}}</span></td>
                    </tr>
                    <tr>
                        <th>付款时间：</th>
                        <td>
                            {{$order['created_at']}}
                        </td>
                    </tr> 
                    <tr>
                        <th>买家：</th>  
                        <td>{{$buyer['nickname']}}</td>
                          
                    </tr>
                    <tr>
                        <th>运费：</th>
                        <td>{{$order['freight_price']}}</td>
                    </tr>
                    <tr>
                        <th>实收总计：</th>
                        <td><span class="info-table-red">{{$order['pay_price']}}</span></td>
                    </tr>
                </tbody>
            </table> 
        </div>
        <div class="state-region">
            <div style="padding: 0px 0px 30px 40px;">

                @if($refund['status'] == 1 || $refund['status'] == 7)
                    <h3 class="state-title">
                        <span class="icon warm">!</span>
                        等待商家处理退款申请
                    </h3>
                    <div class="state-desc">
                        <p>收到买家仅退款申请，请尽快处理。</p>
                        <p>请在<span id="countdown" class="info-table-red"></span>处理本次退款，如逾期未处理，将自动同意退款。</p>
                    </div>
                    <div class="state-action">
                        @if ($refund['type'] == 0 || ($refund['type'] == 1 && $refund['status'] == 7))
                            <button class="fl btn-yes btn_agree_refund ml10" data-amount="{{$refund['amount']}}">同意买家退款</button>
                        @elseif ($refund['type'] == 1 && $refund['status'] == 1)
                            <button class="fl btn-yes btn_agree_return ml10" data-amount="{{$refund['amount']}}">同意买家退货</button>
                        @endif
                        <button class="fl btn-ordinary btn_refuse_refund ml10">拒绝退款申请</button>
                    </div>
                @elseif($refund['status'] == 2)
                    商家不同意退款申请
                @elseif($refund['status'] == 3)
                    商家同意退款
                @elseif($refund['status'] == 4 || $refund['status'] == 8)
                    退款完成
                @elseif($refund['status'] == 5)
                    买家取消了退款
                @elseif($refund['status'] == 6)
                    商家同意退货
                @elseif($refund['status'] == 9)
                    退款申请关闭
                @endif
            </div>
            <div class="state-remind-region">
                <div class="dashed-line"></div>
                <div class="state-remind">
                    <h4>会搜云提醒：</h4>
                    <ul>
                        @if($refund['status'] == 1)
                            <li>如果未发货，请点击同意退款给买家。</li>
                            <li>如果实际已发货，请主动与买家联系。</li>
                            <li>如果你逾期未处理，视作同意买家申请，系统将自动退款给买家。</li>
                        @elseif($refund['status'] == 2)
                            <li>买家修改退款申请后，需要你重新处理。</li>
                        @elseif($refund['status'] == 3 || $refund['status'] == 4 || $refund['status'] == 5 || $refund['status'] == 6 || $refund['status'] == 7 || $refund['status'] == 8 || $refund['status'] == 9 || $refund['status'] == 10)
                            <li>如通过“微信支付”付款订单，退款3个工作日到账。</li>
                            <li>如通过“储值余额”付款订单，退款即时到账。</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- 协助记录 -->
    <div class="assist">
        <h3 class="assist-title pr">
            协商记录
            <a class="assist-title-more" href="javascript:;" id="publish_message">点击发表留言</a>
            <div id="assist_content">
                <div class="mt20 n one">
                    <textarea maxlength="300" id="txt_message" class="form-control assist-message-textarea" placeholder="你可以在这里给买家留言，为了保证你的权益，请尽可能详细的上传留言资料。"></textarea>
                    <div class="assist-message-tips">大约还可以输入300字</div>
                </div>
                <div class="pr mt10">
                    <div id="assist_message_img_result">
                        <img src="/mctsource/images/addimg.png" width="60" height="60" style="display: inline-block;" onclick="imgCommon(3)" />
                    </div>
                    <input type="hidden" id="oid" value="{{$order['id']}}">
                    <input type="hidden" id="refundID" value="{{$refund['id']}}">
                    <input type="hidden" id="hid_img" /><!-- 存放上传图片的隐藏域 -->
                    <input type="button" id="assist_submit" class="btn-yes assist-title-submit" value="发表留言" />
                </div>
            </div> 
        </h3>

        @forelse($messages as $v)
        <p class="mt30">
            <strong>
                @if ($v['is_seller'] == 1)
                    商家
                @else
                    买家
                @endif
            </strong>
            <span class="assist-time">{{$v['created_at']}}</span>
        </p>
        <div class="dashed-line mt10"></div>
        <div class="assist-detail">
            <div class="mt5">
            @if ($v['status'] == 1)
                <p>拒绝了本次退款申请</p>
                <span>拒绝原因：</span>{{$v['content']}}
            @elseif ($v['status'] == 2)
                <p>同意退款给买家，本次维权结束</p>
                <span>退款金额：</span>{{$refund['amount']}}
            @elseif ($v['status'] == 3)
                <p>撤销本次退款，本次维权结束</p>
            @elseif ($v['status'] == 4)
                <p>修改退款申请</p>
                <span>退款金额：</span>{{$v['amount']}}<br>
                <span>退款原因：</span>
                    @if ($v['reason'] == 1)
                        配送信息错误
                    @elseif ($v['reason'] == 2)
                        买错商品
                    @elseif ($v['reason'] == 3)
                        不想买了
                    @elseif ($v['reason'] == 4)
                        未按承诺时间发货
                    @elseif ($v['reason'] == 5)
                        快递无跟踪记录
                    @elseif ($v['reason'] == 6)
                        空包裹
                    @elseif ($v['reason'] == 7)
                        快递一直未送达
                    @elseif ($v['reason'] == 8)
                        缺货
                    @else
                        其他
                    @endif
                <br><span>联系方式：</span>{{$v['phone']}}<br>
                <span>退款留言：</span>{{$v['edit_remark']}}
            @elseif ($v['status'] == 5)
                <p>已同意退款申请，等待买家退货</p>
                <span>退货地址：</span>{{$v['refund_address']['address'] . ',' . $v['refund_address']['name'] . ',' . $v['refund_address']['mobile']}}
            @elseif ($v['status'] == 6)
                <p>已退货,等待商家确认收货</p>
                <span>快递公司：</span>{{$v['express_name']}}<br>
                <span>快递单号：</span>{{$v['express_no']}}<br>
                <span>退货留言：</span>{{$v['content']}}
            @elseif ($v['status'] == 7)
                <p>退款完成</p>
            @elseif ($v['status'] == 8)
                <p>处理逾期，自动同意退款</p>
            @elseif ($v['status'] == 9)
                <p>退货逾期，退款失败</p>
            @elseif ($v['status'] == 10)
                <p>未及时处理被拒绝的退款，退款失败</p>
            @elseif ($v['status'] == 0)
                <span>留言：</span>{{$v['content']}}
            @endif
            </div>
            <div class="mt5">
                @if ($v['imgs'])
                    @foreach(explode(',', $v['imgs']) as $img)
                        <img class="refundDetail-images" src="{{imgUrl($img)}}" id="refundDetail-images" width="60" height="60" style="display: inline-block;"/>
                    @endforeach
                @endif
            </div>
        </div>
        @empty
        @endforelse

        <p class="mt30">
            <strong>买家</strong>
            <span class="assist-time">{{$refund_first['created_at']}}</span>
        </p>
        <div class="dashed-line mt10"></div>
        <div class="assist-detail">
            <p class="mt5">发起了退款申请，等待商家处理</p> 
            <p class="mt5"><span>退款原因：</span>
                @if ($refund_first['reason'] == 1)
                    配送信息错误
                @elseif ($refund_first['reason'] == 2)
                    买错商品
                @elseif ($refund_first['reason'] == 3)
                    不想买了
                @elseif ($refund_first['reason'] == 4)
                    未按承诺时间发货
                @elseif ($refund_first['reason'] == 5)
                    快递无跟踪记录
                @elseif ($refund_first['reason'] == 6)
                    空包裹
                @elseif ($refund_first['reason'] == 7)
                    快递一直未送达
                @elseif ($refund_first['reason'] == 8)
                    缺货
                @else
                    其他
                @endif
            </p>
            <p class="mt5"><span>处理方式：</span>
                @if ($refund_first['type'] == 0)
                    仅退款
                @elseif ($refund_first['type'] == 1)
                    退货退款
                @endif
            </p>
            <p class="mt5"><span>货物状态：</span>
                @if ($refund_first['order_status'] == 0)
                    未收到货
                @elseif ($refund_first['order_status'] == 1)
                    已收到货
                @endif
            </p>
            <p class="mt5"><span>退款金额：</span>{{$refund_first['amount']}}</p>
            <p class="mt5"><span>退款说明：</span>{{$refund_first['remark']}}</p>
            <p class="mt5"><span>联系电话：</span>{{$refund_first['phone']}}</p>
            <p class="mt5">
                @if ($refund_first['imgs'])
                    @foreach(explode(',', $refund_first['imgs']) as $img)
                        <img data-id='1' class="refundDetail-images" src="{{imgUrl($img)}}" width="40" height="40" style="display: inline-block;"/>
                    @endforeach
                @endif
            </p>
        </div>
    </div>

</div>

<!-- 同意买家退款开始 -->
<div class="layer-wrap none" id="div_agree_refund"> 
    <!-- 提示 -->
    <div class="t-tips"> 
        该订单通过<span style="color:#ff8676;">
            @if ($order['pay_way'] == 1)
                微信支付
            @elseif ($order['pay_way'] == 2)
                支付宝支付
            @elseif ($order['pay_way'] == 3)
                储值余额支付
            @elseif ($order['pay_way'] == 10)
                小程序支付
            @else
                其他支付方式
            @endif
        </span>付款，如果您同意退款，退款将自动原路退回至买家付款账号;
    </div>
    <!-- 包裹1 -->
    <div class="mb30">  
        <p class="mt15">
            <span>退款方式：</span>
            <span>仅退款</span>
        </p>
        <p class="mt15">
            <span>退款金额：</span>
            <span style="color:#ff8676;">￥{{$refund['amount']}} @if($refund['freight'] > 0) (包含{{$refund['freight']}}元运费) @endif</span>
        </p>
    </div>    
</div>
<!-- 同意买家退款结束 -->

<!-- 同意买家退货开始 -->
<div class="layer-wrap none" id="div_agree_return">
    <!-- 提示 -->
    <div class="t-tips">
        该订单通过<span style="color:#ff8676;">
            @if ($order['pay_way'] == 1)
                微信支付
            @elseif ($order['pay_way'] == 2)
                支付宝支付
            @elseif ($order['pay_way'] == 3)
                储值余额支付
            @elseif ($order['pay_way'] == 10)
                小程序支付
            @else
                其他支付方式
            @endif
        </span>付款，需您同意退款申请，买家才能退货给您；买家退货后您需再次确认收货后，退款将自动原路退回至买家付款账号;
    </div>
    <!-- 包裹1 -->
    <div class="mb30">
        <p class="mt15">
            <span>退款方式：</span>
            <span>退货退款</span>
        </p>
        <p class="mt15">
            <span>退款金额：</span>
            <span style="color:#ff8676;">￥{{$refund['amount']}} @if($refund['freight'] > 0) (包含{{$refund['freight']}}元运费) @endif</span>
        </p>
    </div>
</div>
<!-- 同意买家退货结束 -->

<!-- 拒绝退款申请开始 -->
<div class="layer-wrap none" id="div_refuse_refund"> 
    <!-- 提示 -->
    <div class="t-tips"> 
        建议您与买家协商后，再确定是否拒绝退款。如果您拒绝退款后，买家可以修改退款申请协议重新发起退款。也可以直接发起维权申请，将会有会搜云客服介入处理。
    </div>
    <!-- 包裹1 -->
    <div class="mb30">  
        <p class="mt15">
            <span>退款方式：</span>
            <span>
                @if ($refund['type'] == 0)
                    仅退款
                @elseif ($refund['type'] == 1)
                    退货退款
                @endif
            </span>
        </p>
        <p class="mt15">
            <span>拒绝理由：</span>
            <span style="color:#ff8676;">
                <textarea class="form-control refuse-textarea" placeholder="请填写您的拒绝理由"></textarea>
            </span> 
        </p>
    </div>    
</div>
<!-- 拒绝退款申请结束 -->

@endsection
@section('page_js')
<script type="text/javascript">
    //维权状态
    var o_status ="{{$refund['status']}}"; 
    var end_time = "{{$refundEndTimestamp}}";
    var refundID = "{{$refund['id']}}";
    var oid = "{{$order['id']}}";
    var pid = "{{$product['id']}}";
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!--<script src="{{ config('app.source_url') }}static/js/jquery.raty.min.js"></script>-->
<!-- 当前页面js -->
<script src="{{ config('app.source_url') }}mctsource/js/order_0ev45wgr.js"></script>
<!-- 订单公用文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/order_common.js"></script>
@endsection
