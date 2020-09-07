@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/news.css">
@endsection

@section('content')
	<div class="content">
		<div class="banner"></div>
		<div class="nav_content">
			<ul class="nav_list">
				<a href="/home/index/news" class="nav_list_item @if(request('Pid') == '') nav_border @endif"><li>最新广告</li></a>
				@foreach($type as $val)
				<a href="/home/index/news?Pid={{ $val['id'] }}" class="nav_list_item @if(request('Pid') == $val['id']) nav_border @endif"><li>{{ $val['name'] }}</li></a>
				@endforeach
			</ul>
		</div>
		<div class="zixun_list">
			@forelse($information['data'] as $val)
			<div class="zixun_list_item">
				<a href="/home/index/newsDetail/{{$val['id']}}/news">
					@if(isset($val['source']) && $val['source'])
					<div class="zixun_list_item_img">
						<img src="{{ imgUrl() }}{{ $val['source']['l_path'] }}" alt="" />
					</div>
					@else
                        <div class="zixun_list_item_img">
                            <img width="240px" height="220px" src=""/>
                        </div>	
                    @endif
					<div class="zixun_list_item_r">
						<h3>{{$val['title']}}</h3>
						<p>{{$val['created_at']}}</p>
					</div>
				</a>
			</div>
			@empty
			<div class="zixun_list_no">
				暂无数据
			</div>
		</div>
		@endforelse
		<!-- <nav class="list_page page-bottom">
			{{$page}}
		</nav> -->
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection
@section('js')
<script>
	var imgUrl = '{{ imgUrl() }}';
	var pid = "{{ request('Pid') ?? 0 }}";
</script>
	<script src="{{ config('app.source_url') }}mobile/js/news.js"></script>
@endsection