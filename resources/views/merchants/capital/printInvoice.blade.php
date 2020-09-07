@extends('merchants.default._layouts')
@section('head_css')
<!-- 当前页面css -->
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/css/printInvoice.css" />
@endsection
@section('slidebar')
@include('merchants.capital.slidebar')
@endsection
@section('middle_header')
<div class="middle_header">
    <div class="third_nav">
        <!-- 二级导航三级标题 开始 -->
        <ul class="common_nav">
            <li>
                <a href="{{ URL('/merchants/capital/fee/invoiceList') }}">已开发票</a>
            </li>
            <li class="hover">
                <a href="javascript:void(0);">开具发票</a>
            </li>
        </ul>
        <!-- 二级导航三级标题 结束 -->
    </div>
    <!-- 帮助与服务 开始 -->
    <div class="help_btn">
        <i class="glyphicon glyphicon-question-sign"></i>帮助和服务
    </div>
    <!-- 帮助与服务 结束 -->
</div>
@endsection
@section('content')
<div class="content">
    <div class="content-wrap clearfix">
        <div class="invoiceDetail pull-left">
            <p class="title">发票详情</p>
            <ul>
                <li>
                    <span class="iWrap">发票类型：</span>
                    
                    <label class="inv-type">
                        <input type="radio" name="type" value="2" checked/>
                        <span>增值税普通专用发票（专票）</span>
                    </label>
                    <label class="inv-type">
                        <input type="radio" name="type" value="1"/>
                        <span>增值税普通发票（普票）</span>
                    </label>
                </li>
                <li>
                    <span class="iWrap">发票性质：</span>
                    <label class="inv-nature">
                        <input type="radio" name="nature" value="1"/>
                        <span>纸质发票</span>
                    </label>
                    <label class="inv-nature">
                        <input type="radio" name="nature" value="2"/>
                        <span>电子发票</span> 
                    </label>
                </li>
                <li>
                    <span class="iWrap">发票抬头类型：</span>       
                    <label class="inv-headType">
                        <input type="radio" name="headType" value="1" class="companyBusiness"/>
                        <span>企业单位</span>
                    </label>
                    <label class="inv-headType">
                        <input type="radio" name="headType" value="2"/>
                        <span>个人/非企业单位</span>
                    </label>
                </li>
                <li>
                    <span class="iWrap"><span class="red">*</span>发票抬头：</span>  
                    <input type="text" name="companyName" placeholder="公司名称"/>
                </li>
                <li>
                    <span class="iWrap"><span class="red">*</span>纳税人识别号：</span>  
                    <input type="text" name="taxNumber" placeholder="纳税人识别号"/>
                </li>
                <li>
                    <span class="iWrap">发票金额：</span>
                    <span class="red price"></span><span>(根据续费订单自动生成)</span>
                </li>
                <div class="special">
                    <li>
                        <span class="iWrap"> <span class="red">*</span>开户行地址：</span>
                        <input type="text" name="depositAddress" placeholder="专票填写" />
                    </li>
                    <li>
                        <span class="iWrap"><span class="red">*</span>开户行账号：</span>
                        <input type="text" name="depositBill" placeholder="专票填写" />
                    </li>
                    <li>
                        <span class="iWrap"><span class="red">*</span>公司地址：</span>
                        <input type="text" name="companyAddress" placeholder="专票填写" />
                    </li>
                    <li>
                        <span class="iWrap">公司联系电话：</span>
                        <input type="text" name="companyTel" placeholder="专票填写" />
                    </li>
                </div>
                
            </ul>
        </div>
        <div class="consigneeDetail pull-right">
            <p class="title">收件人信息</p>
            <ul>
                <li>
                    <span class="iWrap"><span class="red">*</span>收件人：</span>
                    <input type="text" name="recceptName" />
                </li>
                <li>
                    <span class="iWrap"><span class="red">*</span>联系电话：</span>
                    <input type="text" name="recceptTel" />
                </li>
                <li>
                    <span class="iWrap"><span class="red">*</span>收件地址：</span>
                    <!--三级联动块-->
                    <div class="js-area-layout area-layout" data-area-code="" style="display:inline-block">
                        <span>
                            <select name="member_province" class="js-province address-province" id="member_province">
                                <option value=''>选择省份</option>
                                @foreach($provinceList as $pro)
                                    <option @if(!empty($address) && $address['province_id'] == $pro['id']) selected  @endif value="{{ $pro['id'] }}"> {{ $pro['title'] }}</option>
                                @endforeach
                            </select>
                        </span>
                        <span class="marl-15">
                            <select name="member_city" class="js-city address-city" id="member_city">
                                <option value=''>选择城市</option>
                                @if(!empty($address) && isset($regionList[$address['province_id']]))
                                @forelse($regionList[$address['province_id']] as $val)
                                <option @if($address['city_id'] == $val['id']) selected  @endif value="{{ $val['id'] }}"> {{ $val['title'] }}</option>
                                @endforeach
                                @endif
                            </select>
                        </span>
                        <span class="marl-15">
                            <select name="member_county" class="js-county address-county" id="member_county">
                                <option value=''>选择地区</option>
                                @if(!empty($address) && isset($regionList[$address['city_id']]))
                                @forelse($regionList[$address['city_id']] as $val)
                                <option @if($address['area_id'] == $val['id']) selected  @endif value="{{ $val['id'] }}"> {{ $val['title'] }}</option>
                                @endforeach
                                @endif
                            </select>
                        </span>
                    </div>
                </li>
                <li>    
                    <span class="iWrap"><span class="red">*</span>详细地址：</span>
                    <input type="text" name="recceptAddress" autocomplete="on"/>
                </li>
                <li class="clearfix">
                    <span class="iWrap pull-left">用户须知：</span>
                    <p class="tip pull-left">发票申请提交后，需要平台工作人员审核及核对，请您耐心等待，您也可以在已开发票查看最新的开票进展。</p>
                </li>
            </ul>
        </div>
        <p class="submit pull-right"><button class="btn" id="createREQ" type="submit">提交</button></p>
    </div>
    <div id="pop" style="display:none">
        <div class="table">
            <ul class="table-head clearfix">
                <li>时间</li>
                <li>店铺名称</li>
                <li>续费/订购</li>
                <li>服务年限</li>
                <li>费用(元)</li>
                <li>支付方式</li>
                <li>操作</li>
            </ul>
            <div class="t_body">
            </div>
        </div>
        
        <div class="page"></div>
    </div>


</div>
@endsection
@section('page_js')
<script>
    var host ="{{ config('app.url') }}";
    var json = {!! $regions_data !!};
    //去除input空格
    function replaceSpace(obj){
        obj.value = obj.value.replace(/\s/gi,'')
    }

    var _type = "{{ $address['type'] or '0' }}";
    var _default = "{{ $address['is_default'] or '0' }}";
    var _send_default = "{{ $address['is_send_default'] or '0'}}";
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/printInvoice.js"></script>
@endsection