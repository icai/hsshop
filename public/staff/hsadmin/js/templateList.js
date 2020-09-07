
var app = angular.module('myApp', []);
app.controller('myCtrl',function($scope,$http) {
	$scope.isDraft = true;//当前是否显示草稿箱
	$scope.xcxList = [];//小程序信息列表
	$scope.draftContent = "";//草稿箱内容
	$scope.templateContent = "";//模板内容

	// methods  事件
	//草稿箱 模板列表切换 type 1 草稿箱 2模板列表
	$scope.switchNav = function(type){
		getXcxList(type,$scope);
	}
	getXcxList(1,$scope);
	/**
	 * 获取小程序列表信息
	 * @param  type 1 草稿箱 2模板列表
	 */
	function getXcxList(type,$scope){
		if(type == 1){
			$scope.isDraft = true;
		}else{
			$scope.isDraft = false;
		}
		$http({
	        method: 'GET',
	        url: '/staff/xcx/template/all?type='+type
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		$scope.xcxList = [];
	           	$scope.xcxList = res.data.data;
	          	var totalCount = res.data.total, showCount = 10,
                limit = res.data.pageSize;
	           	$('.page').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/staff/xcx/template/all?type='+type+'&page='+page,function(response){
                            if(response.errCode == 0){
                            	$scope.xcxList = [];
                                $scope.$apply(function(){
                                    $scope.xcxList = response.data;
                                })
                            }
                        })
                    }
                });
                $('.page').append('<div style="float:right;font-size:18px;margin-top:20px">共'+ totalCount +'条</div>');
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
	/**
	 * 添加到模板库
	 * @param id 模板id
	 */
	$scope.addRepository = function(id){
		$http({
	        method: 'GET',
	        url: '/staff/xcx/template/add?id='+id
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		tipshow("添加成功","info");
	    	}else{
	    		tipshow(res.data.errMsg,"warn")
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
	/**
	 * 删除草稿箱
	 * @param id 模板id
	 */
	$scope.deleteDraft = function(id,index){
		$http({
	        method: 'GET',
	        url: '/staff/xcx/template/del?id='+id
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		tipshow("删除成功","info");
	    		getXcxList(1,$scope);
	    	}else{
	    		tipshow(res.data.errMsg,"warn")
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
	/**
	 * 删除模板库
	 * @param id 模板id
	 */
	$scope.deleteTemplate = function(id,index){
		$http({
	        method: 'GET',
	        url: '/staff/xcx/template/del?isSync=1&id='+id
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		tipshow("删除成功","info");
	    		getXcxList(2,$scope);
	    	}else{
	    		tipshow(res.data.errMsg,"warn")
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
	/**
	 * 作为版本代码
	 * @param id 模板id
	 */
	$scope.setVersion = function(id, index){
		$http({
	        method: 'GET',
	        url: '/staff/xcx/template/setVersion?id='+id+'&type='+index
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		tipshow("设置版本库代码成功","info");
                getXcxList(2,$scope);
	    	}else{
	    		tipshow(res.data.errMsg,"warn")
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
	/**
	 * 同步草稿箱
	 */
	$scope.syncDraft = function(){
		$http({
	        method: 'GET',
	        url: '/staff/xcx/third/templatedraft'
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		$scope.draftContent = '草稿箱'+res.data.errMsg;
	    	}else{
	    		tipshow(res.data.errMsg,"warn")
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
	/**
	 * 同步模板列表
	 */
	$scope.syncTemplate = function(){
		$http({
	        method: 'GET',
	        url: '/staff/xcx/third/template'
	    }).then(function successCallback(res) {
	    	if(res.data.errCode == 0){
	    		$scope.draftContent ='模板库'+res.data.errMsg;
	    	}else{
	    		tipshow(res.data.errMsg,"warn")
	    	}
	    }, function errorCallback(response) {
	            // 请求失败执行代码
	    });
	}
})