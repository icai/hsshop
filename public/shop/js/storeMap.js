var geocoder,map,marker,address,info = '';
var title = store.title;
var addressDetail = store.address;
var longitude = store.longitude;
var latitude = store.latitude;
var num = store.phone?store.phone:store.telphone
$(".storeIntro .store_item_img").attr("src","/"+store.imgs);
$(".storeIntro .store_item_title").text(addressDetail);
$(".storeIntro .tel").text("电话：" + num);
$(".storeIntro .phone").attr("href","tel:"+num);
function DefaultLocation(){
	//地图初始化
	map = new qq.maps.Map(document.getElementById("map"), {
        center: new qq.maps.LatLng(latitude,longitude),
        zoom:12
    });
	//标记
    address = new qq.maps.LatLng(latitude,longitude);
    marker = new qq.maps.Marker({
        map: map,
        position: address
    });
    //调用弹窗
	info = new qq.maps.InfoWindow({map : map});
	openinfo()
    //标记点击事件
	qq.maps.event.addListener(marker, 'click', function() {
        openinfo()
    });
    //比例尺
    var scaleControl = new qq.maps.ScaleControl({
        align: qq.maps.ALIGN.BOTTOM_LEFT,
        margin: qq.maps.Size(85, 15),
        map: map
    });
}
//弹窗
function openinfo(){
	info.open();
	info.setContent(
		'<div style="font-size:14px;">' + title +'</div>'+
		'<div class="address" style="margin:8px 0 8px 0;font-size:12px;">' + addressDetail +'</div>'+
		'<a class="goAway" href="https://apis.map.qq.com/tools/routeplan/eword='+ addressDetail +'&epointx='+latitude+'&epointy='+longitude+'?referer=myapp&key=FLIBZ-34ELI-C6WGO-5HIAO-6QBPE-KKB2D">到这里去</a>'
	);
	info.setPosition(address);
}
//查看全部
$(".lookAll").click(function(){
	var markers = []
	var latlngBounds = new qq.maps.LatLngBounds();
	$.get("/shop/store/getStoreList?tag=2",function(res){
		if(res.status != 1){
			return false;
		}
		console.log(res)
		var data = res.data.data;
		var points = [];//标注数组
		for (var i = 0;i < data.length;i ++) {
//          latlngBounds.extend(latlng);
			var marker = new qq.maps.Marker({
				map: map,
				position: new qq.maps.LatLng(data[i].latitude,data[i].longitude)
			});
			marker.title = data[i].title;
			marker.newaddress = data[i].address;
			marker.latitude = data[i].latitude;
			marker.longitude = data[i].longitude;
			marker.phone = data[i].phone;
			marker.img = data[i].file[0].s_path;
			click(marker)
		}
	});
	map.setZoom(8) 
});
function click(marker){
	qq.maps.event.addListener(marker, 'click', function() {
		info.open();
		info.setContent(
			'<div style="font-size:14px;">' + marker.title +'</div>'+
			'<div class="address" style="margin:8px 0 8px 0;font-size:12px;">' + marker.newaddress +'</div>'+
			'<a class="goAway" href="https://apis.map.qq.com/tools/routeplan/eword='+ marker.newaddress +'&epointx='+marker.position.lat+'&epointy='+marker.position.lng+'?referer=myapp&key=FLIBZ-34ELI-C6WGO-5HIAO-6QBPE-KKB2D">到这里去</a>'
		);
		info.setPosition(new qq.maps.LatLng(marker.position.lat, marker.position.lng));
		$(".storeIntro .store_item_img").attr("src","/"+marker.img);
		$(".storeIntro .store_item_title").text(marker.title);
		$(".storeIntro .tel").text("电话："+marker.phone);
		$(".storeIntro .phone").attr("href","tel:"+marker.phone);
	});
}
