@extends('shop.common.vote_template')
@section('title', $title)
@section('head_css') 
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/LCalendar.min.css"  media="screen">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/book_bookSave.css"  media="screen">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/mobileSelect.css"  media="screen">
@endsection
@section('main')
	<div class="app" v-cloak>
		<div class="detail_banner">
			@if(isset($bookDatas['banner_img']) && $bookDatas['banner_img'])
			<img src="{{ $bookDatas['banner_img'] }}" alt="" />
			@else
			<img src="{{ config('app.source_url') }}shop/images/book_detail.png" alt="" />
			@endif
		</div>
		<div class="detail_list">
			<p>请认真填写表单</p>
			@if($formData)
			@foreach($formData as $key => $val)
			<div class="formData">
				<label for="">{{ $val['ykey'] }}</label>
				@if (isset($val['ytype'])  && $val['ytype']== 'text')
				<input type="text" placeholder="{{ $val['yval'] or ''}}" value="{{ $val['yval'] or ''}}" class="{{ $val['yclass'] or '' }}" @if((isset($val['yclass']) && $val['yclass'] == 'book_date') || (isset($val['yclass']) && $val['yclass'] == 'book_time')) readonly="readonly" style="-webkit-user-select: none;" @endif />
				@elseif(isset($val['ytype']) && $val['ytype']== 'select')
				<select name="" id="">
					@foreach($val['option'] as $item)
					<option value="{{ $item }}" @if($val['yval'] == $item) selected="selected" @endif>{{ $item }}</option>
					@endforeach
				</select>
				@endif
			</div>
			@endforeach
			@endif
			<div class="detail_lremarks">
				<h2>备注</h2><textarea class="remark" name="" rows="" cols="" placeholder="请输入备注信息">{{ $userBookDatas['remark'] or '' }}</textarea>
			</div>
		</div>
		<input class="detail_submit" type="submit" value="提交消息"/>
	</div>
	

@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
	var imgUrl = "{{ imgUrl() }}";
    var host ="{{ config('app.url') }}";
    var wid = "{{ $userBookDatas['wid'] or 0 }}";
    var bookId = "{{ $userBookDatas['book_id'] or 0 }}";
    var limit_type = "{{$bookDatas['limit_type']}}";
    var start_at = "{{date('Y-m-d',$bookDatas['start_time'])}}";
    var end_at = "{{date('Y-m-d',$bookDatas['end_time'])}}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script type="text/JavaScript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<!--当前js-->
<script src="{{ config('app.source_url') }}shop/js/LCalendar.js"></script>
<script src="{{ config('app.source_url') }}shop/js/mobileSelect.js"></script>
<script src="{{ config('app.source_url') }}shop/js/book_bookSave.js"></script>
@endsection
