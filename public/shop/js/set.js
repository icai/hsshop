$(function(){
	var county = "<option value=''>选择地区</option>";
	/*省市区三级联动*/
	var province1 = document.getElementsByClassName('js-province')[0]	
	province1.onchange=function(){
		var index = province1.selectedIndex; // 选中索引
		var text = province1.options[index].text; // 选中文本
		var value = province1.options[index].value; // 选中值
		var dataId = value;
		var province = json[dataId];
		console.log(province)
		var city = "<option value=''>选择城市</option>";
		for(var i = 0;i < province.length;i ++){
			city += '<option value ="'+province[i]['id']+'"">'+province[i]['title']+'</option>';
		}
		$('.js-city').html(city);
		$('.js-county').html(county);
	};
	var city1 = document.getElementsByClassName('js-city')[0]
	city1.onchange=function(){
		var city1 = document.getElementsByClassName('js-city')[0]
		var city_index = city1.selectedIndex; // 选中索引
		var city_text = city1.options[city_index].text; // 选中文本
		var city_value = city1.options[city_index].value; // 选中值
		var dataId = city_value;
		var city = json[dataId];
		//console.log(json[dataId])
        var county = "<option value=''>选择地区</option>";
		for(var i = 0;i < city.length;i ++){
			county += '<option value ="'+city[i]['id']+'"">'+city[i]['title']+'</option>';
		}
		$('.js-county').html(county);
	};
	//点击保存按钮
	$(".js-save-info").click(function(){
		$.get('/shop/member/save',$('form').serialize(),function(data){
			if(data.status == 1){
				tool.tip(data.info);
				setInterval(function(){
					window.location.href = data.url;
				},2000);
			}
			tool.tip(data.info);
		});
		return false;
	})
})