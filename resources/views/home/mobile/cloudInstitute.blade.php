@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
<meta name="keywords" content="手机APP开发,小程序开发公司,分销系统开发,行业APP定制开发,网络营销培训">
<meta name="description" content="会搜云是杭州会搜股份有限公司旗下知名品牌，十年专注手机APP开发，定制APP制作，APP外包服务，已为2300多家企业定制APP服务，阿凡提系统教你运营一站式服务，资讯热线：0571-87796692">
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/cloudInstitute.css">  
@endsection
<style type="text/css">
	.card_item{
		background:#fff;
		margin:10px;
		padding:20px;
		display:block;
	}
	.card_item .image{height:150px;overflow:hidden;text-align:center;}
	.card_item .image img{max-width:100%;max-height:100%;}
	.card_item .title{font-weight:600;font-size:16px;color:#333;padding:10px 0; overflow: hidden;white-space: nowrap;text-overflow: ellipsis;}
	.card_item .desc{display: -webkit-box;overflow: hidden;text-overflow: ellipsis;word-wrap: break-word;white-space: normal !important;-webkit-line-clamp: 2;-webkit-box-orient: vertical;}
	.category ul{background:#fff;}
	.banner{text-align:center;line-height:4rem;font-size:16px !important;}
	.news_button{border:1px solid #eee;padding:8px 30px;color:#f1efef;}
	.news_button:link,.news_button:visited,.news_button:hover,.news_button:active{border:1px solid #eee;padding:8px 30px;color:#f1efef;}
</style>

@section('content')
	<div class="content">
		<div class="banner">
			<!-- <p>INSTITUTE OF HUISOUYUN</p>
			<p><span class="i_s"></span><span>会搜云  学院</span><span class="i_s"></span></p> -->
			<a href="javascript:void(0);" class="news_button">会搜云资讯</a>
		</div>
		<!--nav-->
		<div class="category">
			<ul>
				@foreach($data as $key=>$val)
				<li @if($cateId == $val['id']) class="active"@endif onclick="window.location.href='/home/index/information/oneCategory/{{$val['id']}}'">{{ $val['name'] }}</li>
				@endforeach

			</ul>
		</div>
		<!--内容-->
		<!-- <div class="cloud_content" style="display: block">
			@foreach($informationData['data'] as $item)
			<div class="cloud_cont">
				<a href="/home/index/detail/{{ $item['id'] }}">
					@if($item['source'])
                        <div class="cloud_cont_l">
                            <img class="lazy" data-original="{{ config('app.source_url') }}{{$item['source'][0]['l_path']}}" width="100%" height="100%" />
                        </div>
                    @endif

					<div class="cloud_cont_r">
						<p class="cl_p1">{{ $item['title'] }}</p>
						<p class="cl_p2">{{ $item['content'] }}</p>
						<p class="cl_p3">{{ $item['auth'] }}:{{ $item['created_at'] }}</p>
					</div>
				</a>
			</div>
			@endforeach
			
		</div> -->
		<div class="card_list">
			@foreach($informationData['data'] as $item)
			<a class="card_item" href="/home/index/detail/{{ $item['id'] }}/news">
				@if($item['source'])
				<div class="image">
					<img class="lazy" data-original="{{imgUrl($item['source'][0]['l_path'])}}">
				</div>
				 @endif
				<p class="title">{{ $item['title'] }}</p>
				<p class="desc">{{ $item['content'] }}</p>
			</a>
			@endforeach
		</div>
	</div> 
	
@endsection


@section('js')
<script src="{{ config('app.source_url') }}mobile/js/cloudInstitute.js"></script>
@endsection