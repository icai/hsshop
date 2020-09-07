@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/evaluate.css"> 
@endsection
@section('main')

<div id="app">
	<div class="page">
		<div class="p_category">
			<div class="p_catList" v-for="(catTop,catIndex) in listTop" @click="selTyype(catTop,catIndex)" v-text="catTop.name + '（' + catTop.num + '）'">全部(200)</div>
		</div>
		<div class="p_list" v-for="pList in listBot">
			<div class="list_tit">
				<img v-bind:src="pList.headimgurl" />
				<span v-text="pList.nickname">用户9527</span>
				<p v-text="pList.created_at">2017-09-08</p>
			</div>
			<div class="list_eval" v-text="pList.content">这个质量真是出乎意料的好啊，啊发顺丰的萨芬好看撒的更何况啥都不会高科技手段不合格看吧</div>
			<div class="list_img">
				<div class="evaimg" v-for="pListImg in pList.img" @click.stop="lookImg(pListImg.s_path)">
					<img v-bind:src="imgUrl + '' + pListImg.s_path" />
				</div>
				
			</div>
			<div class="list_spec">
				<span v-text="pList.spes">亮黑色</span>
			</div>
			<div class="list_reply">
				<p v-if="pList.reply.length != 0" v-html="'<span>商家回复：</span>' + pList.reply.content"><span>商家回复：</span></p>
			</div>
		</div>
	</div>
	<!--图片预览-->
	<div class="preview_picture" v-if="previewShow" @click="previewHide">
		<div class="board"></div>
		<img :src="imgUrl + '' + previewImg" :style="{top: (pageHeight-100-imgHeight)/2+'px'}" ref="img"/>
	</div>
</div>

@include('shop.common.footer')
@endsection
@section('page_js')
	<script type="text/javascript">
	    var pid="{{$pid}}";
	    var _host = "{{ config('app.source_url') }}";
	    var host ="{{ config('app.url') }}";
	    var imgUrl = "{{ imgUrl() }}";
	</script>
	<script src="{{ config('app.source_url') }}shop/js/until.js?v=1.00"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
	<!-- 当前页面js -->
	<script src="{{ config('app.source_url') }}shop/js/evaluate.js"></script>
@endsection
