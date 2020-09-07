@extends('merchants.default._layouts')
@section('head_css')
    <!-- 当前页面css -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/currency_smsConf.css" />
@endsection
@section('slidebar')
    @include('merchants.currency.slidebar')
@endsection
@section('middle_header')

@endsection
@section('content')
    <div class="content">
        <div class="div weiXin">
            <div class="title weiXin_title">
                <div class="title_left">
                    <b>微信支付商户证书说明：</b>
                    <p>微信支付接口中，涉及资金回滚的接口会使用到商户证书，包括退款、撤销接口。商家在申请微信支付成功后，收到的相应邮件后，可以按照指引下载API证书，也可以按照以下路径下载：<a href="https://pay.weixin.qq.com">微信商户平台</a>-->账户设置-->API安全-->证书下载 。
                        这里需要上传：apiclient_cert.pem apiclient_key.pem 文件。
                    </p>
                </div>
            </div>
        </div>
        <div class="div weiXin">
            <form class="uploadForm" enctype="multipart/form-data">
                <b>公众号商户证书上传</b>
                @if($api_flag == 1)
                <b>（已上传 如有需要请重新上传）</b>
                    @else
                    <b>（未上传）</b>
                @endif
                <br>
                <div class="row1">
                    <span>apiclient_cert.pem文件:</span>
                    <div class="input_shade"><span class="shade_button">选择文件</span><b>未选择文件</b></div>
                    <input type="file" name="file_cert" id="file_cert" accept=".pem">
                </div>
                <div class="row1">
                    <span>apiclient_key.pem文件:</span>
                    <div class="input_shade"><span class="shade_button">选择文件</span><b>未选择文件</b></div>
                    <input type="file" name="file_key" id="file_key" accept=".pem">
                    <input type="hidden" name="type" value="1">
                </div>
                <button type="button" class="btn btn-primary certSubBtn">提交</button>
            </form>
        </div>
        {{--<div class="div weiXin">--}}
            {{--<form class="uploadForm" enctype="multipart/form-data">--}}
                {{--<b>小程序商户证书上传</b>--}}
                {{--@if($mini_flag == 1)--}}
                    {{--<b>（已上传 如有需要请重新上传）</b>--}}
                {{--@else--}}
                    {{--<b>（未上传）</b>--}}
                {{--@endif--}}
                {{--<br>--}}
                {{--<div class="row1">--}}
                    {{--<span>apiclient_cert.pem文件:</span>--}}
                    {{--<div class="input_shade"><span class="shade_button">选择文件</span><b>未选择文件</b></div>--}}
                    {{--<input type="file" name="file_cert" id="file_cert" accept=".pem">--}}
                {{--</div>--}}
                {{--<div class="row1">--}}
                    {{--<span>apiclient_key.pem文件:</span>--}}
                    {{--<div class="input_shade"><span class="shade_button">选择文件</span><b>未选择文件</b></div>--}}
                    {{--<input type="file" name="file_key" id="file_key" accept=".pem">--}}
                {{--</div>--}}
                {{--<input type="hidden" name="type" value="2">--}}
                {{--<button type="button" class="btn btn-primary certSubBtn">提交</button>--}}
            {{--</form>--}}
        {{--</div>--}}

    </div>

@endsection
@section('page_js')
    <!--特殊按钮js文件-->
    <script type="text/javascript">
        $('.certSubBtn').on('click', function(){
            $.ajax({
                url: '/merchants/currency/cert',
                type: 'POST',
                cache: false,
                data: new FormData($(this).closest('.uploadForm')[0]),
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(res) {
                    if(res.status == 1){
                        tipshow(res.info,'info');
                        setTimeout(function(){
                            window.location.reload();
                        },2000);
                    }else{
                        tipshow(res.info,'warn');
                    }
                },
                error:function(){

                }
            })
        });
        //上传文件显示 && 报错
        $(".row1").on("change","input[type='file']",function(){
            var filePath=$(this).val();
            console.log($(this).siblings('.input_shade').children('b'))
            if(filePath.indexOf("pem")!=-1){
                $(this).siblings('.input_shade').children('b').html("");
                var arr=filePath.split('\\');
                var fileName=arr[arr.length-1];
                $(this).siblings('.input_shade').children('b').html(fileName);
            }else{
                $(this).siblings('.input_shade').children('b').html("");
                $(this).siblings('.input_shade').children('b').html("您上传文件类型有误").show();
                return false 
            }
        })
    </script>
@endsection