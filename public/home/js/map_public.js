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
