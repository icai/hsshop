@extends('shop.common.template')
@section('head_css')

@endsection
@section('main')



@endsection
@section('page_js')
	<script type="text/javascript">
		var imgUrl = "{{ imgUrl() }}";
	</script>
    <script src="{{ config('app.source_url') }}shop/js/until.js"></script>
@endsection


