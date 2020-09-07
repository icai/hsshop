@extends('shop.common.template')
@section('head_css')
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/static/css/tspec_common.css">
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/detail_1083503c2f5268412584b1783760782d.css"  media="screen">
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}shop/css//groups_list.css">
@endsection

@section('main')
<div id="main" v-cloak>
    <div class="container">
		<div v-for='(item,index) in detail'>
			<div class='gp-list-wrap list-3'>
				<a :href="'/shop/grouppurchase/detail/'+item.id" class='gp-list-box'>
					<img :src='imgUrl+item.img' class='gp-list-datu' mode='aspectFit'>
					<div class='gp-list-item-label' v-if='item.label'><span v-text="item.label"></span></div>
					<div class='gp-list-item disfle'>
						<div class=''>
							<div v-text="item.title">item.title</div>
							<div class='gp-list-item-explain' v-text="item.subtitle">item.subtitle</div>
						</div>
						<div class='gp-list-other'>
							<div class='gp-list-sold'>已售：<span v-text="item.groups_num"></span></div>
							<div class='gp-list-price'>￥<span v-text="item.min"></span></div>
						</div>
					</div>
				</a>
			</div>
			<!--团购人员  -->
			<div class='gp-people-wrap b-line-e5e5e5' style='padding-bottom:30px;'>
				<div class='gp-people-head' :data-id='index' v-on:click="setShowGroupPeoplea(index)">
					<div class='gp-people-head-item' v-for='items in item.groupData.member' v-if="items.is_head==1">
						<span class='colonel '>团长</span>
						<img :src="items.headimgurl" class='gp-people-head-icon '>
					</div>
					<div class='gp-people-head-item' v-for='items in item.groupData.member' v-if="items.is_head!==1">
						<img :src="items.headimgurl" class='gp-people-head-icon '>
					</div>
					<div class='gp-people-head-item nobody'>?</div>
				</div>
				<div class='pad10'>
					仅剩<span class='colb08' v-text="item.groups_num-item.groupData.num"></span>个名额，
					<span>
						<span v-text="item.days"></span>:<span v-text="item.hours"></span>:<span v-text="item.minutes"></span>:<span v-text="item.seconds"></span>
					</span>后结束
				</div>
				<a :href="'/shop/grouppurchase/groupon/'+item.groupData.id+'/'+item.wid" class='participate'>一键参团</a>
			</div>
		</div>
		<!-- 摇一摇 -->
		<div bindtap='onShow' class='posfix' v-on:click='clickset'>
			<img class="yao" src="{{ config('app.source_url') }}shop/images/yao.gif">
		</div>
		<!-- 遮罩 -->
		<div class='tipmod' v-if='yaoshow'>
			<img class='modimg' src="{{ config('app.source_url') }}shop/images/yao.gif">
		</div>
		<!--拼团成员弹窗  -->
		<div class='group-people-wrap' v-if="isShowGroupPeople">
			<div class='t-mask' v-on:click='setShowGroupPeople'></div>
			<div class='group-people-content'>
				<div class='group-people-info' v-for="item in group_people" v-if="item.is_head==1">
					<div class='group-people-head'>
						<span class='colonel'>团长</span>
						<img :src="item.headimgurl" class='group-people-head-icon'>
					</div>
					<div class='group-people-username' v-text="item.nickname"></div>
					<div class='group-people-time'><span v-text="item.created_at"></span>开团</div>
				</div>
				<div class='group-people-people' v-for="item in group_people" v-if="item.is_head!==1">
					<img :src='item.headimgurl' class='group-people-people-head'>
					<div class='group-people-people-username' v-text="item.nickname"></div>
					<div class='group-people-people-time'><span v-text="item.created_at"></span>参团</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@section('page_js')
<script type="text/javascript">
	var _host = "{{ config('app.source_url') }}";
    var host ="{{ config('app.url') }}";
    var imgUrl = "{{ imgUrl() }}";
</script>
<script src="{{ config('app.source_url') }}shop/js/until.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
<script src="{{ config('app.source_url') }}shop/js/groups_list.js" ></script>
@endsection