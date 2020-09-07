//一维数组去重方法
Array.prototype.unique3 = function(){
 	var res = [];
 	var json = {};
 	for(var i = 0; i < this.length; i++){
  		if(!json[this[i]]){
   			res.push(this[i]);
   			json[this[i]] = 1;
  		}
 	}
 	return res;
}

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
    /**
     * 地址选择需求 level 0.省 1.城市 2.区县
     * 1.选择省份 
     *     ① 显示省份信息
     * 2.选择城市
     *     ① 显示省份和城市信息
     * 3.选择市区
     *     ① 显示省份、城市、区域信息
     * 4.选择省份 + 选择城市
     *     ① 显示省份
     *     ② 显示省份、城市
     * 5.选择省份 + 选择城市 + 选择区（县） 
     *     ① 显示省份
     *     ② 显示省份、城市
     *     ③ 显示省份、城市、区域信息
     * 6.选择过的区域在新增区域选择框中不再显示
     * 7.编辑选择区域时显示已选的的数据
     */
	$scope.list = json;          //地址原数据
	$scope.oList = $scope.processingData(json);         //地址可选数据
	$scope.tempoList = {};		//临时可选数据（作用：打开地址选择弹窗时保持现有数据，点击取消时用于还原原有数据）
	$scope.sList = [];          //地址已选数据
	$scope.tempsList = []; 		//临时已选数据（作用：打开地址选择弹窗时保持现有数据，点击取消时用于还原原有数据）
	$scope.isShowAddress = false; //是否显示地址弹窗
	$scope.addressIndex = 0;   
	$scope.isRequest = true;  //提交表单是否完成 
	$scope.first_amount = 1;  //首件([个、Kg])
	$scope.first_fee = 0;     //运费（元）
	$scope.additional_amount = 0; //续件([个、Kg])
	$scope.additional_fee = 0;   //续费（元）
	/**
	 * 处理快递公司数据
	 */
	$scope.handleExpress = function(){ 
		var res = [];
		for(item in express){
			res.push(express[item]);
		}
		return res;
	} 
	$scope.data = {
		id:'',
		title:'',
		delivery_id:0,
		billing_type:0,
		sort:50,
		delivery_rule:''
	}; 
	$scope.express = $scope.handleExpress();  

	//显示或关闭地址弹窗
	$scope.setShowAddress = function(add){ 
		if(typeof add !='undefined')
			$scope.addressIndex = $scope.sList.length;
		$scope.isShowAddress = !$scope.isShowAddress;
		$scope.tempoList = $scope.processingData($scope.oList);
		$scope.tempsList = $scope.processingData($scope.sList);
	}
	/**
	 * 设置是否选中
	 * @param {[object]} obj  [选择的数据对象]
	 * @param {[int]} type [选择的地区级别 0.省，1.城市 2.县、区]
	 * @param {obj} [param] [pid 省份id cid 城市id did 县区id]
	 */
	$scope.setActive = function(obj,type,param){
		if(typeof obj.active == 'undefined'){
			obj.active = true; 
		}else{
			if(obj.active == true){
				obj.active = false; 
			}else{
				obj.active = true;  
			}
		}
		$scope.setAllActive(obj,type,param);
	}
	/**
	 * 设置是否打开
	 * @param {[object]} obj  [选择的数据对象]
	 * @param {[int]} type [选择的地区级别 0.省，1.城市 2.县、区]
	 */
	$scope.setOpen = function(obj,type){
		if(typeof obj.isOpen == 'undefined')
			obj.isOpen = true;
		else
			obj.isOpen = !obj.isOpen;
	} 
	/**
	 * 所有省份下的城市和县、区选中或不选中
	 * @param {[object]} obj   [地区id]
	 * @param {[int]} type [地区级别 0.省，1.城市 2.县、区]
	 */
	$scope.setAllActive = function(obj,type,param){
		switch(type){
			case 0: //省份：所有该省份下的城市和县区全部选中或不选中
				var d = $scope.oList[obj.id];   
				for(var i=0;i<d.length;i++){
					d[i].active = obj.active; 
					var dd = $scope.oList[d[i].id]; 
					if(typeof dd !=='undefined'){
						for(var j=0;j<dd.length;j++){
							dd[j].active = obj.active; 
						}
					} 
				} 
				break;
			case 1://城市：所有该城市的县区全部选中或不选中
				var d = $scope.oList[obj.id];
				if(typeof d !=='undefined'){
					for(var i=0;i<d.length;i++){
						d[i].active = obj.active; 
					} 
				}
				
				$scope.selProvince(obj,type,param);
				break;
			case 2://县、区
				//城市选中或不选中
				$scope.selCity(obj,type,param);
				$scope.selProvince(obj,type,param);
				//省份选中或不选中                  
				break;
		}
	}
	/**
	 * 选中省份
	 * @param {[object]} obj   [地区id]
	 * @param {[int]} type [地区级别 0.省，1.城市 2.县、区]
	 */
	$scope.selProvince = function(obj,type,param){
		var list = $scope.oList[param.pid];
		var bl = false;  
		for(var i=0;i<list.length;i++){ 
			if(list[i].active){
				bl = true;
			}else{
				bl = false;
				break;
			}
		} 
		list = $scope.oList['-1'];  
		for(var i=0;i<list.length;i++){
			if(list[i].id == param.pid){
				list[i].active = bl;
				break;
			}
		} 
		return bl;
	}
	/**
	 * 选中城市
	 * @param {[object]} obj   [地区id]
	 * @param {[int]} type [地区级别 0.省，1.城市 2.县、区]
	 */
	$scope.selCity = function(obj,type,param){
		var list = $scope.oList[obj.pid]
		var bl = false,is_bl = false;  
		for(var i=0;i<list.length;i++){ 
			if(list[i].active){
				is_bl = true;
				bl = true;
			}else{
				bl = false;
				break;
			}
		} 
		list = $scope.oList[param.pid];  
		for(var i=0;i<list.length;i++){
			if(list[i].id == obj.pid){
				list[i].active = bl;  
				break;
			}
		} 
		return bl;
	}

	/**
	 * 添加按钮点击事件
	 */
	$scope.addAddress = function(){
		var tempList = $scope.processingData($scope.oList);
		var resList = $scope.sList[$scope.addressIndex] || {};
		//重组数据
		for(var i=0;i<tempList['-1'].length;i++){ 
			if(tempList['-1'][i].active){ 
				if(typeof resList['-1'] =='undefined')
					resList['-1'] = []; 
				resList['-1'].push(tempList['-1'][i]);
			}
			var jList = tempList[tempList['-1'][i].id] || []; 
			for(var j=0;j<jList.length;j++){
				if(jList[j].active){ 
					if(typeof resList[tempList['-1'][i].id] =='undefined'){
						resList[tempList['-1'][i].id] = [];
					} 		
					resList[tempList['-1'][i].id].push(jList[j]);
					if(typeof resList['-1'] =='undefined'){
						resList['-1'] = [];
					} 
					resList['-1'].push(tempList['-1'][i]); 
				}
				var aList = tempList[jList[j].id] || []; 
				for(var n=0;n<aList.length;n++){
					if(aList[n].active){
						if(typeof resList[jList[j].id] =='undefined')
							resList[jList[j].id] = [];
						resList[jList[j].id].push(aList[n]); //插入县区
						if(typeof resList[tempList['-1'][i].id] =='undefined')
							resList[tempList['-1'][i].id] = [];
						resList[tempList['-1'][i].id].push(jList[j]);//插入城市
						if(typeof resList['-1'] =='undefined')
							resList['-1'] = [];
						resList['-1'].push(tempList['-1'][i]);//插入省市
					}
				}
			}
		}	  
		$scope.sList[$scope.addressIndex] = $scope.AddressRemoveAndSort(resList); 
		$scope.oList = $scope.removeData(tempList);
		
	}
	/**
	 * 移除数据
	 * @param  {[object]} tempList [数据集]
	 * @return {[object]}          [移除完数据的数据]
	 */
	$scope.removeData = function(tempList){
		for(item in tempList){
			var arr = tempList[item];
			for(var i=0;i<arr.length;i++){
				if(arr[i].active){
					arr.splice(i,1);
					i--;
				}
			}
		} 
		return tempList;
	}
	/**
	 *  移除省份 
	 * 可选数据
	 * 1.插入该省份到指定位置
	 * 2.插入该省份下所有城市
	 * 3.插入该省份下所有城市的所有县区
	 * 已选数据
	 * 1.移除该省份
	 * 2.移除该省份下所有城市
	 * 3.移除该省份下所有城市的所有县区 
	 */
	$scope.removeProvinceSelect = function(obj){
		var tempList = $scope.sList[$scope.addressIndex];
	 	for(var i=0;i<tempList['-1'].length;i++){
			if(obj.pobj.id == tempList['-1'][i].id){
				$scope.oList['-1'].push(tempList['-1'][i]);
				var jList = tempList[tempList['-1'][i].id]; 
				for(var j=0;j<jList.length;j++){
					$scope.oList[tempList['-1'][i].id].push(jList[j]);
					var aList = tempList[jList[j].id]; 
					if(typeof aList !=='undefined'){
						for(var k=0;k<aList.length;k++){
							$scope.oList[jList[j].id].push(aList[k]);
							$scope.sList[$scope.addressIndex][jList[j].id].splice(k,1);
							k--;
						}
					} 
					$scope.sList[$scope.addressIndex][tempList['-1'][i].id].splice(j,1);
					j--;
				}
				$scope.sList[$scope.addressIndex]['-1'].splice(i,1);
				break;
			} 
		} 
		$scope.oList = $scope.AddressRemoveAndSort($scope.oList); 
	}
	/**
	 *  移除城市
	 * 可选数据
	 * 1.插入本身
	 * 	 ①.如果不存在该城市的省份则插入该省份数据
	 * 2.插入所有县区
	 * 已选数据
	 * 1.移除本身
	 *   ①.如果是最后一个城市移除省份
	 * 2.移除所有县区 
	 */
	$scope.removeCitySelect = function(obj){
		var tempList = $scope.sList[$scope.addressIndex];
		var jList = tempList[obj.pobj.id];  
		for(var j=0;j<jList.length;j++){
			if(obj.cobj.id == jList[j].id){
				$scope.oList[obj.pobj.id].push(jList[j]);
				var aList = tempList[jList[j].id];
				if(typeof aList !=='undefined'){
					for(var k=0;k<aList.length;k++){
						$scope.oList[jList[j].id].push(aList[k]);
						$scope.sList[$scope.addressIndex][jList[j].id].splice(k,1);
						k--;
					}
				}   
				$scope.oList['-1'].push(obj.pobj);  
				//如果是最后一个城市移除省份
				if(jList.length==1){
					for(var i=0;i<tempList['-1'].length;i++){
						if(tempList['-1'][i].id == obj.pobj.id){
							$scope.sList[$scope.addressIndex]['-1'].splice(i,1);
							break;
						}
					}
				}
				$scope.sList[$scope.addressIndex][obj.pobj.id].splice(j,1); 
				break;
			} 
		}
		$scope.oList = $scope.AddressRemoveAndSort($scope.oList); 
	}
	/**
	 *  移除县、区
	 * 可选数据
	 * 1.插入本身
	 * 	①.如果不存在该县区的城市则插入该城市
	 * 	②.如果不存在该城市的省份则插入该省份
	 * 已选数据
	 * 1.移除本身
	 * 	①.如果是最后一个区县移除城市
	 * 	②.如果是最后一个城市移除省份
	 */
	$scope.removeAreaSelect = function(obj){
		var tempList = $scope.sList[$scope.addressIndex];
		var aList = tempList[obj.cobj.id];
		var jList = tempList[obj.pobj.id];
		for(var i=0;i<aList.length;i++){
			if(aList[i].id == obj.dobj.id){
				$scope.oList['-1'].push(obj.pobj); //加入省
				$scope.oList[obj.pobj.id].push(obj.cobj); //加入市 
				$scope.oList[obj.cobj.id].push(obj.dobj); //加入县、区
				$scope.sList[$scope.addressIndex][obj.cobj.id].splice(i,1); //移除本身  
				//如果是最后一个区县移除城市
				if(aList.length==0){
					for(var m=0;m<tempList[obj.pobj.id].length;m++){
						if(tempList[obj.pobj.id][m].id == obj.cobj.id){
							$scope.sList[$scope.addressIndex][obj.pobj.id].splice(m,1);
							break;
						}
					}
				} 
				//如果是最后一个城市移除省份
				if(jList.length==0){
					for(var i=0;i<tempList['-1'].length;i++){
						if(tempList['-1'][i].id == obj.pobj.id){
							$scope.sList[$scope.addressIndex]['-1'].splice(i,1);
							break;
						}
					}
				} 
				break;
			}
		}  
		$scope.oList = $scope.AddressRemoveAndSort($scope.oList); 
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
	
	$scope.showAddress = [];
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

	/**
	 * 是否全市
	 * @param {object} list 显示的地区数据
	 * @param {int} cid 要判断的城市id
	 * @return {bool} 是否是全市
	 */
	$scope.isAllCity = function(list,cid){
		var result = false; 
		var json = $scope.list;
		if(typeof json['-1'] !=='undefined'){
			var pList = json['-1'];
			for(var i=0;i<pList.length;i++){ 
				var jList = json[pList[i].id];  
				var bl = false;
				for(var j=0;j<jList.length;j++){
					if(jList[j].id == cid){
						var aList = json[jList[j].id];
						var oaList = list[jList[j].id]; 
						if(aList.length == oaList.length){ //包含所有县区
							result = true;
						}else{
							result = false; 
						}
						bl = true;
						break;
					} 
				} 
				if(bl==true){
					break;
				}
			}
		}
		return result;
	}

	/**
	 * 地址选择点击确认
	 */
	$scope.confirmClick = function(){ 
		var first_amount = 1,
			first_fee =0,
			additional_amount = 0,
			additional_fee = 0;
		if(typeof $scope.showAddress[$scope.addressIndex] !== 'undefined'){
			if(typeof $scope.showAddress[$scope.addressIndex].first_amount !=='undefined')
				first_amount = $scope.showAddress[$scope.addressIndex].first_amount;
			if(typeof $scope.showAddress[$scope.addressIndex].first_fee !=='undefined')
				first_fee =$scope.showAddress[$scope.addressIndex].first_fee;
			if(typeof $scope.showAddress[$scope.addressIndex].additional_amount !=='undefined')
				additional_amount = $scope.showAddress[$scope.addressIndex].additional_amount;
			if(typeof $scope.showAddress[$scope.addressIndex].additional_fee !=='undefined')
				additional_fee =$scope.showAddress[$scope.addressIndex].additional_fee;
		}
		if(typeof $scope.sList[$scope.addressIndex] !== 'undefined'){  
			if($scope.sList[$scope.addressIndex]['-1'].length==0){
				$scope.showAddress.splice($scope.addressIndex,1);
				$scope.sList.splice($scope.addressIndex,1);
			}else{
				$scope.showAddress[$scope.addressIndex] = $scope.processingData($scope.sList[$scope.addressIndex]);
			} 
		} 
		$scope.isShowAddress = false; //关闭弹窗
		$scope.resetAddressState();  
		$scope.showAddress[$scope.addressIndex].first_amount =first_amount;
		$scope.showAddress[$scope.addressIndex].first_fee =first_fee;	
		$scope.showAddress[$scope.addressIndex].additional_amount = additional_amount;
		$scope.showAddress[$scope.addressIndex].additional_fee = additional_fee;
		//处理显示地区 加入是否全省 
		for(var i=0;i<$scope.showAddress.length;i++){
			var d = $scope.showAddress[i];
			var pList = d['-1']; 
			for(var j=0;j<pList.length;j++){
				var pid = pList[j].id;
				pList[j].isAllProvince = $scope.isAllProvince(d,pid);
			}
		}
		console.log($scope.showAddress);
	}
	/**
	 * 地址选择点击取消
	 */
	$scope.cancelClick = function(){ 
		// $scope.addressIndex; 
		$scope.isShowAddress = false; //关闭弹窗
		$scope.oList = $scope.processingData($scope.tempoList); 
		$scope.sList = $scope.processingData($scope.tempsList); 
		$scope.resetAddressState();
	}
	/**
	 * 重置可选地址和已选地址的选择状态和打开状态
	 */
	$scope.resetAddressState = function(){
		var oList = $scope.oList;
		for(var item in oList){
			var data = oList[item];
			for(var i=0;i<data.length;i++){
				data[i].active = false;
				data[i].isOpen = false;
			}
		}
		var sList = $scope.sList;
		for(var item in sList){
			var data = sList[item];
			for(var i=0;i<data.length;i++){
				data[i].active = false;
				data[i].isOpen = false;
			}
		}
	}
	/**
	 * 编辑地址
	 */
	$scope.editAddress = function(index){
		$scope.addressIndex = index; 
		$scope.setShowAddress(); 
	}
	/**
	 * 删除地址
	 */
	$scope.removeAddress = function(index){ 
		var e  = event || window.event;
		e.stopPropagation();  
		showDelProver($(e.target), function(){
			var sList = $scope.sList[index];
			for(var item in sList){
				for(var i=0;i<sList[item].length;i++){
					$scope.oList[item].push(sList[item][i]);
				} 
			}
			$scope.oList = $scope.AddressRemoveAndSort($scope.oList); 
			$scope.resetAddressState();
			$scope.showAddress.splice(index,1); 
			$scope.sList.splice(index,1);
			$scope.$apply();
        }, '确定要删除吗?');  
	} 
	/**
	 * 运费编辑时数据处理
	 */
	$scope.editData = function(){
		$scope.data.id = jsonData.id;
		$scope.data.title = jsonData.title;
		$scope.data.billing_type = jsonData.billing_type;
		// $scope.data.delivery_id = jsonData.delivery_id; 
		$scope.data.sort = jsonData.sort;
		$scope.first_amount = jsonData.delivery_rule[0].first_amount;
		$scope.first_fee = jsonData.delivery_rule[0].first_fee;
		$scope.additional_amount = jsonData.delivery_rule[0].additional_amount;
		$scope.additional_fee = jsonData.delivery_rule[0].additional_fee; 
		var dr = jsonData.delivery_rule;
		var oList = $scope.oList;
		var list = $scope.list;
		var sList = [];
		var selRegions =[];
		//补全城市和省份
		for(var i=1;i<dr.length;i++){
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
									if(typeof sList[i-1] === 'undefined')
										sList[i-1] ={};
									if(typeof sList[i-1][jList[k].id] ==="undefined")
										sList[i-1][jList[k].id] =[];
									sList[i-1][jList[k].id].push(aList[l]);
									bl = true;
									break;
								}
							}
						} 
						if(!bl){
							if(jList[k].id==regions[n]){
								if(typeof sList[i-1] === 'undefined')
									sList[i-1] ={};
								if(typeof sList[i-1][pList[j].id] ==="undefined")
									sList[i-1][pList[j].id] =[];
								sList[i-1][pList[j].id].push(jList[k]);
								bl = true;
								break;
							}
						}else{
							//补全城市
							if(typeof sList[i-1] === 'undefined')
									sList[i-1] ={};
							if(typeof sList[i-1][pList[j].id] ==="undefined")
								sList[i-1][pList[j].id] =[];
							sList[i-1][pList[j].id].push(jList[k]);
							break;
						}
					}
					if(bl){
						//补全省份
						if(typeof sList[i-1] === 'undefined')
							sList[i-1] ={};
						if(typeof sList[i-1]['-1'] ==="undefined")
							sList[i-1]['-1'] =[];
						sList[i-1]['-1'].push(pList[j]);
						break;
					}
				}
			}
			$scope.sList[i-1] = $scope.AddressRemoveAndSort(sList[i-1]); 
			$scope.showAddress[i-1] = $scope.processingData($scope.sList[i-1]);
			$scope.showAddress[i-1].first_amount = dr[i].first_amount;
			$scope.showAddress[i-1].first_fee = dr[i].first_fee;
			$scope.showAddress[i-1].additional_amount = dr[i].additional_amount;
			$scope.showAddress[i-1].additional_fee = dr[i].additional_fee;
			selRegions = selRegions.concat(regions);
			selRegions = selRegions.unique3();
		}  
		//处理可选城市 
		
		var pList = oList['-1'];
		if(dr.length>1){
			for(var i=0;i<pList.length;i++){
				var jList = oList[pList[i].id];
				var hash = {}; //哈希集合 用于记录某个省份下的城市是否移除情况 
				for(var j=0;j<jList.length;j++){
					var aList = oList[jList[j].id]; 
					var isAllRemove = $scope.spliceInfo(aList,selRegions); //移除数据并返回处理结果 （是否全部移除）
					if(isAllRemove){//如果县区全部移除则移除改城市
						hash[jList[j].id] = true;
						jList.splice(j,1);
						j--;
					}else{
						hash[jList[j].id] = false;
					}
				}
				var pbl = false;
				for(var item in hash){ 
					if(!hash[item]){
						pbl = false;
						break;
					}else{
						pbl = true;
					}
				}
				//如果城市全部移除则移除省份
				if(pbl){
					pList.splice(i,1);
					i--;
				} 
			} 
		}   
		//处理显示地区 加入是否全省 
		for(var i=0;i<$scope.showAddress.length;i++){
			var d = $scope.showAddress[i];
			var pList = d['-1']; 
			if(typeof pList !=='undefined'){
				for(var j=0;j<pList.length;j++){
					var pid = pList[j].id;
					pList[j].isAllProvince = $scope.isAllProvince(d,pid);
				}
			} 
		}
	}
	/**
	 * 移除数据并返回是否全部包含
	 * @param  {[array]} arr1 [原数据的数组]
	 * @param  {[array]} arr2 [已选地区的id集合]
	 * @param  {[array]} arro [要移除的数组]
	 * @return {[bool]}      [arr2里的数据包含arr1里的所有数据]
	 */
	$scope.spliceInfo = function(arr1,arr2){
		var result = true; 
		var hash = {};
		if(typeof arr1 !=='undefined'){
			for(var i=0;i<arr1.length;i++){ 
				for(var j=0;j<arr2.length;j++){
					if(arr1[i].id == arr2[j]){
						hash[arr1[i].id] = true;
						arr1.splice(i,1);
						i--;
						break;
					}
					if(j==arr2.length-1){
						hash[arr1[i].id] = false;
					}
				} 
			}
			for(var item in hash){
				if(!hash[item]){
					result = false;
					break;
				} 
			}
		}else{
			result = false;
		}
		
		return result;
	}

	//是否编辑
	if(typeof jsonData.id !=='undefined'){ 
		$scope.editData();
	} 
		

	/**
	 * 保存表单
	 */
	$scope.submitInfo = function(){
		var data ={data:$scope.data};     
		data.data.delivery_id = 0; //默认
		if(data.data.title==""){
			tipshow('请填写模版名称','warn');
			return;
		}
		// if(data.data.delivery_id=="0"){
		// 	tipshow('请选择快递公司','warn');
		// 	return;
		// }
		if(data.data.sort==""){
			tipshow('请填写排序值','warn');
			return;
		} 
		var resList = $scope.showAddress;
		var oList = $scope.list;
		var delivery_rule = [];
		delivery_rule[0]={
			'regions':[0],
			'first_amount':$scope.first_amount,
			'first_fee':$scope.first_fee,
			'additional_amount':$scope.additional_amount,
			'additional_fee':$scope.additional_fee
		};
		for(var i=0;i<resList.length;i++){
			var pList = resList[i]['-1'];
			var regions =[];
			for(var j=0;j<pList.length;j++){ 
				if($scope.isAllProvince(resList[i],pList[j].id)){//是全省
					regions.push(pList[j].id); 
				}else{//不是全省
					var jList = resList[i][pList[j].id];
					for(var k=0;k<jList.length;k++){ 
						//是否是全市
						if($scope.isAllCity(resList[i],jList[k].id)){ //是全市
							regions.push(jList[k].id);
						}else{//不是全市
							var aList = resList[i][jList[k].id];  
							if(typeof aList !=="undefined"){
								for(var n=0;n<aList.length;n++){
									regions.push(aList[n].id);
								}
							}else{//城市下面要是没有县区就传递城市id
								regions.push(jList[j].id);
							} 
						}
						
					}
				}	
			} 
			var n = i+1;
			delivery_rule[n]={
				'regions':regions,
				'first_amount':resList[i].first_amount,
				'first_fee':resList[i].first_fee,
				'additional_amount':resList[i].additional_amount,
				'additional_fee':resList[i].additional_fee
			};
		}  
		data.data.delivery_rule = JSON.stringify(delivery_rule); 
		if($scope.isRequest){ 
			$scope.isRequest = false;
			var url = '/merchants/currency/expressSet';
			if(data.data.id!=''){
				url ='/merchants/currency/expressSet/'+data.data.id;
			}
			$http({  
		     	url : url,  
		      	method : 'POST',  
		      	data : data,   
		  	}).success(function(res) {  
		  		if(res.status == 1){
		  			tipshow(res.info);
		  			setTimeout(function(){
		  				location.href='/merchants/currency/express';
		  			},500); 
		  		}else{
		  			tipshow(res.info,'warn');
		  		}
		  		setTimeout(function(){
		  			$scope.isRequest = true;
		  		},100)
		  		
		  	});  
		}
		 
	}
});
