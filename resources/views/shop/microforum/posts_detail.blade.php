<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png" />
    <title>{{ $title or '' }}</title>
    <script src="{{ config('app.source_url') }}mobile/js/rem.js"></script>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/cbase.css" />
    <!-- photoswipe 插件样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/photoswipe.css" />
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/default-skin/default-skin.css" />
    <!-- 当前页面css -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/detail_gniblzxc.css" />
</head>

<body>
    <div class="body-wrapper">
        <!-- 中间 -->
        <div class="container">
            <!-- 内容 -->
            <div class="aricle-wrapper js_aricle_wrapper">
                <div class="aricle-list">
                    <div class="aricle-list-user">
                        <div class="aricle-list-user-head">
                            <img src="{{$userInfo['headimgurl']}}" />
                        </div>
                        <div class="aricle-list-user-info">
                            <h3 class="user_name">{{$userInfo['nickname']}}</h3>
                            <p class="create_time1">{{$postsInfo['created_at']}}</p>
                        </div>
                    </div>
                    <div class="b-line"></div>
                    <div>
                        <div class="aricle-list-img clearfix">
							@for ($i=0; $i<count($postsInfo['img_paths']);$i++)
								@if (count($postsInfo['img_paths']) == 1)
								<figure>
									<a href="{{ config('app.url') }}{{$postsInfo['img_paths'][$i]['l_path']}}" data-size="{{$postsInfo['img_paths'][$i]['img_size']}}" class="aricle-img-1 ml0">
										<img src="{{ imgUrl() }}{{$postsInfo['img_paths'][$i]['s_path']}}" />
									</a>
								</figure>
								@elseif ($i%3 == 0)
								<figure>
									<a href="{{ config('app.url') }}{{$postsInfo['img_paths'][$i]['l_path']}}" data-size="{{$postsInfo['img_paths'][$i]['img_size']}}" class="aricle-img-3 ml0">
										<img src="{{ imgUrl() }}{{$postsInfo['img_paths'][$i]['s_path']}}" />
									</a>
								</figure>
								@else
								<figure>
									<a href="{{ config('app.url') }}{{$postsInfo['img_paths'][$i]['l_path']}}" data-size="{{$postsInfo['img_paths'][$i]['img_size']}}" class="aricle-img-3">
										<img src="{{ imgUrl() }}{{$postsInfo['img_paths'][$i]['s_path']}}" />
									</a>
								</figure>
								@endif
							@endfor
                        </div>
                        <div class="aricle-list-title">{{$postsInfo['title']}}</div>
                        <div class="aricle-list-content">{!!$postsInfo['content']!!}</div> 
                    </div>
                    <div class="aricle-zan-wrapper">
						@if ($userInfo['isFavored'])
                        <a href="javascript:;" class="aricle-zan aricle-zan-01" data-id="{{$postsInfo['id']}}" data-isf="{{$userInfo['isFavored']}}" >{{$postsInfo['favorCount']}}</a>
						@else
                        <a href="javascript:;" class="aricle-zan aricle-zan-02" data-id="{{$postsInfo['id']}}" data-isf="{{$userInfo['isFavored']}}" >{{$postsInfo['favorCount']}}</a>
						@endif
                        <a href="javascript:;" class="aricle-comment aricle-comment-02 ml20">{{count($repliesInfo)}}</a>
                    </div>
                    <div class="b-line mt20"></div>
                </div> 
                <!-- 回复内容 -->
				@foreach($repliesInfo as $v)
					<div class="aricle-list">
						<div class="aricle-list-user" style="border:none;">
							<div class="aricle-list-user-head">
								<img src="{{$v['headimgurl']}}" />
							</div>
							<div class="aricle-list-user-info @if ($v['is_owner'] == 1) aricle-delete @else aricle-reply @endif" data-id="{{$v['id']}}" data-pid="{{$postsInfo['id']}}" data-rid="{{$v['id']}}" data-name="{{$v['nickname']}}">
								<h3 class="user_name">
									{{$v['nickname']}}
									<p class="create_time">{{$v['created_at']}}</p>
								</h3>
								<div class="mt10">
									@if ($v['parent_id'] > 0) 回复{{$v['parent_user_name']}}:@endif{{$v['content']}}
								</div>
							</div>
						</div>   
					</div>  
				@endforeach 
            </div>
        </div>
        <!-- 评论输入框 -->
        <div class="comment-outer">
            <div style="height:0;">&nbsp;</div>
            <input type="text" class="comment-outer-txt"  />
            <input type="button" class="comment-outer-btn" data-pid="{{$postsInfo['id']}}" data-rid="" value="发送" />
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
    <script type="text/javascript">  
        var imgUrl = "{{ imgUrl() }}";
        var mid = '{{ session("mid") }}';
    </script>
    <script src="{{ config('app.source_url') }}shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/photoswipe.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/PhotoSwipe-4.1.2/photoswipe-ui-default.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/js/detail_gniblzxc.js"></script>
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
		// console.log(xhr)
		xhr.setRequestHeader("aliToken", aliToken);
		// var context = settings.context
		// console.log(44224)
		// if (settings.beforeSend.call(context, xhr, settings) === false ||
		//     triggerGlobal(settings, context, 'ajaxBeforeSend', [xhr, settings]) === false)
		//   return false

		// triggerGlobal(settings, context, 'ajaxSend', [xhr, settings])
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
