@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/refundForm.css">
@endsection
@section('main')
    <div id="main" v-cloak>
       <div>
            <!---->
            <div class="weui-cells vux-no-group-title">
                <div class="vux-x-input weui-cell">
                    <div class="weui-cell__hd">
                        <!---->
                        <label for="vux-x-input-8rj5b" class="weui-label" style="width: 6em;">快递公司：</label>
                    </div>
                    <div class="weui-cell__bd weui-cell__primary">
                        <input id="vux-x-input-8rj5b" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" v-model="formData.express_name" type="text" placeholder="请填写快递公司" class="weui-input">
                    </div>
                    <div class="weui-cell__ft">
                        
                    </div>
                </div>
                <div class="vux-x-input weui-cell">
                    <div class="weui-cell__hd">
                        <label for="vux-x-input-oaqyn" class="weui-label" style="width: 6em;">快递单号：</label>
                    </div>
                    <div class="weui-cell__bd weui-cell__primary">
                        <input id="vux-x-input-oaqyn" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" type="text" v-model="formData.express_no" placeholder="请准确填写快递单号" class="weui-input">
                    </div>
                    <div class="weui-cell__ft"></div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"></div>
                    <div class="vux-cell-bd">
                        <p>
                            <label class="vux-label">退货留言</label>
                        </p>
                        <span class="vux-label-desc"></span>
                    </div>
                    <div class="weui-cell__ft vux-cell-primary vux-cell-align-left">(最多170字)
                    </div>
                </div>
                <div class="weui-cell vux-x-textarea">
                    <div class="weui-cell__hd">
                    </div>
                    <div class="weui-cell__bd">
                        <textarea  v-model="formData.remark" autocomplete="off" autocapitalize="off" autocorrect="off" spellcheck="false" placeholder="请详细填写退款说明，最多170字" rows="5" cols="30" maxlength="170" class="weui-textarea" @input="changeInput"></textarea>
                        <div class="weui-textarea-counter">
                            <span>@{{number}}</span>/170
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div id="group_2">  
                <p>
                    <span>上传凭证（最多3张）</span>
                </p>
                <div class="upload_images flex_start_v">
                    <div class="imgs_item" v-for="(item,index) in formData.imgs">
                        <img :src="imgUrl + item"/>
                        <span class="delImg" @click="delImgIndex(index)">×</span>
                    </div>
                    <div v-if="formData.imgs.length<3" class="uploaderDiv" @click="imgUploader">
                        <input id="btnUp" type="button" multiple="multiple" name="" class="absolute" value="" />
                        <img src="{{ config('app.source_url') }}shop/images/xj.png" width="80" height="80"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="submit">
            <button @click="submit()">提交申请</button>
        </div>
        <div class="vux-toast" v-if="toastShow">
            <div class="weui-mask_transparent"></div>
            <div class="weui-toast weui-toast_text" style="width: auto;">
                <p class="weui-toast__content" style="padding: 10px;" v-html="msg"></p>
            </div>
        </div>
    </div>
@include('shop.common.footer')
@endsection
@section('page_js')
    <script type="text/javascript">
        var imgUrl = "{{ imgUrl() }}";
        var wid = "{{$wid}}"
        var refundID = "{{$refundID}}"
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/ajaxupload.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/refundForm.js"></script>
@endsection
