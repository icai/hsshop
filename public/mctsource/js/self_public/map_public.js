/* author 韩瑜
 * date 2018-8-31
 * 腾讯地图方法
 */
//默认地点
var geocoder,map,marker,address,province,city,district,street,streetNumber,town,village,addressDetail,info = null;
var searchService, markers = [];
function DefaultLocation() {
	//默认地点
    //定义map变量 调用 qq.maps.Map() 构造函数   获取地图显示容器
    map = new qq.maps.Map(document.getElementById("mapShow"), {
        center: new qq.maps.LatLng(30.308460,120.259350),      // 地图的中心地理坐标。
        zoom:10
    });
    info = new qq.maps.InfoWindow({map: map});
    //点击地图事件
	qq.maps.event.addListener(map, 'click', function(event) {
		var address = new qq.maps.LatLng(event.latLng.getLat(),event.latLng.getLng());
    	geocoder = new qq.maps.Geocoder({
	        complete : function(result){
	        	console.log(result)
	        	var nowaddress = result.detail.addressComponents
	        	province = nowaddress.province
	        	city = nowaddress.city
	        	district = nowaddress.district
	        	street = nowaddress.street
	        	streetNumber = nowaddress.streetNumber
	        	town = nowaddress.town
	        	village = nowaddress.village
	        	addressDetail = province+city+district+street+streetNumber+town+village
	        	//地址信息弹窗
	        	info.open();
        		info.setContent(
        			'<div style="width:180px;height:auto;">'+
        			'地址：'+addressDetail+
            		'<hr />' +
            		'<a class="blue" style="margin-top:10px;">已设为联系地址</a>'+
            		'</div>'
        		);
        		info.setPosition(address);
        		optionaddress(addressDetail,province,city,district)
				
		        $('input[name="latitude"]').val(event.latLng.getLat());
		        $('input[name="longitude"]').val(event.latLng.getLng());
		        
		        tipshow("地理位置已确认，不要忘记保存哦");
	        }
	    });
	    //反地址解析：坐标转换为地址
	    geocoder.getAddress(address);
    });
    //搜索事件
    var latlngBounds = new qq.maps.LatLngBounds();
	searchService = new qq.maps.SearchService({
        pageIndex: 0,
        pageCapacity: 10,
        panel: document.getElementById('s_result'),
        autoExtend: true,
        complete: function(results) {
        	console.log(results)
            //设置回调函数参数
            var pois = results.detail.pois;
            for (var i = 0, l = pois.length; i < l; i++) {
                var poi = pois[i];
                //扩展边界范围，用来包含搜索到的Poi点
                latlngBounds.extend(poi.latLng);
                var marker = new qq.maps.Marker({
                    map: map,
                    position: poi.latLng
                });
                marker.addressDetail = poi.address
                marker.latLng = poi.latLng
				click(marker,poi)
            }
            //调整地图视野
            map.fitBounds(latlngBounds);
        },
        error: function() {
        }
    });
}

//清除地图上的marker
function clearOverlays(overlays) {
    var overlay;
    while (overlay = overlays.pop()) {
        overlay.setMap(null);
    }
}
//设置搜索的范围和关键字等属性
function searchKeyword() {
	var addTxt = $('#addTxt').val();
	$("#s_result").show();
    clearOverlays(markers);
    searchService.setLocation("杭州");
    searchService.search(addTxt);
}
//标记点击事件
function click(marker,poi){
	qq.maps.event.addListener(marker, 'click', function() {
		info.open();
		info.setContent(
			"<div>地址:<span>"+marker.addressDetail+"</span></div>"+
    		'<hr />' +
    		'<a class="blue" onclick="addAddress(this)" data-lat="'+poi.latLng.lat+'" data-lng="'+poi.latLng.lng+'">设为联系地址</a>'
		);
		info.setPosition(marker.latLng);
		//显示详细地址
		$("#addTxt").val(marker.addressDetail);
	});
}
//匹配位置
function optionaddress(addressDetail,province,city,district){
	//显示详细地址
	$("#addTxt").val(addressDetail);
	//三级联动匹配点击位置
	$('.js-province option').each(function(){
		if($(this).text() == province.slice(0,-1)){
			$(this).attr("selected","selected").siblings().removeAttr('selected');
			var county = "<option value=''>选择地区</option>";
			var dataId = $(this).val();
			var provincelist = json[dataId];
			var city = "<option value=''>选择城市</option>";
			for(var i = 0;i < provincelist.length;i ++){
				city += '<option value ="'+provincelist[i]['id']+'"">'+provincelist[i]['title']+'</option>';
			}
			$('.js-city').html(city);
			$('.js-county').html(county);
		}
	})
	$('.js-city option').each(function(){
		if($(this).text() == city){  
			$(this).attr("selected","selected").siblings().removeAttr('selected');
			var dataId = $(this).val();
			var citylist = json[dataId];
			var county = "<option value=''>选择地区</option>";
			for(var i = 0;i < citylist.length;i ++){
				county += '<option value ="'+citylist[i]['id']+'"">'+citylist[i]['title']+'</option>';
			}
			$('.js-county').html(county);
		}
	})
	$('.js-county option').each(function(){
		if($(this).text() == district){  
			$(this).attr("selected","selected");  
		}
	})
}
function addAddress(that){
	$(that).text("已设为联系地址");
	tipshow("地理位置已确认，不要忘记保存哦");
	$('input[name="latitude"]').val($(that).data("lat"));
    $('input[name="longitude"]').val($(that).data("lng"));
    $("#addTxt").val($(that).siblings('div').children().text());
    $("#s_result").hide();
}
