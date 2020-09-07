<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title></title>
		<style type="text/css">
			.z-content{text-align: center; margin: 100px auto; background: #fff; width: 800px; height: 300px; padding-top: 60px;}
			.z-content p{margin: 0; margin-top: 10px;}
			.z-content .zp{font-size: 30px;}
			.clo4b{color: #4b0; }
			.red{color: red;}
			.hide{display: none;}
		</style>
	</head>
	<body style="background: #f2f2f2;">		
		<div class="z-content hide">
			<img src="{{ config('app.source_url') }}mctsource/images/err_a.png"/>
			<p class="zp red">授权失败</p>
			<p>本页面将在<span class="z-ero">3</span>秒后自动关闭</p>
		</div>
		<div class="z-content hide">
			<img src="{{ config('app.source_url') }}mctsource/images/suc_a.png"/>
			<p class="zp clo4b">恭喜，授权成功</p>
			<p>本页面将在<span class="z-suc">3</span>秒后自动关闭</p>
		</div>
	</body>
	<script src="{{ config('app.source_url') }}/static/js/jquery-1.11.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript">
		var status = {{ $status }};
		var type   = '{{ $type }}';
		$(".z-content").eq(status).show()
		$(function(){
			var n = 2;
			function succs(){ 
				if(n==0){
                    window.location.href="/merchants/marketing/xcx/list" //update  20180710 梅杰 绑定多个小程序后统一跳转到小程序列表
//					if (type && type == 'updateauthorized') { //update by wuxiaoping 218.03.15
//						window.location.href="/merchants/marketing/xcx/list"; //#更新授权设定跳转的链接地址
//					}else {
//						window.location.href="/merchants/marketing/xcx/list"; //#设定跳转的链接地址
//					}
					 
				} 
				$(".z-suc").text(n); // 显示倒计时 
				n--;  
			} 	
			function error(){ 
				if(n==0){
                    window.location.href="/merchants/marketing/xcx/list" //update 梅杰  20180710 绑定多个小程序后统一跳转到小程序列表
//					window.location.href="/merchants/marketing/xcx/list"; //#设定跳转的链接地址
				} 
				$(".z-ero").text(n); // 显示倒计时 
				n--;  
			} 		
			if(status == 0){
				setInterval(error,1000);			
			}else{
				setInterval(succs,1000);				
			}		
		})
	</script>
</html>
