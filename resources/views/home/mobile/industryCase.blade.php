@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
<meta name="keywords" content="餐饮APP开发,物流APP开发,医疗APP开发,电商APP开发,教育APP开发">
<meta name="description" content="会搜云为顾客提供最全最专业的电商app开发解决方案，商城APP开发解决方案，餐饮APP开发解决方案，社区APP开发解决方案，教育APP开发解决方案等，国内知名行业类APP开发品牌，欢迎咨询：0571-87796692">	
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}static/css/swiper-3.4.0.min.css"> 
<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/IndustryCase.css">  
@endsection



@section('content')
	<div class="content">
		<div class="app-type select-type none">
			<ul>
				@foreach($typeData as $key =>$val)
                <li @if($type == $key) class="active" @endif>
                    <a href="/home/index/{{ $key }}/shop">{{ $val }}</a>
                </li>
                @endforeach
			</ul>
		</div>
		<div class="all-type select-type none">
			@if($industryList)
			<ul>
				@foreach($industryList as $val)
				<li>
					<a href="/home/index/{{ $type }}/shop?industry={{ $val['id'] }}">{{ $val['name'] }}</a>
				</li>
				@endforeach
			</ul>
			@endif
		</div>
		<div class="lunbo">
		</div>			
		<!--行业类别-->
		<div class="category">
			<ul>
				<li class="app-catalog" >@if($type == 1) 会搜云新零售系统@elseif($type == 2) APP定制 @elseif($type == 3) 微信小程序 @elseif($type == 4) 微信商城 @endif<span class="arrow"></span></li>
				<li class="all-catalog" >@if($industryRow) {{ $industryRow['name'] }} @else 全部目录 @endif<span class="arrow"></span></li>
			</ul>
			<div class="center-line"></div>

		</div>
		<div class="category_sec">
			<ul>
				@forelse($caseList['data'] as $val)
				<li>
					<div class="category-content">
						<img src="{{ imgUrl() }}{{ $val['logo'] or '' }}" alt="">
						<a class="info-box" href="/home/index/caseDetails?id={{ $val['id'] }}">
							<h3>{{ $val['name'] }}</h3>
							@if($type == 1) 
							<p>会搜云新零售系统</p>
							@elseif($type == 2)
							<p>APP定制</p>
							@elseif($type == 3)
							<p>微信小程序</p>
							@elseif($type == 4)
							<p>微信商城</p>
							@endif
						</a>
					</div>
				</li>
				@empty
                <li style="line-height:330px">暂无数据</li>
                @endforelse
			</ul>
		</div>
		<nav class="list_page page-bottom">
			{{$page}}
		</nav>
	</div> 

@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection


@section('js')
<script src="{{ config('app.source_url') }}static/js/swiper-3.4.0.min.js"></script>
<script>
	var url = '{{ imgUrl() }}';
</script>
<script src="{{ config('app.source_url') }}mobile/js/IndustryCase.js"></script>
@endsection