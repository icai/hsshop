@extends('shop.common.marketing')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/showcase_with_components_3912c45fcd54e5a32071203020f85b76.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/wealth.css">
@endsection
@section('main')
<div class="my_wealth " id="app" v-cloak>
    <div class="no-sidebar">
        <div class="content-body">
            <div class="custom-level withdrawal">

                <div class="withdrawal_bg">
                    <p v-if="grade.title" class="distribute_grade" @click="showRule" v-text="grade.title"></p>
                    <div class="withdrawal_left">
                        <p class="withdrawal_info">可提现（元）</p>
                        <p class="withdrawal_money">{{$member['cash']}}</p>
                    </div>
                    <div class="withdrawal_right">
                        <p class="withdrawal_info">待提现（元）</p>
                        <p class="withdrawal_money">{{$member['amount']}}</p>
                    </div>
                    <!-- <div class="withdrawal_money">
                        <span>￥</span>
                        <span class="money">{{$member['cash']}}</span>
                    </div> -->
                    <a class="withdrawal_money_href" href="/shop/distribute/withdrawal">
                        <!-- <a href="/shop/distribute/withdrawal">提取金额 ></a> -->
                    </a>
                </div>
            </div>
            <!-- tab切换 -->
            <div :class="status?'wealth_tab':'wealth_tab tab_fix'">
                <ul class="wealth_tab_ul box_bottom_1px">
                    <li :class="nav_index==index?'active':''" v-for="(item,index) in nav_list" @click="nav_tab(index)"><span :class="index!=3?'box_right_1px':''" v-text="item">分销商品</span></li>
                </ul>
            </div>

            <!-- 分销商品 -->
            <div class="tab_item distribute_product" v-if="nav_index == 0">
                <ul>
                    <li class="box_bottom_1px" v-for="(item,index) in productList">
                        <div class="goods_img" @click="good_detail(wid,item.id)">
                            <img :src="imgUrl + item.img" alt="">
                        </div>
                        <div class="goods_info">
                            <p class="goods_title" v-text="item.title"></p>
                            <p class="goods_price">￥<span v-text="item.price">89.00</span><span style="font-weight:400;color:#cccccc;margin:0 3px">/</span><i>一级赚<span v-text="item.distribute_amount">8.9<span></i><span style="font-weight:400;color:#cccccc;margin:0 3px">/</span><i>二级赚<span v-text="item.distribute_amount_sec">8.9<span></i></p>
                            <div class="more_share flex" :style="item.skuData ? '':'justify-content:flex-end'">
                                <!-- add by 韩瑜 2018-10-30 更多规格弹窗入口-->
                                <div v-if="item.skuData" class="more_price" @click="more_btn(index)">更多规格></div>
                                <!-- end -->
                                <div class="btn share_card" @click="shareCard(item)">分享</div>
                            </div>
                        </div>
                    </li>
                </ul>
                <!--加载更多提示-->
		        <div v-if="!pageStatus" class="loadMore" v-text="moreHint"></div>
            </div>

            <!-- 我的团队 -->
            <div class="tab_item my_team" v-if="nav_index == 1">
                <div class="team_top flex">
                    <div class="my_info team_top_item">
                        <div>
                            <img :src="memberData.headimgurl" alt="">
                        </div>
                        <p v-text="memberData.nickname"></p>
                    </div>
                    <div class="low_grade_num team_top_item">
                        <div>下级总用户/累计消费金额</div>
                        <p class="text_b"><span v-text="memberData.son_num"></span>人/<span v-text="memberData.trade_amount"></span>元</p>
                    </div>
                    <div class="distribute_money team_top_item">
                        <div>累计佣金</div>
                        <p class="text_b"><span v-text="memberData.total_cash"></span>元</p>
                    </div>
                </div>
                <div class="low_grade_title row">
                    <span class="weixin_name cell">微信昵称</span>
                    <span class="all_money cell" style="padding-left:20px" :class="sort_desc==1?'desc':'asc'" @click="sortNum()">累计购次</span>
                    <span class="create_time cell">注册时间</span>
                </div>
                <div class="low_grade_content">
                    <div class="low_grade_item row box_bottom_1px" v-for="item in sonMember">
                        <div class="weixin_name cell left" style="width:45%">
                            <img :src="item.headimgurl" alt="">
                            <p v-text="item.nickname"></p>
                            <span class="sec_grade" v-if="item.son_num != 0" @click="showSon(item.id)">二级</span>
                        </div>
                        <div class="all_money cell" style="width:15%" v-text="item.buy_num"></div>
                        <div class="create_time cell" style="width:40%" v-text="item.created_at"></div>
                    </div>
                </div>
                <!--加载更多提示-->
		        <div v-if="!pageStatus" class="loadMore" v-text="moreHint"></div>
            </div>
            <!-- 订单明细 -->
            <div class="tab_item order_list" v-if="nav_index == 2">
                <div class="order_item" v-for="item in distributeOrder">
                    <div class="order_title flex">
                        <!-- <p>鞋的天空之城</p> -->
                        <span v-text="order_status[item.status]"></span>
                    </div>
                    <div class="order_detail" v-for="list in item.orderDetail">
                        <div class="goods_img">
                            <img :src="imgUrl + list.img" alt="">
                        </div>
                        <div class="goods_info">
                            <p class="goods_title" v-text="list.title"></p>
                            <div class="goods_price flex">
                                <p>￥<span v-text="list.price"></span></p>
                                <span>×<span v-text="list.num"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="order_bottom flex">
                        <p v-text="item.memberData.nickname">下单用户昵称</p>
                        <div class="">
                            <p class="final_price">实付：<i v-text="item.pay_price">198000</i><span v-text="'佣金'+item.commission"></span></p>
                            <p class="order_tip">（确认收货7天后获取）</p>
                        </div>
                    </div>
                </div>
                <!--加载更多提示-->
		        <div v-if="!pageStatus" class="loadMore" v-text="moreHint"></div>
            </div>


            <!--收支明细  -->
            <div class="tab_item balance_detail" v-if="nav_index == 3">
                <ul class="balance_tab flex box_bottom_1px">
                    <li :class="balance_index==index?'active':''" v-for="(item,index) in balance_list" @click="balanceClick(index)"><span :class="index==1?'':'box_right_1px'" v-text="item"></span></li>
                    <!-- <li><span>提现记录</span></li> -->
                </ul>
                <!-- 收益记录 -->
                <div class="income_record" v-if="balance_index == 0">
                    <div class="record_title row box_bottom_1px">
                        <span class="order_number cell">订单号</span>
                        <span class="status cell">状态</span>
                        <span class="distribute_money cell">佣金（元）</span>
                    </div>
                    <div class="record_content" v-for="item in incomeLog">
                        <div class="month" v-text="item.title"></div>
                        <div class="record_list">
                            <div class="record_item row" v-for="list in item.data">
                                <p class="order_number cell" v-text="list.order==null? '':list.order.oid"></p>
                                <p class="status cell" v-text="list.status==0?'未到账':list.status==1?'已到账':'已流失'"></p>
                                <p class="distribute_money cell" :class="list.status==-1?'run':''" v-text="list.money"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 提现记录 -->
                <div class="withdraw_record" v-else>
                    <div class="record_content" v-for="item in cashLog">
                        <div class="month" v-text="item.title"></div>
                        <div class="record_list">
                            <div class="record_item flex box_bottom_1px" v-for="list in item.data">
                                <div>
                                    <p v-text="withdraw_type[list.type]"></p>
                                    <span v-text="list.created_at"></span>
                                </div>
                                <div class="flex_right">
                                    <p v-text="list.money"></p>
                                    <span :class="list.status==3?'withdraw_error':''" v-text="withdraw_status[list.status]">提现被拒</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="share_bg" v-if="show_share" @touchmove.prevent>
                <div class="share_img">
                    <img src="{{ config('app.source_url') }}shop/images/share_bg4.png" alt="">
                </div>
                <div class="close_share" @click="closeShare()"></div>
            </div>
            <!-- <div class="share_img">
            	<img src="/shop/images/share@2x.png">
            </div> -->
            <!-- 更多规格价格弹窗-->
            <div class="more_price_model" v-if="show_more" v-clock>
                <div class='more_price_model_cl' @click='close_more_btn'></div>
                <div class="more_content">
                    <div class="more_content_top">
                        <div>价格</div>
                        <div>一级佣金</div>
                        <div>二级佣金</div>
                    </div>
                    <div class="more_content_middle">
                        <div class="middle_item" v-for="item in productList[moreIndex].skuData">
                            <div class="middle_item_l" v-text="'￥' + item.price "></div>
                            <div class="middle_item_r" v-text="'赚' + item.distribute_amount "></div>
                            <div class="middle_item_r" v-text="'赚' + item.distribute_amount_sec "></div>
                        </div>
                    </div>
                    <div class="more_content_bottom" v-if="ismore" v-clock>
                        <p>滑动查看更多</p>
                    </div>
                    <div class="moreclose" @click='close_more_btn'><img src="{{config('app.url')}}shop/images/moreclose.png" alt=""></div>
                </div>
            </div>
            <!-- 分销升级规则弹窗 -->
            <div class="grade_rule_model" v-if="show_rule" v-clock>
                <div class='rule_model_bg' @click='closeRule'></div>
                <div class="rule-content">
                    <div class="title">分销员升级规则</div>
                    <p class="rule-detail">你当前是<i v-text="grade.title"></i>，<span v-if="grade.grade.length == 2">购买<a :href="'/shop/product/search/'+ wid +'?distribute_grade_id='+ grade.grade[1].id">指定商品</a> ，可升级为<i v-text="grade.grade[1].title"></i>。</span>购买<a :href="'/shop/product/search/'+ wid +'?distribute_grade_id='+ grade.grade[0].id">指定商品</a> ，可升级为<i v-text="grade.grade[0].title"></i>。高级分销员可获得更多佣金</p>
                    <div class="btn-comfirm" @click='closeRule'>我知道了</div>
                </div>
            </div>
            <!-- 二级用户弹窗 -->
            <div class="sec_son_model" v-if="show_son" v-clock>
                <div class='sec_son_model_cl' @click='close_sec_btn'></div>
                <div class="son_content">
                    <div class="son_content_top flex">
                        <div>微信昵称</div>
                        <div class="sec_all_num" :class="orderBy=='asc'?'desc':'asc'" @click="sortSecNum()">累计购次</div>
                        <div>注册时间</div>
                    </div>
                    <div class="son_content_middle">
                        <div class="middle_item flex" v-for="item in secSon">
                            <div class="middle_item_r flex left">
                                <img :src="item.headimgurl" alt="">
                                <p v-text="item.nickname"></p>
                            </div>
                            <div class="middle_item_n flex" v-text="item.buy_num"></div>
                            <div class="middle_item_t flex" v-text="item.created_at"></div>
                        </div>
                    </div>
                    <div class="son_content_bottom" v-if="showMoreBtn" @click="moreClick()" v-clock>
                        <p>加载更多</p>
                    </div>
                    <!-- <div class="moreclose" @click='close_sec_btn'><img src="{{config('app.url')}}shop/images/moreclose.png" alt=""></div> -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('page_js')
    <script>
        var wid = "{{session('wid')}}";
        var imgUrl = "{{ imgUrl() }}";
        var host = "{{config('app.url')}}";
        var grade ={!! json_encode($grade) !!};
        var isProductEmpty = "{{ $isProductEmpty }}";
    </script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/wealth.js"></script>
    <script>
		// 微信分享
        var url = location.href.split('#').toString();
        console.log(distribute_flag);
        $.get("/home/weixin/getWeixinSecretKey",{"url": url},function(data){
            if(data.errCode == 0){
                wx.config({
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: data.data.appId, // 必填，公众号的唯一标识
                    timestamp: data.data.timestamp, // 必填，生成签名的时间戳
                    nonceStr: data.data.nonceStr, // 必填，生成签名的随机串
                    signature: data.data.signature,// 必填，签名，见附录1
                    jsApiList: [
                        'checkJsApi',
                        'onMenuShareTimeline',
                        'onMenuShareAppMessage',
                        'onMenuShareQQ',
                        'chooseWXPay'
                    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                });
            }

            wxShare();
        })
        function wxShare(){
            if(typeof app.list1.share_title !="undefined"){
                var share_title =app.list1.share_title ? app.list1.share_title : app.list1.title;
                var share_desc =app.list1.share_desc ? app.list1.share_desc : app.list1.subtitle;
                var share_img =app.list1.share_img ?imgUrl + app.list1.share_img : imgUrl + app.list1.img;
                var share_url=host+'shop/product/detail/'+wid +'/'+app.list1.id + '?_pid_='+ '{{ session("mid") }}'
                @if($reqFrom == 'aliapp')
                my.postMessage({share_title:share_title,share_desc:share_desc,share_url:share_url,imgUrl:share_img});
                @endif
                @if($reqFrom == 'wechat')
                    if(!isProductEmpty){
                        wx.ready(function () {
                            //分享到朋友圈
                            wx.onMenuShareTimeline({
                                title: share_title, // 分享标题
                                desc: share_desc, // 分享描述
                                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                                imgUrl: share_img, // 分享图标
                                success: function () {
                                    // 用户确认分享后执行的回调函数
                                },
                                cancel: function () {
                                    // 用户取消分享后执行的回调函数
                                }
                            });

                            //分享给朋友
                            wx.onMenuShareAppMessage({
                                title: share_title, // 分享标题
                                desc: share_desc, // 分享描述
                                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                                imgUrl: share_img, // 分享图标
                                type: '', // 分享类型,music、video或link，不填默认为link
                                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                                success: function () {
                                    // 用户确认分享后执行的回调函数

                                },
                                cancel: function () {
                                    // 用户取消分享后执行的回调函数
                                }
                            });

                            //分享到QQ
                            wx.onMenuShareQQ({
                                title: share_title, // 分享标题
                                desc: share_desc, // 分享描述
                                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                                imgUrl: share_img, // 分享图标
                                success: function () {
                                    // 用户确认分享后执行的回调函数
                                },
                                cancel: function () {
                                    // 用户取消分享后执行的回调函数
                                }
                            });

                            //分享到腾讯微博
                            wx.onMenuShareWeibo({
                                title: share_title, // 分享标题
                                desc: share_desc, // 分享描述
                                link: share_url, // 分享链接,将当前登录用户转为puid,以便于发展下线
                                imgUrl: share_img, // 分享图标
                                success: function () {
                                    // 用户确认分享后执行的回调函数
                                },
                                cancel: function () {
                                    // 用户取消分享后执行的回调函数
                                }
                            });
                            wx.error(function(res){
                                // config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
                            });
                        });
                    }
                @endif
            }else{
                setTimeout(function(){
                    wxShare();
                },50)
            }
        }
	</script>
    
    <!--懒加载插件-->
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.picLazyLoad.min.js"></script>
@endsection