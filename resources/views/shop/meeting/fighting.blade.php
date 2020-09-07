<!DOCTYPE html>
<html class="admin responsive-320">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name=”renderer” content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>活动参与实时战况</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/base.css">
    <!-- 页面样式 -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}shop/css/fighting.css">
</head>
<body>
	<div class="app" v-clock style="background: url('{{ config('app.source_url') }}shop/images/fighting/beijing.png') top center no-repeat;">
        <div class="head_title">
            <img src="{{ config('app.source_url') }}shop/images/fighting/join.png" alt="">
        </div>
        <div class="list" style="background-image: url('{{ config('app.source_url') }}shop/images/fighting/list.png');">
            <div class="list_item" v-for="(item,index) in fightingList">
                <div>
                    <img :src="imgArr[index]" alt="">
                </div>
                <div class="text_left">@{{item.name}}</div>
                <div class="text_left">@{{item.company}}</div>
                <div>@{{item.num}}</div>
            </div>
        </div>
	</div>
	<!-- zepto -->
    <script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
    <script src="{{ config('app.source_url') }}shop/static/js/vue.min.js"></script>
	<script src="{{ config('app.source_url') }}shop/static/js/vue-resource.min.js"></script>
    <script>
        var _host = "{{ config('app.source_url') }}";
    	Vue.http.options.emulateHTTP = true;
    	var vm = new Vue({
    		el:".app",
    		data:{
                imgArr:[
                    _host+'shop/images/fighting/1.png',
                    _host+'shop/images/fighting/2.png',
                    _host+'shop/images/fighting/3.png',
                    _host+'shop/images/fighting/4.png',
                    _host+'shop/images/fighting/5.png',
                ],
    			fightingList:[]
    		},
    		created:function(){
    			getPageInfo(this)
    			//获取页面信息接口
    			function getPageInfo(that){
    				that.$http.post("/shop/meeting/fighting",{_token:$("meta[name='csrf-token']").attr("content")}).then(function(res){
	    				if(res.body.status == 1){
	    					that.fightingList = res.body.data;
	    					setTimeout(function(){//请求成功的基础上进行轮训
	    						getPageInfo(that)
	    					},3000)
	    				}
	    			},function(error){
	    				console.log("获取页面信息错误",res.body.info)
	    			})
    			}
    			
    		}
    	})
    </script>
</body>
</html>
