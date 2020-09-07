@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/book_index.css"  media="screen">
@endsection
@section('main')

	@if($list['data'])
	@foreach($list['data'] as $val)
	<div class="app" v-cloak >
		<a href="{{ config('app.url') }}shop/book/detail/{{ $wid }}/{{ $val['id'] }}" class="index_head">
			<div class="index_fdiv">
				<img src="{{ $val['cover_img'] }}" alt="" />
				<div class="index_fdivf">
					<p>在线预约</p>
					<p>ONLINE APPOINTMENT</p>
				</div>
			</div>
			<div class="index_ldiv">
				<p class="head_p">{{ $val['title'] }}</p>
				<p class="foot_p">{{ $val['address'] }}</p>
			</div>
		</a>
		<div class="index_footer">
			<div class="index_fleft"><img src="{{ config('app.source_url') }}shop/images/book_index1.png" alt="" /><a onclick="openModal( {{ $val['phone'] }} )">电话预订</a></div>
			<span>|</span>
			<div class="index_fright"><img src="{{ config('app.source_url') }}shop/images/book_index2.png" alt="" /><a href="//map.baidu.com/mobile/webapp/search/search/foo=bar&qt=s&wd={{ $val['address'] }}/vt=map">导航</a></div>
		</div>
	</div>
	
	@endforeach
	@else
	<div id="app" v-cloak>
		暂无匹配商家预约数
	</div>
	@endif
<div class="box"></div>
@endsection
@section('page_js')
<script type="text/javascript">
	var _host  = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
	var host   = "{{ config('app.url') }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--当前js-->
<script src="{{ config('app.source_url') }}shop/js/book_index.js"></script>
<script type="text/javascript">
	function openModal(tel){
		var content="确定拨打电话："+tel+"吗？";
		var sureTitle="确定";
		var cancleTitle="取消";
		tool.notice(1,"提示",content,sureTitle,sureBtn,cancleTitle,cancleBtn)
		function sureBtn(){
			window.location.href="tel:"+tel;
		}
		function cancleBtn(){
			$("#mask").remove();
		}
	}

//获取页面顶部被卷起来的高度
		function scrollTop() {
		    return Math.max(
		        //chrome
		    document.body.scrollTop,
		    //firefox/IE
		        document.documentElement.scrollTop);
		}
		//获取页面文档的总高度
		function documentHeight() {
		    //现代浏览器（IE9+和其他浏览器）和IE8的document.body.scrollHeight和document.documentElement.scrollHeight都可以
		    return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
		}
		function windowHeight() {
		    return (document.compatMode == "CSS1Compat") ?
		        document.documentElement.clientHeight :
		        document.body.clientHeight;
		}
	 
// 下拉加载更多
        var page = 2;
        var loading = false;  //状态标记
        var hasData = true;
        var that = this;
        window.onscroll = function () {
            if (scrollTop() + windowHeight() >= (documentHeight()-50 )) {
            	
            	
            	
                if (loading) return;
                loading = true;
                if(!hasData){
                        return;
                }
                
	           $.ajax({
				headers: {'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')},
				type:"post",
				url:"/shop/book/getBookListApi",
				data:{ page: page },
				
				
			success:function(response){
					var data=response.data
					for(var i=0;i<data.length;i++){
						var adiv=''  
					    adiv+= '<div class="app" v-cloak >'
						adiv+=	'<a href="{{ config('app.url') }}shop/book/detail/'+data[i].wid+'/'+data[i].id+'" class="index_head">'
						adiv+=	'<div class="index_fdiv">'
						adiv+=	'<img src="'+data[i].cover_img+'" alt="" />'
						adiv+=	'<div class="index_fdivf">'
						adiv+=	'<p>在线预约</p>'
						adiv+=	'<p>ONLINE APPOINTMENT</p>'
						adiv+=	'</div></div>'
						adiv+=	'<div class="index_ldiv">'
						adiv+=	'<p class="head_p">'+data[i].title+'</p>'
						adiv+=	'<p class="foot_p">'+data[i].address+'</p>'
						adiv+=	'</div></a>'
						adiv+=	'<div class="index_footer">'
						adiv+=	'<div class="index_fleft">'
						adiv+=  '<img src="{{ config('app.source_url') }}shop/images/book_index1.png" alt="" />'
						adiv+=	'<a onclick="openModal( '+data[i].phone+' )">电话预订</a>'
						adiv+=	'</div>'
						adiv+=	'<span>|</span>'
						adiv+=	'<div class="index_fright">'
						adiv+=	'<img src="{{ config('app.source_url') }}shop/images/book_index2.png" alt="" />'
						adiv+=	'<a href="//map.baidu.com/mobile/webapp/search/search/foo=bar&qt=s&wd='+data[i].address+'/vt=map">导航</a>'
						adiv+=	'</div></div></div>'
						$('.box').append(adiv)	
					}	
					page++;
	                loading = false;	
				}
				
			})
				}
        }

</script>
@endsection
