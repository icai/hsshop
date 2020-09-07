@extends('shop.common.marketing')
@section('title', '会员卡列表')
@section('head_css')
    <link rel="shortcut icon" href="{{ config('app.source_url') }}shop/image/icon_totuan2@2x.png" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/withdrawal.css">
@endsection
@section('main')
    <div class="container">

        @if($company_pay == 1)
        <div class="weChat-withdraw">
            <ul class="weChatInner">
                <li><img src="{{ config('app.source_url') }}shop/images/weixni@2x.png"/></li>
                <li><span class="weChat-tips">微信提现</span></li>
            </ul>
        </div>
        @else
        <div>
            <h3>提现账户</h3>
            <div class="j_item">
                <a href="/shop/distribute/selectAccount">
                    @if(!empty($bank))
                        <div class="j_item_logo">
                            <img src="{{$bank['logo']}}">
                        </div>
                        <div class="j_item_content">
                            <p class="bank_name">{{$bank['bank_name']}}</p>
                            <p class="bank">尾号{{$bank['account']}}</p>
                        </div>
                        <div class="j_item_dt">
                            <div class="dt"></div>
                        </div>
                    @else
                        <div style="flex: 1; line-height: 40px;">请先选择账户</div>
                        <div class="j_item_dt" style="margin-top: 0px">
                            <div class="dt"></div>
                        </div>
                    @endif
                </a>
            </div>
        </div>
        @endif
        <h3>提现金额</h3>
        <input class="cash" type="hidden" value="{{$member['cash']}}">
        <div class="withdrawal_money">
            <div class="inputContent">
                <span>￥</span>
                <input type="text" name="money" id="money"/>
                <div class="delete">x</div>
            </div>
            <p class="tip">零钱余额{{$member['cash']}}元</p>
        </div>
        
        <a class="btn" href="javascript:void(0);">确认提现</a>
    </div>
    @include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var bank_id = "0";
                @if(!empty($bank) && $company_pay != 1)
        var bank_id = "{{$bank['id']}}";
        @endif
    </script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/until.js"></script>
    <script type="text/javascript" src="{{config('app.source_url')}}shop/js/withdrawal.js"></script>
@endsection