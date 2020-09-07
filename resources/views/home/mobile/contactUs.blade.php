@extends('home.mobile.default._layouts')

@section('title',$title)

@section('css')
	<link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mobile/css/corporateCulture.css">  
@endsection



@section('content')
	<div class="content">
		<!-- 宣传图片 -->
		<div class="banner-wrap">
			<img src="{{ config('app.source_url') }}mobile/images/banner_aboutus11.jpg" />
		</div>
		<!--author 韩瑜 date 2018.7.11-->
		<!-- 菜单 -->
		<!-- <div class="menu"> 
			<ul class="menu-list">
				<li class="menu-list-wrap">
					<a href="/home/index/about">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon01.png" alt="" />
							<div class="menu-list-word">
								<h3>会搜简介</h3>
								<p>了解会搜云</p>
							</div>
						</div>
					</a>
				</li>
				<li class="menu-list-wrap">
					<a href="/home/index/growth">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon02.png" alt="" />
							<div class="menu-list-word">
								<h3>发展历程</h3>
								<p>会搜的一路走来</p>
							</div>
						</div>
					</a>
				</li>		
				<li class="menu-list-wrap">
					<a href="/home/index/culture">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon03.png" alt="" />
							<div class="menu-list-word">
								<h3>企业文化</h3>
								<p>爱与感恩的理念</p>
							</div>
						</div>
					</a>
				</li>		
				<li class="menu-list-wrap">
					<a href="/home/index/recruit">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon04.png" alt="" />
							<div class="menu-list-word">
								<h3>招贤纳士</h3>
								<p>伯乐寻找千里马</p>
							</div>
						</div>
					</a>
				</li>
				<li class="menu-list-wrap menu-now">
					<a href="/home/index/contactUs">
						<div class="menu-list-content">
							<img src="{{ config('app.source_url') }}mobile/images/about-icon05.png" alt="" />
							<div class="menu-list-word">
								<h3>联系我们</h3>
								<p>帮助您解答问题</p>
							</div>
						</div>
					</a>
				</li>	
			</ul>
		</div> -->
		<!-- 菜单end -->
		<div class="content-wrap">
			<!-- 标题 -->
			<div class="content-wrap-title">
				<h3>联系我们</h3>
			</div>
			<div class="company-name">杭州会搜科技股份有限公司</div>
			<div class="company-information">
				<p><span><img src="{{ config('app.source_url') }}mobile/images/contact-icon01.png" alt="" /></span>1299112710</p>
				<p><span><img src="{{ config('app.source_url') }}mobile/images/contact-icon02.png" alt="" /></span>13862414586</p>
				<p><span><img src="{{ config('app.source_url') }}mobile/images/contact-icon03.png" alt="" /></span>kf@huisou.cn</p>
				<p><span><img src="{{ config('app.source_url') }}mobile/images/contact-icon04.png" alt="" /></span>杭州市江干区九盛路9号东方电子商务园7幢5层</p>
			</div>			
		</div>
		<div class="mapDiv flex_star">
                <div id="mapShow"><!--插入地图--></div>
            </div>
            <div class="information_code">
        	<img src="{{ config('app.source_url') }}mobile/images/hs_Code.jpg" alt="" />
	        	<p>（长按二维码&nbsp;关注会搜云）</p>
	    </div> 
	</div>
@endsection
@section('footer')
	@include('home.mobile.default.footer')
@endsection

@section('js')
	<script src="https://api.map.baidu.com/api?v=2.0&ak=Gl9ARRgPlcASCW55a33dw5AE8URjrKRu"></script>
	<script type="text/javascript">
		$(function(){
			$(".menu").scrollLeft(200);
			//地图
            Map("mapShow",15);       //默认地点；
		});
		//地图方法；
		function Map(ele,level){
			var map = new BMap.Map(ele); // 创建地图实例    
				
			//通过经纬度坐标来初始化地图  
			var point = new BMap.Point(120.2658260000,30.3146360000); // 创建点坐标    
		//	map.centerAndZoom(point, 12); // 初始化地图，设置中心点坐标和地图级别    
			
			//通过城市名称来初始化地图  （级别可选）;
			map.centerAndZoom(point, level); 
			var marker = new BMap.Marker(point);  // 创建标注
			map.addOverlay(marker);               // 将标注添加到地图中
		
			//点击获得坐标点；
		//	var gc = new BMap.Geocoder(); 
		//	map.addEventListener('click', function(e){  
		//		//console.log(e.point); 
		//		var point = new BMap.Point(e.point.lng, e.point.lat);
		//		var marker = new BMap.Marker(point); // 创建标注    
		//		map.addOverlay(marker);
		//		
		//		var pt = e.point;
		//		gc.getLocation(pt, function(rs){
		//		var addComp = rs.addressComponents;
		//		$("#addTxt").val(addComp.province + addComp.city + addComp.district + addComp.street + addComp.streetNumber);
		//		});
		//	}); 
			
			map.enableScrollWheelZoom();   // 开启鼠标滚轮缩放    
			map.enableKeyboard(); 		   // 开启键盘控制    
			map.enableContinuousZoom();    // 开启连续缩放效果    
			map.enableInertialDragging();  // 开启惯性拖拽效果   
			
			map.addControl(new BMap.NavigationControl());  //添加标准地图控件(左上角的放大缩小左右拖拽控件)  
			map.addControl(new BMap.ScaleControl());       //添加比例尺控件(左下角显示的比例尺控件)  
		//	map.addControl(new BMap.OverviewMapControl()); // 缩略图控件  
		//	map.addControl(new BMap.MapTypeControl());     // 仅当设置城市信息时，MapTypeControl的切换功能才能可用map.setCurrentCity("北京"); （个人测试设置城市不管用，无法加载地图）   
		}
		//横向导航
	    var now_left = $('.menu-now').offset().left;
	    var now_right = now_left + $('.menu-now').width();
	    var now_width = $(window).width();
	    var now_hide = now_right - now_width
	 	if(now_right > now_width){
	 		$(".menu-list").scrollLeft(now_hide); 
	 	}
	</script>
@endsection