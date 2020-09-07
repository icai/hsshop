/**
 * 运费模块重构  之前代码全部移除
 * @data 2017/11/28
 * @author  txw
 */
var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope,$http) { 
	/**
	 * 数组引用类型处理
	 * @param  {[json]} arr [原json对象]
	 * @return {[json]}     [新json对象不引用原对象的内存地址]
	 */
    $scope.processingData = function (arr) {
      var temp = { result: arr };
      var res = JSON.parse(JSON.stringify(temp));
      return res.result;
    } 

    $scope.goUp = function(id,$event){
    	$event.stopPropagation();
    	location.href="/merchants/currency/expressSet/"+id;
    }

    $scope.list = list; //选择后的地区数据集
    $scope.json = json; //所有地区数据集
    /**
     * 处理页面数据
     */
    $scope.processingPageData = function(){
    	var json = $scope.json; 
    	var list = $scope.list;
    	hstool.load();
    	for(var h=0;h<json.length;h++){ 
    		var dr = json[h].delivery_rule?JSON.parse(json[h].delivery_rule):[];
    		var selRegions =[];
    		var showAddress = []; //处理后的地址
    		//补全城市和省份
    		for(var i=0;i<dr.length;i++){ 
    			if(dr[i].regions.length== 1 && dr[i].regions[0]==0){ //全国数据处理
    				var obj = {
    					'-2':[{
	    					id : 0,
	    					level: -1,
	    					pid:-2,
	    					title:'默认'
    					}]
    				};
    				showAddress.push(obj);
    			}else{//其他数据处理
    				var regions = dr[i].regions;
    				var pList = list['-1'];
    				for(var n=0;n<regions.length;n++){
    					var bl = false;
						for(var j=0;j<pList.length;j++){
							var jList = list[pList[j].id];
							for(var k=0;k<jList.length;k++){
								var aList = list[jList[k].id]; 
								if(typeof aList !== 'undefined'){
									for(var l=0;l<aList.length;l++){
										if(aList[l].id==regions[n]){
											if(typeof showAddress[i] === 'undefined')
												showAddress[i] ={};
											if(typeof showAddress[i][jList[k].id] ==="undefined")
												showAddress[i][jList[k].id] =[];
											showAddress[i][jList[k].id].push(aList[l]);
											bl = true;
											break;
										}
									}
								} 
								if(!bl){
									if(jList[k].id==regions[n]){
										if(typeof showAddress[i] === 'undefined')
											showAddress[i] ={};
										if(typeof showAddress[i][pList[j].id] ==="undefined")
											showAddress[i][pList[j].id] =[];
										showAddress[i][pList[j].id].push(jList[k]);
										bl = true;
										break;
									}
								}else{
									//补全城市
									if(typeof showAddress[i] === 'undefined')
											showAddress[i] ={};
									if(typeof showAddress[i][pList[j].id] ==="undefined")
										showAddress[i][pList[j].id] =[];
									showAddress[i][pList[j].id].push(jList[k]);
									break;
								}
							}
							if(bl){
								//补全省份
								if(typeof showAddress[i] === 'undefined')
									showAddress[i] ={};
								if(typeof showAddress[i]['-1'] ==="undefined")
									showAddress[i]['-1'] =[];
								showAddress[i]['-1'].push(pList[j]);
								break;
							}
						}
    				}
    			}
    			showAddress[i] =$scope.AddressRemoveAndSort(showAddress[i]);
    			showAddress[i].first_amount = dr[i].first_amount;
    			showAddress[i].first_fee = dr[i].first_fee;
    			showAddress[i].additional_amount = dr[i].additional_amount;
    			showAddress[i].additional_fee = dr[i].additional_fee;
    			
    		}
    		//处理显示地区 加入是否全省 
			for(var i=1;i<showAddress.length;i++){
				var d = showAddress[i];
				var pList = d['-1']; 
				if(typeof pList !=='undefined'){
					for(var j=0;j<pList.length;j++){
						var pid = pList[j].id;
						pList[j].isAllProvince = $scope.isAllProvince(d,pid);
					}
				} 
			}
    		json[h].showAddress =$scope.processingData(showAddress);
    	} 
		hstool.closeLoad();
    	hstool.hsload();
    }
    /**
	 * 地区数据去重并排序
	 * @return {object} [去重并排序后的数据]
	 */
	$scope.AddressRemoveAndSort = function(data){
		//省份去重  
		var res = {},hash={};
		if(typeof data !=='undefined'){ 
			for(var item in data){//去重
				if(typeof res[item] === 'undefined')
					res[item] = [];
				res[item]=$scope.arrayRemove(data[item]);
			}
			for(var item in res){ //排序
				res[item].sort($scope.arraySort("id"));
			}
		}
		return res;
	}
	/**
	 * 数组对象去重
	 * @param {array} [arr] [原数组对象]
	 * @return {array} [去重后的array]
	 */
	$scope.arrayRemove = function(arr){
		var result =[],hash={};
		for(var i=0;i<arr.length;i++){
			var id = arr[i].id;
			if(!hash[id]){
				result.push(arr[i]);
				hash[id] = true;
			}
		}
		return result;
	}
	/**
	 * 数组对象排序
	 * @param {array} [prop] [原数组对象]
	 * @return {array} [排序后的array]
	 */
	$scope.arraySort = function(prop){
	 	return function (obj1, obj2) {
	        var val1 = obj1[prop];
	        var val2 = obj2[prop];
	        if (val1 < val2) {
	            return -1;
	        } else if (val1 > val2) {
	            return 1;
	        } else {
	            return 0;
	        }            
	    } 
	}

	/**
	 * 判断是否是全省 
	 * @param {object} [list] [显示的地区数据]
	 * @param {int} [pid] [要判断的省份id]
	 * @return {bool} [是否是全省]
	 */
	$scope.isAllProvince = function(list,pid){
		var result = false; 
		var json = $scope.list;
		if(typeof json['-1'] !=='undefined'){
			var pList = json['-1'];
			for(var i=0;i<pList.length;i++){
				if(pList[i].id == pid){
					var jList = json[pList[i].id];
					var ojList = list[pList[i].id];
					var jbl = false;
					if(jList.length == ojList.length){ //包含所有城市
						jbl = true;
					}
					if(jbl){ 
						var hash = {};
						for(var j=0;j<jList.length;j++){
							var aList = json[jList[j].id];
							var oaList = list[jList[j].id];
							if(aList.length == oaList.length){ //包含所有县区
								hash[jList[j].id] = true;
							}else{
								hash[jList[j].id] = false; 
							}
						}
						var abl;
						for(var item in hash){
							if(!hash[item]){
								abl = false;
								break;
							}
						}
						if(typeof abl === 'undefined'){
							result = true;
							break;
						}else{
							result = false;
							break;
						}
					}else{ //不包含所有城市则返回不是全省
						result = false;
						break;
					}
				} 
			}
		}
		return  result;
	}

    
    $scope.processingPageData();


    /**
     * 箭头点击事件
     */
    $scope.arrowClick = function(index,id){ 
    	$scope.json[index].is_reduced = $scope.json[index].is_reduced=='0'?'1':'0';
    	var data ={'_token': $('meta[name="csrf-token"]').attr('content')};
    	$http({  
	     	url : '/merchants/currency/expressToggle/'+id,  
	      	method : 'POST',  
	      	data : data,   
	  	}).success(function(res) {  
	  		console.log(res); 
	  	});
    } 
    /**
     * 删除运费地址
     */
    $scope.removeInfo = function(id,$event){
		$event.stopPropagation();  
		showDelProver($($event.target), function(){
            //执行删除 
            var url = '/merchants/currency/expressDel/' + id;
            var data ={'_token': $('meta[name="csrf-token"]').attr('content')};
            $http({  
		     	url : url,  
		      	method : 'POST',  
		      	data : data,   
		  	}).success(function(res) {  
		  		if(res.status == 1){
		  			tipshow(res.info);
		  			setTimeout(function(){
		  				location.reload();
		  			},500); 
		  		}else{
		  			tipshow(res.info,'warn');
		  		}
		  		setTimeout(function(){
		  			$scope.isRequest = true;
		  		},100)
		  		
		  	});  
        }, '确定要删除吗?');
    }
});