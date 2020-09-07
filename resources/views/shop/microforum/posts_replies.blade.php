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
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/replies_gniblzxc.css" /> 
</head>
<body class="body-wrapper">
    <div id="app"> 
        <div class="container-full" >
            <div class="b-line reply-wrapper pr">
                <label>回复</label>
                <span class="reply-wrapper-title">{{ $name }}</span>
                <a href="javascript:history.back();" class="cancel">取消</a>
            </div>
            <div class="b-line pr"> 
                <textarea maxLength="140" class="form-content" placeholder="回复评论..." v-model="content"></textarea>
                <span class="content-explain">@{{maxLength}}字</span>
            </div> 
            
        </div>
        <input type="button" class="footer-btn-submit" v-on:click="submitForm()" v-model="submitText"  :disabled="disabled" />
    </div> 
    <script type="text/javascript">
        var wid ="{{ session('wid') }}";
        var pid = {{ $pid }};
        var rid = {{ $rid }};
        var name = "{{ $name }}";
        var imgUrl = "{{ imgUrl() }}";
        var mid = '{{ session("mid") }}';
    </script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script> 
    <script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>  
    <script src="{{ config('app.source_url') }}shop/js/replies_gniblzxc.js"></script> 
    @if($reqFrom == 'aliapp')
	<script type="text/javascript" src="https://appx/web-view.min.js"></script>
	<script>
		$.ajaxSettings = $.extend($.ajaxSettings, {
		beforeSend: beforeSend,
		complete:complete,
		});
		function complete(xhr, status){
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
