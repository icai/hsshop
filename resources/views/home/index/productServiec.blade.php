@extends('home.base.head')
@section('head.css')
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}home/css/productServiec.css" />
@endsection
@section('content')
@include('home.base.slider')
<div class="main_content">
  <div class="padding-header"></div>
  <div class="pro-con">
    <div class="breadcrumb_nav">
      <div>
        <img src="{{ config('app.source_url') }}home/image/addr01.png">
        当前位置：<a href="{{ config('app.url') }}">首页</a>><span> 产品服务</span>
      </div>
    </div>
    <p class="prop">五大产品服务体系</p>
    <p class="desc">全渠道全场景的SaaS产品，助力企业转型布局新零售</p>
    <div class="profir">
      <div class="w260 flef">
        <img class="pro-img" src="{{ config('app.source_url') }}home/image/product-service-1.png">
        <div class="pro-info">
          <p class="probl">会搜云新零售系统</p>
          <p>直播+电商、新零售、</p>
          <p>智能拓客、企业管理</p>
          <p>社交名片</p>
        </div>
        <a class="proa" href="https://ai.huisou.cn">点击进入</a>
      </div>
      <div class="w260 flef">
        <img class="pro-img" src="{{ config('app.source_url') }}home/image/product-service-2.png">
        <div class="pro-info">
          <p class="probl">APP定制</p>
          <p>iOS开发、Android开发、</p>
          <p>APP原型设计、交互设计、</p>
          <p>界面设计</p>
        </div>
        <a class="proa" href="/home/index/customization">点击进入</a>
      </div>
      <div class="w260 flef">
        <img class="pro-img" src="{{ config('app.source_url') }}home/image/product-service-3.png">
        <div class="pro-info">
          <p class="probl">小程序定制</p>
          <p>小程序定制、小程序开发、</p>
          <p>小程序调制、小程序审核&nbsp;&nbsp;&nbsp;</p>
        </div>
        <a class="proa" href="/home/index/applet">点击进入</a>
      </div>
      <div class="w260 flef">
        <img class="pro-img" src="{{ config('app.source_url') }}home/image/product-service-4.png">
        <div class="pro-info">
          <p class="probl">微商城开发</p>
          <p>微商城开发、微商城建设、</p>
          <p>微信分销系统、客户管理系统</p>
        </div>
        <a class="proa" href="/home/index/microshop">点击进入</a>
      </div>
      <div class="w260 flef">
        <img class="pro-img" src="{{ config('app.source_url') }}home/image/product-service-5.png">
        <div class="pro-info">
          <p class="probl">微营销总裁班</p>
          <p>微信增粉秘诀、微信群运营、</p>
          <p>转型创新思维、微营销团队打造</p>
        </div>
        <a class="proa" href="/home/index/microMarketing">点击进入</a>
      </div>
    </div>
  </div>
</div>
@endsection
@section('foot.js')
<script src="{{ config('app.source_url') }}home/js/productServiec.js" type="text/javascript" charset="utf-8"></script>
@endsection