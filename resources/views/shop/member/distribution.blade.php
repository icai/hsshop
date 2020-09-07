@extends('shop.common.marketing')
@section('head_css')
@section('main')
<style type="text/css">
    body {background-color: #fff;}
    .content .title{padding:25px;text-align:center;font-size:18px;}
    .content .step1{padding: 10px;text-align: center;}
    .content .step1 img{width: 80%;}
    .content .info1{text-align: center;padding: 10px;color: #999;}
    .content .qrcode{text-align:center;padding:10px;padding: 10px;}
    .content .info2{text-align: center;padding: 10px;color: #b8b8b8;}
    .content .step2{padding: 10px;text-align: center;font-size: 12px;}
    .content .step2 img{width: 80%;}
    .content .info3{text-align:center;padding:10px;color:#999;}
    .content .bottom_img{text-align:center;background:#fff;}
    .content .bottom_img img{width:120px;}
</style>
<div class="content">
    <div class="step1">
        <img src="{{ config('app.source_url') }}shop/images/STEP-1@2x.png">
    </div>
    <p class="info1">让小伙伴扫描二维码</p>
    <div class="qrcode">
        <img src="{{ imgUrl() }}hsshop/image/qrcodes/distribution/{{session('wid')}}/{{ session('mid') }}/qrcode.png">
    </div>
    <div class="info2">(截图保存上方二维码，微信识别即可)</div>
    <div class="step2">
        <img src="{{ config('app.source_url') }}shop/images/STEP-2@2x.png">
    </div>
    <p class="info3">成功绑定，收益任你领</p>
    <div class="bottom_img">
        <img src="{{ config('app.source_url') }}shop/images/chenggongbangding@2x.png">
    </div>
</div>

@endsection
@section('page_js')

@endsection