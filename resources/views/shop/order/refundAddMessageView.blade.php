<!--退款添加留言页-->
@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base1.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css/header.css"/>
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/trade_cf2f229bbe8369499fbee3c9ca4251c5.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/safeguard_1c5f2b2c598b88d2eceffd92bf496cfe.css">
@endsection
@section('main')
    <div class="container " style="min-height: 650px;">
        <div class="content safe-message">
            <div class="text-area">
                <textarea id="content" class="js-text font-size-12" placeholder="为了保证您的权益，请尽可能详细的提交留言信息。"></textarea>
            </div>

            <div class="block-item item-picture-upload">
                <label class="vertical-top">图片举证</label>
                <div class="picture-detail">
                    <p class="c-gray-dark font-size-12" style="margin-bottom: 10px;">可上传3张图片</p>
                    <p class="js-input-container multi-uploader uploader" style="display: flex;" >
                    <span class="js-add-picture add-wrapper picture-wrapper">
                        
                    </span>
                    </p>
                </div>
            </div>
            <div class="action-container">
                <button id="submitMessage" class="js-submit btn btn-block btn-green">提交</button>
                <a href="javascript:window.history.back();" class="btn btn-block btn-white">取消</a>
            </div>
        </div>  
    </div>
    <form name="form1" id="form1" enctype="multipart/form-data" method="post" style="opacity: 0;">
        <input type="file" class="add-picture" id="upload_img" name="file" style="width: 1px;height: 1px;">
    </form>
@endsection
@section('page_js')
    <script type="text/javascript">
        var wid = '{{$wid}}';
        var oid = '{{$oid}}';
        var rid = '{{$refundID}}';
        var imgUrl = "{{ imgUrl() }}";
        var pid = '{{request('pid') ?? 0}}';
        var propID = '{{$propID}}';
    </script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/order_refundAddMessage.js"></script>
@endsection
