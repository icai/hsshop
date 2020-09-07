<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/> 
    <title>{{ $title or '' }}</title>
    <script src="{{ config('app.source_url') }}mobile/js/rem.js"></script>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cbase.css" />  

    <!-- 当前页面css -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/prelease_gniblzxc.css" /> 
</head>
<body>
    <div class="body-wrapper" id="app">
        <div class="container-full" >
            <div class="b-line post-type" style="display: flex;flex-wrap: wrap;">
                <label>发贴</label>
                <a href="javascript:;" v-for="(item,index) in items" :data-id="item.id" :class="{active:discussions_id == item.id}" @click="selectPostType(item.id)" v-html="item.title"></a>
            </div>
            <div class="b-line">
                <input type="text" placeholder="标题" class="form-title" v-model="title" />
            </div>
            <div class="b-line pr"> 
                <textarea maxlength="500" class="form-content" placeholder="你想说点什么呢..." v-model="content"></textarea> 
                <span class="content-explain">@{{maxLength}}字</span>
            </div> 
            <div class="add-wrapper">
                <nav class="b-line" style="padding-bottom:0.2rem;">
                    <a href="javascript:;" v-on:click="switchWrapper(1)">
                        <img src="{{ config('app.source_url') }}shop/images/img-icon.png" class="add-img" />
                    </a>
                </nav>
                <!-- 添加图片 -->
                <div class="add-img-wrapper" v-show="img_wrapper">
                    <!-- 添加按钮 -->
                    <div class="iblock pr img-box" id="add_img_box">
                        <a href="javascript:;">
                            <img class="js-img-file" src="{{ config('app.source_url') }}shop/images/add_photo@2x.png" /> 
                            <input type="file" class="img-file" multiple accept="image/*" id="img_file" />
                        </a>
                    </div> 
                    <p class="iblock img-box tips">
                        最多可上传9张图片
                    </p>
                </div>
                <!-- 添加标签 -->
                <div class="add-emoji-wrapper" v-show="emoji_wrapper">
                    
                </div>
            </div>
        </div>
        <input type="button" class="footer-btn-fabu" v-on:click="submitForm()" :disabled="disabled" v-model="submitText"  />
    </div> 
    @include("shop.common.bottomFooter")
    <script type="text/javascript">
        var wid ="{{ session('wid') }}";
        var categoriesDatas = {!!$categoriesDatas!!};  
        var imgUrl = "{{ imgUrl() }}";
        var mid = '{{ session("mid") }}';
        var reqFrom = '{{ $reqFrom }}';
    </script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script> 
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>   
    <script src="{{ config('app.source_url') }}shop/js/prelease_gniblzxc.js"></script>  
    @if($reqFrom == 'aliapp')
	<script type="text/javascript" src="https://appx/web-view.min.js"></script>
	<script>
		$.ajaxSettings = $.extend($.ajaxSettings, {
		beforeSend: beforeSend,
		complete:complete,
		});
		// alert(444)
		function complete(xhr, status){
		// window.location.href="http://www.baidu.com"
		console.log(xhr.responseText)
		if(xhr.responseText.code && xhr.responseText.code == 40004){
			window.location.href = "/aliapp/authorization/login"
		}
		}
		function getQueryString(name) { 
		var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
		var r = window.location.search.substr(1).match(reg); 
		if (r != null) return unescape(r[2]); 
		return null; 
		}
		var aliToken = getQueryString('aliToken');
		if(aliToken){
		window.localStorage.setItem('aliToken',aliToken);
		}else{
		aliToken = window.localStorage.getItem('aliToken');
		}
		function beforeSend(xhr, settings) {
		xhr.setRequestHeader("aliToken", aliToken);

		}
		var url = location.href.split('#').toString();
		if(window.location.search){
		url += '&_pid_='+ mid;
		}else{
		url += '?_pid_='+ mid;
		}
		var xcx_share_url = url;
		my.postMessage({share_title:'',share_desc:'',share_url:xcx_share_url,imgUrl:''});
	</script>
	@endif
</body>
</html>
