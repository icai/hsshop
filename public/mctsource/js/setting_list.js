"use strict";  //严格模式
//文档加载完成
$(function(){
	//子选项卡点击事件
    $("body").on("click",".switch-small label",function(e) { 
        e.preventDefault();
        var open = $(this).attr("data-is-open");
        var parents = $(this).parents('.js-set-wrap');
        if (open == "1") {
            $(this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");  
            parents.removeClass('enable').addClass('disable');
            parents.find('.js-set-wrap-text').html("未启用");
        } else {
            $(this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");  
            parents.removeClass('disable').addClass('enable');
            parents.find('.js-set-wrap-text').html("启用");
        }
    });
});
hstool.load();
var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope,$http) { 
	$scope.list = []; //消息设置数据
	//获取消息数据
    $http.get('/merchants/notification/settingViewList').success(function(res) {
        $scope.list = res.data.subscribeList; 
        hstool.closeLoad();  //关闭加载层
    });
    //开启消息提醒
    $scope.openSwitcher=function(index,e){    
    	e.preventDefault(); 
    	$http({  
	      	url : '/merchants/notification/notificationSubscribe',  
	      	method : 'POST',  
	      	data : {notification_type:index},   
      	}).success(function(res) {  
      		if(res.status == 1){
      			tipshow(res.info);
      			$scope.list[index].isSubscribed = true;  
      		}else{
      			tipshow(res.info,'warn');
      		}
      		
      	});  
    }
    //关闭消息提醒
    $scope.closeSwitcher =function(index,e){  
    	e.preventDefault();
    	$http({  
	      	url : '/merchants/notification/notificationUnsubscribe',  
	      	method : 'POST',  
	      	data : {notification_type:index},   
      	}).success(function(res) {  
      		if(res.status == 1){
      			tipshow(res.info);
      			$scope.list[index].isSubscribed = false;  
      		}else{
      			tipshow(res.info,'warn');
      		}
      	}); 
    }
    $scope.setSwitcherClass = function(isSubscribed){
    	console.log(isSubscribed);
    }

});