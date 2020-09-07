@extends('shop.common.marketing')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/search.css">
    
@endsection
@section('main')
   <div class="container " style="min-height: 667px;">
        <div class="header">
        </div>
        <div class="content no-sidebar">
            <div class="content-body">
                <div class="search-form">
                    <form action="{{ config('app.url') }}/shop/product/search/{{$wid}}" method="GET">
                        <input type="hidden" value="{{$wid}}" id="wid">
                        <input type="hidden" name="distribute_grade_id" value="{{request('distribute_grade_id')}}">
                        <input type="search" class="search-input" placeholder="搜索本店所有商品" name="title" value="">
                        <span class="search-icon"></span>
                    </form>
                </div>

                <ul class="js-goods-list sc-goods-list clearfix list size-3" data-size="3" style="visibility: visible;">


                    <!-- 商品区域 -->
                    <!-- 展现类型判断 -->

                </ul>



            </div>
           
        </div>
    </div>
   
    <script type="text/javascript">
        var source = '{{ config('app.source_url') }}';
        var host   = '{{ config('app.url')}}';
        var img_url = '{{ imgUrl() }}';
        var distribute_grade_id = "{{request('distribute_grade_id')}}";
    </script>
    @include('shop.common.footer')
@endsection
@section('page_js')
  
  <script src="{{ config('app.source_url') }}shop/js/search.js"></script>
@endsection
