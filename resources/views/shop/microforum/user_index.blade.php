<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/> 
    <title>{{ $title or '' }}</title>
    <script src="{{ config('app.source_url') }}mobile/js/rem.js"></script>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cbase.css" />
    <!-- photoswipe 插件样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/photoswipe.css"  />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/default-skin/default-skin.css"  />

    <!-- 当前页面css -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/forum_gniblzxc.css" /> 
</head>
<body> 
	<div class="body-wrapper" id="app">
		<!-- 顶部 -->
		<div class="header">
			<div class="header-left">
				<img src="{{$userInfo['headimgurl']}}" class="head-img" />
			</div>	
			<div class="header-right">
				<p class="header-right-title">{{$userInfo['nickname']}}</p>
			</div>
		</div>
		<!-- 导航 -->
		<nav-list :list="categoriesDatas" :active-index="activeIndex" v-on:setid="setTypeId"></nav-list> 
		<!-- 中间 -->
		<div class="container">
			<!-- 内容 -->
			<div class="aricle-wrapper js_aricle_wrapper">
				<div class="aricle-list" v-for="(vo,index) in list.data">
					<div class="aricle-list-user">
						<div class="aricle-list-user-head">
							<img :src="vo.headimgurl"  />
						</div>
						<div class="aricle-list-user-info">
							<h3 class="user_name" v-html="vo.nickname"></h3>
							<p class="create_time" v-html="vo.created_at"></p>
						</div> 
					</div>
					<div>
						<div class="aricle-list-img clearfix">
							<figure v-for="(v,i) in vo.img_paths">
								<a :href="imgUrl+v.l_path" :data-size="v.img_size" :class="[vo.img_paths.length<2 ? 'aricle-img-1' : 'aricle-img-3',i%3==0 ? 'ml0':'']">
									<img :src="imgUrl+v.s_path" />
								</a>
							</figure> 
						</div>   
						<div class="aricle-list-title" v-html="vo.title"></div>
						<p class="aricle-list-content" v-html="vo.content"></p>
						<div class="aricle-list-detail">
							<a :href="'/shop/microforum/post/detail/'+vo.id">查看详情</a>  
						</div>
					</div>
					<div class="aricle-zan-wrapper">   
						<a href="javascript:;" v-if="" v-on:click="favorsedOrUnfavorsed(vo.id,index,vo.isFavors)" :class="[vo.isFavors == 0 ? 'aricle-zan-02':'aricle-zan-01','aricle-zan'] " v-html="vo.favorsCount"></a> 
						<a :href="'/shop/microforum/post/detail/'+vo.id" class="aricle-comment aricle-comment-02 ml20" v-html="vo.repliesCount"></a>
					</div>
				</div> 
				<div class="drop-refresh" v-html="postText"></div>
			</div> 
		</div> 
		<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
		    <div class="pswp__bg"></div>
		    <div class="pswp__scroll-wrap">
		        <div class="pswp__container">
		            <div class="pswp__item"></div>
		            <div class="pswp__item"></div>
		            <div class="pswp__item"></div>
		        </div>
		        <div class="pswp__ui pswp__ui--hidden">
		            <div class="pswp__top-bar">
		                <div class="pswp__counter"></div>
		                <div class="pswp__preloader">
		                    <div class="pswp__preloader__icn">
		                      <div class="pswp__preloader__cut">
		                        <div class="pswp__preloader__donut"></div>
		                      </div>
		                    </div>
		                </div>
		            </div>
		            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
		                <div class="pswp__share-tooltip"></div> 
		            </div>
		            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
		            </button>
		            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
		            </button>
		            <div class="pswp__caption">
		                <div class="pswp__caption__center"></div>
		            </div>
		        </div>

		    </div>
		</div>
	</div>
	@include("shop.common.bottomFooter")
	<script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
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
		var mid = '{{ session("mid") }}';
		if(window.location.search){
		url += '&_pid_='+ mid;
		}else{
		url += '?_pid_='+ mid;
		}
		var xcx_share_url = url;
		my.postMessage({share_title:'',share_desc:'',share_url:xcx_share_url,imgUrl:''});
	</script>
	@endif
	<script type="text/javascript">
		var mid = '{{ session("mid") }}';
		var categoriesDatas = {!!$categoriesDatas!!}; //分类
		var user_id = {!!$userInfo['id']!!};
		var id_type = {{$id_type}};
		var _host = "{{ config('app.source_url') }}";
		var imgUrl ="{{ imgUrl() }}";
	</script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/photoswipe.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/photoswipe-ui-default.min.js"></script> 
	<script src="{{ config('app.source_url') }}shop/js/user_gniblzxc.js"></script> 
</body>
</html>
