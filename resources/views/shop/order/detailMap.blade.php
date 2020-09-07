<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
	<title>线路规划</title>
	<style type="text/css">  
        html{height:100%}  
        body{height:100%;margin:0px;padding:0px;position: relative;}  
        #container{height:350px;width: 100%;}
        #chooseLine{width: 100%;height: 29px;overflow: hidden;border-bottom: 1px solid #f5f5f5;}
        #chooseLine div{float: left;width: 50%;height: 29px;text-align: center;line-height: 29px;list-style: none;}
        #chooseLine div:nth-child(1){color:#4381ff;}
        #j_results{width: 100%;height:calc(100% - 380px);overflow-y:scroll; position: absolute;left:0;top:380px;display: block;}
        #g_results{width: 100%;height:calc(100% - 380px);overflow-y:scroll; position: absolute;left:0;top:380px;display: none;}
    </style>  
</head>
<body>
	<div id="container"></div>
	<div id="chooseLine">
		<div index="1">驾车路线</div>
		<div index="2">公交路线</div>
	</div>
	<div id="j_results"></div>
	<div id="g_results"></div>

	<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=1jjGj6M8T4mPm3s0Ut1RjM0dEZPVuGTN"></script>
	<script type="text/javascript" src="{{ config('app.source_url') }}/shop/static/js/zepto.min.js"></script>
	<script>
		function GetQueryString(strKey) {
			var strUrl = window.location.href;
			var intStart = strUrl.indexOf("?") + 1;
			var arrParameter = {};
			if (intStart !== 0) {
				var strQuery = strUrl.substring(intStart);
				var arrParaKey = strQuery.split("&");
				var arrSingle = [];
				var i = 0;
				for (; i < arrParaKey.length; i++) {
					arrSingle = arrParaKey[i].split("=");
					if (i === arrParaKey.length - 1) {
						var sIndex = arrSingle[1].indexOf("#");
						if (sIndex !== -1) {
							arrSingle[1] = arrSingle[1].substring(0, sIndex);
						}
					}
					arrParameter[arrSingle[0]] = arrSingle[1]
				}
			}
			return arrParameter[strKey] || "";
		}
		var lat = Number(GetQueryString('lat'));
		var lng = Number(GetQueryString('lng'));
		var olat = Number(GetQueryString('olat'));
		var olng = Number(GetQueryString('olng'));
		setTimeout(function(){
			console.log(lat,lng,olat,olng);
			var map = new BMap.Map("container");    // 创建地图实例  
			var p1 = new BMap.Point(lng,lat);		//当前坐标
			var p2 = new BMap.Point(olng,olat);		//目的地坐标
			var point = new BMap.Point(lng,lat);    // 创建点坐标  
			map.centerAndZoom(point, 15);			// 地图中心
			map.enableScrollWheelZoom(true);		// 开启鼠标滚轮缩放
			map.setCurrentCity("北京");         	// 设置地图显示的城市 此项是必须设置的
			

			//添加地图控件
			map.addControl(new BMap.NavigationControl());  //平移缩放控件 
			map.addControl(new BMap.ScaleControl());       //比例尺
			
			// 驾车路线
			var driving = new BMap.DrivingRoute(map, {    
				renderOptions: {    
					map   : map,     
					panel : "j_results",    
					autoViewport: true    
				}    
			});    
			driving.search(p1,p2);
			//公交路线
			var transit = new BMap.TransitRoute(map, {    
				renderOptions: {map: map, panel: "g_results"},
				policy:BMAP_TRANSIT_POLICY_LEAST_TRANSFER    
			});    
			transit.search(p1,p2); 
		},200)
		

		// 切换路线
		$("#chooseLine").on("click","div",function(e){
			$(this).css({color:'#4381ff'}).siblings().css({color:'#000'})
			if($(this).attr("index") == '1'){
				$("#j_results").show();
				$("#g_results").hide();
			}else{
				$("#g_results").show();
				$("#j_results").hide();
			}
		})
	</script>	
</body>
</html>