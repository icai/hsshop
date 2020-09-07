"use strict";  //严格模式
//文档加载完成
var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope,$http) { 
    hstool.load();
    $scope.navList = {}; //导航数据
    $scope.list = []; //消息数据
    $scope.currentType = 0;//当前类型
    $scope.unread = null;
    $scope.isPage = false;
    //获取消息列表和未读数
    $scope.getList = function(page,isqq){
        var data ={};
        hstool.load();
        if($scope.currentType!=0){
            data.notification_type = $scope.currentType
        }
        data.page = page; 
        //获取消息列表
        $http.get('/merchants/notification/notificationList',{params:data}).success(function(res){
            hstool.hsload();
            if(res.status==1){
                var totalCount = res.data.notificationList.count; 
                var showCount = res.data.notificationList.currentPage;
                var pageSize = res.data.notificationList.pageSize;
                if(isqq!=1)
                    $scope.pagination(totalCount,showCount,pageSize);
                $scope.list = res.data.notificationList.data;
                if(totalCount/pageSize>1){
                    $scope.isPage = true;
                }
            }
            hstool.closeLoad();
        }).error(function(){ 
            hstool.closeLoad();
        });   
    }
    /**
     * 获取消息类型 (渲染导航)
     * @return null
     */
    $scope.getNavList = function(){
        $http.get('/merchants/notification/settingList?need_count='+1).success(function(res){  
            if(res.status==1){
                var list = res.data.subscribeList;
                var total= 0;
                for(var vo in list){ 
                    total += parseInt(list[vo].notificationCount); 
                    list[vo].isActive = false; 
                }
                list["0"] = {};
                list['0'].isActive = true;
                list['0'].title = "全部";
                list['0'].notificationCount =total;
                delete list[6]; //去掉后台静态数组的下标6，因为不是商家的 hsz 2018/6/25
                $scope.navList = list;
                $scope.getList(1); 
            }
        });
    }
    $scope.getNavList();
    
    
    
    //消息列表分页
    $scope.pagination = function(totalCount,showCount,limit){
        $('.pagination').extendPagination({
            totalCount: totalCount,//数据总数
            showCount: showCount,//展示页数
            limit: limit,//每页展示条数
            callback: function (page, limit, totalCount) { 
                $scope.getList(page,1);
            }
        });
    }

    //删除消息
    $scope.delMsg = function(id){  
        $http.post('/merchants/notification/deleteNotification',{notification_id:id}).success(function(res){
            if(res.status==1){
                tipshow(res.info);
                for(var i=0;i<$scope.list.length;i++){
                    if($scope.list[i].id == id){
                        $scope.getNavList();
                        $scope.list.splice(i,1);
                        msgTool.getMsgCount();
                        msgTool.getMsgInfo(MSG_URL);
                    }
                }
            }else{
                tipshow(res.info,"warn");
            }
        });
    }

    //切换导航
    $scope.tabNav = function(index){
        $scope.currentType = index;
        $scope.getList(1);
        for(var vo in $scope.navList){
            if(vo == index){
                $scope.navList[vo].isActive = true;
            }else{ 
                $scope.navList[vo].isActive = false;
            }
        }
    }

    //监听hash是否变化
    window.onhashchange = function(){
        hstool.hsload();
        var hash = location.hash;
        hash = hash.substr(1);
        $(".tab_nav li").eq(hash).addClass('hover').siblings().removeClass('hover');
    }
    
});