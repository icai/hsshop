@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refundDetailView.css">
@endsection
@section('main')
    <div id="main" v-cloak>
        <div class="container " v-if="detail != null">
            <div class="message_info">
                <div class="submitApply" v-if="detail.refund.status == 1">
                    <div class="msg_title flex_start_v">
                        <h3>申请已提交，商家处理中</h3>
                    </div>
                </div>
                <div class="agreeReturn" v-if="detail.refund.status == 6">
                    <div class="msg_title flex_start_v">
                        <h3>商家同意退货，请您及时发货</h3>
                    </div>
                </div>
                <div class="refusedRefund " v-if="detail.refund.status == 2">
                    <div class="msg_title flex_start_v">
                        <h3>商家拒绝退款</h3>
                    </div>
                    <div class="merchantMsg">
                        <p>商家留言：@{{detail.refund.seller_remark}}</p>
                    </div>
                    <p>修改申请：商家将重新处理您的退款申请，建议优先选择</p>
                    {{--<p>平台介入：平台将协商处理您的申请并维护您的权益</p>--}}
                    <p>逾期未处理：超过
                    <span v-html="time.day"></span>天
                    <span v-html="time.hour"></span>时
                    <span v-html="time.min"></span>分
                    <span v-html="time.sec"></span>秒此次申请将自动取消，您可以在有效期（确认收货后7天）内再次申请</p>
                </div>
                <div class="refusedRefund " v-if="detail.refund.status == 7">
                    <div class="msg_title flex_start_v">
                        <h3>您已发货，商家处理中</h3>
                    </div>
                </div>
                <div class="successRefund" v-if="detail.refund.status == 8 || detail.refund.status == 4">
                    <div class="msg_title flex_start_v">
                        <h3>退款成功</h3>
                    </div>
                    <p class="message-p">
                        @{{detail.refund.agree_at}}
                    </p>
                </div>
                <div class="untreated"  v-if="detail.refund.status == 9">
                    <div class="msg_title flex_start_v">
                        <h3>未及时处理，可重新申请</h3>
                    </div>
                    <p>因为您超过7天未处理，此次退款申请一自动取消；您可以在有效期（确认收货7天后）内再次申请</p>
                </div>
                <div class="refunding" v-if="detail.refund.status == 3">
                    <div  class="msg_title flex_start_v">
                        <h3 >商家通过申请，退款中</h3>
                    </div>
                    
                </div>
                <div class="refund" v-if="detail.refund.status == 10">
                    <div  class="msg_title flex_start_v">
                        <h3 >商家未及时处理，自动退款中</h3>
                    </div>
                </div>
                <div class="untreated" v-if="detail.refund.status == 5">
                    <div class="msg_title flex_start_v">
                        <h3>您已取消退款，可重新申请</h3>
                    </div>
                </div>
            </div>
            <div class="merchantMsg1" v-if="detail.refund.status == 2">
                <span>商家留言：</span>@{{detail.refund.seller_remark}}
            </div>
            <div class="refund-msg" v-if="detail.refund.status == 7" style="padding-bottom: 10px;margin-bottom:10px">
                <div>
                    <p>快递公司:@{{detail.message.express_name}}</p>
                    <p>快递单号:@{{detail.message.express_no}}</p>
                    <p>退货留言:@{{detail.message.content}}</p>
                </div>
            </div>
            <div v-if="detail.refund.status == 6" class="refund-adreess-msg">
                    <p>退货地址：@{{detail.refund.refund_address.address}}</p>
                    <p>收件人：@{{detail.refund.refund_address.name}}</p>
                    <p>联系电话：@{{detail.refund.refund_address.mobile}}</p>
                    <p class="beBig flex_start_v" @click="addressModelShow()">
                        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMTM4IDc5LjE1OTgyNCwgMjAxNi8wOS8xNC0wMTowOTowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTcgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkFGRjhCQ0VGQjIyMzExRTc4MjVCQjc1OEM5ODU4MzVDIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkFGRjhCQ0YwQjIyMzExRTc4MjVCQjc1OEM5ODU4MzVDIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QUZGOEJDRURCMjIzMTFFNzgyNUJCNzU4Qzk4NTgzNUMiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QUZGOEJDRUVCMjIzMTFFNzgyNUJCNzU4Qzk4NTgzNUMiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4bitgrAAACnUlEQVR42rSWXYhNURTHz70GoyQR0XjgwWDqptAo5Gt8Pfh68SAiL3PnRfMwPidFZtI84EGiW0oePIzvmhk1JC+EqYkymmJ8xCAmyU0Ig9+a/le7496zz9yy69fa57Tu+p+99l5r30R1dXXgGWVQCRWQglLohkfQmclkuoMYIxEhNBfqYa0nxkM4gOClYoSOwXbNX8MVuAFPoB8mwjJYCbPkdxM2Ifg2jlAJ3NJq3kEtNHtWZGk9CvPhqz0j1hV2Soae70ikDSbFEAkI2gELmB6EEXA/nU6XRQmdhDlwDVbDz2AQA7H9mL3KyvVCQlOhBrLKe1EDsSZlYwarqs0nlJHd7Ik1D5o8PlvhNzQiNtwVGgdLoA9aPEEstbs9q/qAOQMjYY0rtFDz5hjZ+QQvYvhdlq1yhVKaX40RwI78jxh+t2Vnu3VToXlPnh9YbcxUzvv1PAbS+sgE+2Dv20hZr5s+3n93Yg8I5TbsVx6hpbBFcxMbJepMxPGzvtcb+q3FG+YKPdZ8AjwNOTeI3FgHR6Dc+fp/vo7VlDrN9+8edYU3LmLYaobE8KuU7XSFchu3MUaAoW46IsaK0KEYEOrRkZ0O0zwBSlQfvlEjezHcGXIt/LwngF0Xy6Mc2J9DmLHWO9m/vrCQNcG7qqmGiDjvoSNCZJUa60vYU6h7WzP9AvvkXMxold3JarKFhLI6LVZotvxzMGWQQo2y9axuvO8qt+AXnCv6tFr/PacoR+ujqtQ4j7OCE0rfYRW0HbAU7z/nu2FtPFePqlMT3SbhV7oMv8FHaIdddve4/Y/AOzCnYLLtO8JJ37+gQMW5HhZpheWqo2fwQAeoleBv8hwMS/0G+SxOxPhf57tVo456u4q3JRn8x8FH2Ek+ayXxR4ABAPuBusRiLkrzAAAAAElFTkSuQmCC" >
                        放大
                    </p>
            </div>
            <div class="refund-msg" v-if="detail.refund.status != 4 && detail.refund.status != 5 && detail.refund.status != 8">
                <div v-if="detail.refund.status == 1">
                    <p>若商家同意：申请将达成并退款至您的支付帐号</p>
                    <p>若商家拒绝：您将有7天时间修改申请</p>
                    <p>若商家未处理：超过
                        <span v-html="time.day"></span> 天 
                        <span v-html="time.hour"></span> 时 
                        <span v-html="time.min"></span> 分 
                        <span v-html="time.sec"></span> 秒系统自动为您退款
                    </p>
                </div>
                <div v-if="detail.refund.status == 2">
                    <p>修改申请：商家将重新处理您的退款申请，建议优先选择</p>
                    <p>逾期未处理：超过
                        <span v-html="time.day"></span> 天
                        <span v-html="time.hour"></span> 时
                        <span v-html="time.min"></span> 分
                        <span v-html="time.sec"></span> 秒此次申请将自动取消，您可以在有效期（确认收货后15天）内再次申请
                    </p>
                </div>
                <div v-if="detail.refund.status == 7">
                    <p>若商家同意：申请将达成并退款至您的支付帐号</p>
                    <p>若商家拒绝：您将有7天时间修改申请</p>
                    <p>若商家未处理：超过
                        <span v-html="time.day"></span> 天
                        <span v-html="time.hour"></span> 时
                        <span v-html="time.min"></span> 分
                        <span v-html="time.sec"></span> 秒系统自动为您退款
                    </p>
                </div>
                <div v-if="detail.refund.status == 6">
                    <p>请您在
                        <span v-html="time.day"></span> 天
                        <span v-html="time.hour"></span> 时
                        <span v-html="time.min"></span> 分
                        <span v-html="time.sec"></span> 秒内退货并填写退货信息，若逾期未发货，此次退款申请关闭，请在有效期内（确认收货后15天）内您可再次申请退款。
                    </p>
                </div>
                <p v-if="detail.refund.status == 9">因为您超过7天未处理，此次退款申请一自动取消；您可以在有效期（确认收货15天后）内再次申请</p>
                <p v-if="detail.refund.status == 3">系统会在1-2天内提交微信支付处理，微信审核完成后1-3个工作日内自动原路退款至您的付款方式。若超时未收到退款，请联系平台官方客服核实。</p>
                <p v-if="detail.refund.status == 10">系统会在1-2天内提交微信支付处理，微信审核完成后1-3个工作日内自动原路退款至您的付款方式。若超时未收到退款，请联系平台官方客服核实。</p>
            </div>
            <div class="weui-cells" style="margin-top:0"  v-if="detail.refund.status == 8 || detail.refund.status == 4">
                <div class="weui-cell">
                    <div class="weui-cell__hd"></div>
                    <div class="vux-cell-bd">
                        <p>
                            <label class="vux-label">退款金额</label>
                        </p>
                        <span class="vux-label-desc"></span>
                    </div>
                    <div class="weui-cell__ft vux-cell-primary vux-cell-align-left refund-money">￥@{{detail.refund.amount}}
                    </div>
                </div>
            </div>
            
            <div class="message_list">
               <div>
                    <div class="weui-cells vux-no-group-title">
                        <a :href="'/shop/order/refundMessagesView/'+ detail.refund.wid + '/' + detail.refund.oid + '/' + detail.refund.pid + '/' + detail.refund.prop_id" class="weui-cell vux-tap-active weui-cell_access">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd vux-cell-primary">
                                <p>
                                    <label class="vux-label">协商详情</label>
                                </p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft">
                            </div>
                        </a>
                    </div>
                </div>
                <div>
                    <div class="weui-cells vux-no-group-title">
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">店铺名称</label></p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left">@{{detail.refund.shop_name}}
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">退款类型</label>
                                </p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.type == 0"> 
                                仅退款
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.type == 1"> 
                                退款退货
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">收货状态</label></p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.order_status
 == 0">未收到货</div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.order_status
 == 1">已收到货</div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">退款金额</label></p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left">￥@{{detail.refund.amount}}
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">退款原因</label></p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==0">
                                其他
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==1">
                                配送信息错误
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==2">
                                买错商品
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==3">
                                不想买了
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==4">
                                未按承诺时间发货
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==5">
                                快递无跟踪记录
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==6">
                                空包裹
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==7">
                                快递一直未送达
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left" v-if="detail.refund.reason==8">
                                缺货
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">商品名称</label>
                                </p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left">@{{detail.refund.product_title}}
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">订单编号</label>
                                </p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left">@{{detail.order.oid}}
                            </div>
                        </div>
                        <div class="weui-cell">
                            <div class="weui-cell__hd"></div>
                            <div class="vux-cell-bd">
                                <p>
                                    <label class="vux-label">申请时间</label>
                                </p>
                                <span class="vux-label-desc"></span>
                            </div>
                            <div class="weui-cell__ft vux-cell-primary vux-cell-align-left">@{{detail.refund.created_at}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="refundFun">
                    <a class="btn" :href="'/shop/order/refundAddMessageView/'+ detail.refund.wid+ '/' + detail.refund.id + '/' + detail.refund.oid + '?pid=' + detail.refund.pid">留言</a>
                    <a :href="'/shop/order/refundReturnView/'+ detail.refund.wid+ '/' + detail.refund.id" class="btn Bred" v-if="detail.refund.status == 6">填写退货信息</a>
                    <a class="btn " v-if="detail.order.status == 1 && detail.refund.status == 1" :href="'/shop/order/refundApplyView/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/1/' + detail.refund.prop_id + '?type=1'">修改申请</a>

                    <a class="btn " v-if="detail.order.status == 1 && detail.refund.status == 2" :href="'/shop/order/refundApplyView/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/1/' + detail.refund.prop_id + '?type=1'">修改申请</a>

                    <a class="btn " v-if="detail.order.status == 2 && detail.refund.status == 1" :href="'/shop/order/refundApplyType/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/1/' + detail.refund.prop_id">修改申请</a>

                    <a class="btn " v-if="detail.order.status == 2 && detail.refund.status == 2" :href="'/shop/order/refundApplyType/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/1/' + detail.refund.prop_id">修改申请</a>

                    <a class="btn " v-if="detail.order.status == 3 && detail.refund.status == 1" :href="'/shop/order/refundApplyType/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/1/' + detail.refund.prop_id">修改申请</a>

                    <a class="btn " v-if="detail.order.status == 3 && detail.refund.status == 2" :href="'/shop/order/refundApplyType/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/1/' + detail.refund.prop_id">修改申请</a>

                    <a class="btn Bred" v-if="detail.refund.status == 1 || detail.refund.status == 2 || detail.refund.status == 6 || detail.refund.status == 7" @click="delApply()">撤销申请</a>
                    <a class="btn Bred" :href="'/shop/order/refundVerifyView/'+ detail.refund.wid+ '/' + detail.refund.id" v-if="detail.refund.status == 4 || detail.refund.status == 8">钱款去向</a>

                    <a class="btn Bred" :href="'/shop/order/refundApplyView/'+ detail.refund.wid+ '/' + detail.refund.oid + '/' + detail.refund.pid + '/0/' + detail.refund.prop_id + '?type=2'" v-if="detail.refund.status == 9">重新申请</a>
                </div>
            </div>
        </div>
        <div class="weui-mask" style="" v-if="addressShow" @click="hideAddressModel()"></div>
        <div class="weui-dialog" style="" v-if="addressShow">
            <div class="img-box">
                <h4>请按照以下地址尽快发货</h4>
                <p>收件人：@{{detail.refund.refund_address.name}}</p>
                <p>联系电话：@{{detail.refund.refund_address.mobile}}</p>
                <p style="line-height: 22px;">退货地址：@{{detail.refund.refund_address.address}}</p>
            </div>
        </div>
        <div class="vux-x-dialog" v-if="confirmShow">
            <div class="weui-mask" style=""></div>
            <div class="weui-dialog" style="">
                <div class="weui-dialog__hd">
                    <strong class="weui-dialog__title">确定撤销申请？</strong>
                </div>
                <div class="weui-dialog__bd">
                    <div></div>
                </div>
                <div class="weui-dialog__ft">
                    <a href="javascript:void(0);" class="weui-dialog__btn weui-dialog__btn_default" @click="cancelApplyConfirm()">取消</a>
                    <a href="javascript:void(0);" class="weui-dialog__btn weui-dialog__btn_primary" @click="sureApplyConfirm()">确定</a>
                </div>
            </div>
        </div>
    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var wid = "{{$wid}}"
        var oid = "{{$oid}}"
        var pid = "{{$pid}}"
        var prop_id = "{{$propID}}"
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/refundDetailView.js"></script>
@endsection
