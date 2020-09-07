@extends('merchants.default._layouts')




@section('page_js')
<script>
    var host = "{{ config('app.source_url') }}"
</script>
<!-- layer -->
<script src="{{ config('app.source_url') }}static/js/layer/layer.js"></script>
<!-- 私有文件 -->
<script src="{{ config('app.source_url') }}mctsource/js/aliPayPage.js"></script>
@endsection