/**
 * @author  huoguanghui
 * @created by 2017年12月12日17:18:58
 */
//模态框居中控制
$('.modal').on('shown.bs.modal', function (e) { 
  	// 关键代码，如没将modal设置为 block，则$modala_dialog.height() 为零 
  	$(this).css('display', 'block'); 
  	var modalHeight=$(window).height() / 2 - $(this).find('.modal-dialog').height() / 2; 
  	if(modalHeight < 0){
  		modalHeight = 0;
  	}
  	$(this).find('.modal-dialog').css({ 
    	'margin-top': modalHeight 
 	}); 
});
/**
	* 总开关按钮点击事件
	* 1.切换开关状态
	* 2.所有子开关切换成总开关状态
	* 3.若是关闭状态 则 子模块全部隐藏
*/ 
var is_on=$(".switch-total label").attr("data-is-open");//开关状态
var id = '';//导航id
$(".switch-total label").click(function() {
    var _this = this;
    var open = $(this).attr("data-is-open");  
    var status = open=="1"?0:1;
    is_on = status;
    if (open == "1") {
        //切换成关闭状态
        $(_this).removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
        $("#distribute_content").addClass('none');
        $('.app').hide();
    } else {
        //切换成开启状态
        $(_this).removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
        $("#distribute_content").removeClass('none'); 
        $('.app').show();
    }
}); 

var app = angular.module('myApp', []);
app.controller('myCtrl',function($scope,$http){
	// 数据开始
	$scope.isBinding = 0;//是否绑定小程序 0否 1 是
	$scope.is_auth_submit = 0;//是否开启自动提交  0 否 1 是
	$scope.btnSubmit  = true;//按钮提交  防止多点
	$scope._host = _host;//静态图片域名
	$scope.host = host;//网站域名
	$scope.imgUrl = imgUrl;//动态图片域名
	$scope.iconGroupList = [//icon列表
		{
			"title":"首页",//标题
		},
		{
			"title":"购物车",
		},
		{
			"title":"一键参团",
		},
		{
			"title":"我的",
		}
	];
	/**
	* 添加导航 导航选择框数据
	* title 标题
	* type 类型   1 微页面 2 一键参团 3 购物车
	*/
	$scope.navSelectData = {
		isNavSelectShow: false,//添加导航选择框是否显示
		top: 122,//弹框上移距离
		navList:[
			{
				title: "微页面及分类",
				type: 1,
			}
		]
	}
	/**
	* 小程序路径说明
	* pages/index/index 主页
	* pages/cart/cart 购物车
	* pages/member/index/index 会员中心
	* pages/micropage/index/index 微页面
	* pages/grouppurchase/groupOnekey/groupOnekey 一键参团
	* 
	* 修改链接说明：
	* 只有微页面可以修改链接
	*
	* 根据需求不同（与有赞相比）
	* 底部最多可设置三个微页面
	* 按照从左向右的顺序 分别  （新建三个微页面，为了实现改变微页面链接不用从新提交代码问题）
	* pages/micropage/index1/index 微页面1
	* pages/micropage/index2/index 微页面2
	* pages/micropage/index3/index 微页面3
	*
	* 初始数据  统一用微页面路径  提交时统一更换路径
	*
	* pageId  微页面 id  非微页面为0
	* id  导航id  添加为0 编辑为导航id
	* is_weixin 0否  1是  当前导航是否提交到微信
	*/
	$scope.tabBarList = [//导航数据
		{
//			"id":0,//导航id
			"title":"首页",//标题
//			"pagePath":"pages/index/index",//页面路径
        	"urlTitle":"小程序主页",//链接名称
        	"isCanReviseUrl":false,//能否修改url
        	"pageId":0//微页面 id  非微页面为0
		}
	];
	$scope.tabBarOriginArr = [];//原始导航集合
	$scope.tabBarIndex = -1;//当前选择添加图片的数据下边
	$scope.pageData = {
		searchTitle:"",//搜索内容
		list:[]//微页面列表
	} 


	/**
	 * func begin
	 * @author huoguanghui
	 * @created 2017年12月13日09:08:58
	 */
	/**
	 * icon 弹框显示
	 */
	$scope.iconModalShow=function(index){
		$(".pic-modal").modal("show");
		$scope.tabBarIndex = index;
	}
	/**
	 * 切换添加icon
	 * @description  标题 icon 赋值
	 */
	$scope.changeIcon = function(item){
		$scope.tabBarList[$scope.tabBarIndex].title = item.title;
		//隐藏弹框
		$(".pic-modal").modal("hide");
	}
	/**
	 * 新增导航功能
	 * @param type   1 微页面 2 一键参团 3 购物车
	 */
	$scope.addNav = function(type,event){
		event.stopPropagation();//阻止冒泡
		switch (type) {
			case 1:
				var item = {
//					"id":0,//导航id
					"title":"",//标题
//					"pagePath":"pages/micropage/index/index",//页面路径
		        	"urlTitle":"",//链接名称
		        	"isCanReviseUrl":true,//能否修改url
		        	"pageId":0
				}
				$scope.tabBarList.splice(-1, 0, item);
				break;
			default:
				// statements_def
				break;
		}
		//添加数据成功后隐藏弹框
		$scope.navSelectData.isNavSelectShow = false;
	}
	/**
	 * 隐藏选择导航弹框
	 */
	$scope.navSelectHide = function(){
		$scope.navSelectData.isNavSelectShow = false;
	}
	/**
	 * 添加导航 or 显示添加导航弹框
	 * @desc 当导航存在购物车和一键参团时  直接添加微页面  
	 * 否则  弹出弹框
	 */
	$scope.addNavs = function(event){		
		event.stopPropagation();//阻止冒泡
		if($scope.navSelectData.navList.length == 1){
			//添加微页面
			var item = {
//				"id":0,//导航id
				"title":"",//标题
//				"pagePath":"pages/micropage/index/index",//页面路径
	        	"urlTitle":"",//链接名称
	        	"isCanReviseUrl":true,//能否修改url
	        	"pageId":0
			}
			$scope.tabBarList.push(item);
		}else if($scope.navSelectData.navList.length == 2){
			$scope.navSelectData.isNavSelectShow = true;//显示弹框
			$scope.navSelectData.top = 82;				//改变弹框距离父级距离
		}else if($scope.navSelectData.navList.length == 3){
			$scope.navSelectData.isNavSelectShow = true;
			$scope.navSelectData.top = 122;
		}
		console.log($scope.tabBarList)
	}
	/**
	 * 删除导航功能
	 * @param item 当前导航数据  index 当前导航下标
	 * @description  删除购物车或一键参团后
	 */
	$scope.deleteNavBar = function(item,index){
//		if(item.urlTitle === "购物车"){
//			var item = {
//				title: "购物车",
//				type: 3,
//			};
//			$scope.navSelectData.navList.push(item)
//		}else if(item.urlTitle === "一键参团"){
//			var item = {
//				title: "一键参团",
//				type: 2,
//			};
//			$scope.navSelectData.navList.splice(0,0,item);
//		}
		$scope.tabBarList.splice(index, 1);
		console.log($scope.tabBarList)
	}
	/**
	 * 打开微页面弹框
	 * @param 当前导航下标
	 */
	$scope.openPageModal = function(index){
		$scope.tabBarIndex = index;//下标复制
		$scope.pageData.list = [];//初始化数组
		$scope.pageData.searchTitle = "";//初始化搜索信息
		$.get('/merchants/xcx/micropage/select?page=1', function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageData.list.push({
                        "id":val.id,
                        "name":val.title,
                        "created_at":val.create_time
                    })
                })
            })
            
            var totalCount = data.total, showCount = 10,
            limit = data.pageSize;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/xcx/micropage/select?page=' + page,function(response){
                        if(response.errCode == 0){
                            $scope.pageData.list = [];
                            angular.forEach(response.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageData.list.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.create_time
                                    })
                                })
                            })
                        }
                    })
                }
            });
			$("#page_model").modal("show");//微页面弹框显示
        },'json')
	}
	/**
	 * 搜索微页面
	 */
	$scope.searchPage = function(){
		$scope.pageData.list = [];//初始化数组
		$.get('/merchants/xcx/micropage/select?page=1&title=' + $scope.pageData.searchTitle, function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageData.list.push({
                        "id":val.id,
                        "name":val.title,
                        "created_at":val.create_time
                    })
                })
            })
            
            var totalCount = data.total, showCount = 10,
            limit = data.pageSize;
            $('.page_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.get('/merchants/xcx/micropage/select?page=' + page +"&title=" + $scope.pageData.searchTitle,function(response){
                        if(response.errCode == 0){
                            $scope.pageData.list = [];
                            angular.forEach(response.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageData.list.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.create_time
                                    })
                                })
                            })
                        }
                    })
                }
            });
			$("#page_model").modal("show");//微页面弹框显示
        },'json')
	}
	/**
	 * 选择微页面
	 */
	$scope.choosePageLinkSure = function(item){
		$scope.tabBarList[$scope.tabBarIndex]["pageId"] = item.id;
		$scope.tabBarList[$scope.tabBarIndex]["urlTitle"] = item.name;
		$("#page_model").modal("hide");//隐藏弹框
	}
	//保存到数据库
	function saveDatabase(callback){
		$http.post("/merchants/xcx/processTopNav",{id:id,is_on:is_on,data:$scope.tabBarList}).success(function (data) {
			$scope.btnSubmit = true;
            if(data.errCode == 0){//保存成功
            	callback();
            }else if(data.errCode < 0){
            	tipshow(data.errMsg,"warn");
            }
        })
	}
	/**
	 * 保存
	 * 1.是否绑定微信小程序  （否 直接保存到数据库，是 通过）
	 * 2.是否开启自动更新    （否 直接保存到数据库，跳转小程序设置页 是 通过）（加成判断，若无更改数据，直接保存，不跳转）
	 * 3.是否更改非微页面数据（否 直接保存到数据库，是 用户选择更改小程序或者保存到数据库）
	 */
	$scope.save = function(isValid){
		if(!$scope.btnSubmit){//交互过程中 不能再次提交
			return false;
		}
		if(!isValid){
            tipshow("请先编辑基本信息","warn")
            return false;
        }
		$scope.btnSubmit = false;
        /**
         * 判断是否更改导航数据
         */
        var isNeedWeixin =  false;//是否需要微信交互
        var pageNum = 0;//重新设置微页面路径 
        if($scope.tabBarList.length !== $scope.tabBarOriginArr.length){
        	isNeedWeixin = false;
        }
        /**
         * 判断数据是否相同 的同时  排除新增的数据  新增直接为0
         */
    	angular.forEach($scope.tabBarList,function(val,index){
           	if(!$scope.tabBarOriginArr[index] || val.title !== $scope.tabBarOriginArr[index].title){
           		isNeedWeixin = true;
           	}
           	if(val.pageId > 0){//大于0 就是微页面
           		pageNum ++;
           		val.pagePath = "pages/micropage/index"+pageNum+"/index";
           	}
        })
        /** 
         * 是否绑定微信小程序
         */
        if($scope.isBinding == 0){//未绑定
        	saveDatabase(function(){
        		tipshow("保存成功");
        		setTimeout(function(){
            		location.reload();
            	},2000)
        	})
        	return false;
        }
        /**
         * 是否开启自动更新
         * @description  未开启直接保存到数据库
         */
        if($scope.is_auth_submit == 0){//未开启
        	saveDatabase(function(){
        		if(isNeedWeixin){//已更改导航数据
	        		tipshow("保存成功,尚未开启自动更新功能,请自行提交");
	            	setTimeout(function(){
	            		window.location.href = "/merchants/marketing/liteappInfo"
	            	},2000)
        		}else{//无更改导航数据
        			tipshow("保存成功");
        		}
        	})
	        return false;
        }
        /**
         * 判断导航是否更改
         * 1.原始数据数量 是否等与 现在导航数量 等于  不相等 弹出弹框（需要微信交互）
         * 2.数量相同时判断isSyncWexin是否相同 title 图片是否相同 相同直接交互数据库   不相同弹出弹框（需要微信交互）
         */
        
        if(isNeedWeixin){//导航存在修改
        	$("#remindModal").modal("show");//微页面弹框显示
        	$scope.btnSubmit = true;
        }else{//导航不存在修改
			saveDatabase(function(){
        		tipshow("保存成功");
        	})
        }
	}
	/**
	 * 保存到数据库
	 */
	$scope.saveData = function(){
		$scope.btnSubmit = false;
		saveDatabase(function(){
    		tipshow("保存成功");
        	$("#remindModal").modal("hide");
//      	setTimeout(function(){
//      		location.reload();
//      	},2000)
    	})
	}

	/**
	 * 页面编辑读取数据
	 */
	$http.post("/merchants/xcx/selectTopNav").success(function (data) {
        if(data.errCode == 0){
        	$scope.is_auth_submit = data.data.is_auth_submit;//自动更新赋值
        	$scope.isBinding = data.data.isBinding;//绑定小程序赋值
        	var list = data.data.template_data ? JSON.parse(data.data.template_data) : [];
        	if(list.length > 0){
        		id = data.data.id;
        		is_on = data.data.is_on;
        		if (is_on == "1") {        			
			        //切换成开启状态
			        $(".switch-total label").removeClass("ui-switcher-off").addClass("ui-switcher-on").attr("data-is-open", "1");
			        $("#distribute_content").addClass('none');
			        $('.app').show();
			    } else {
			        //切换成关闭状态
			        $(".switch-total label").removeClass("ui-switcher-on").addClass("ui-switcher-off").attr("data-is-open", "0");
			        $("#distribute_content").removeClass('none'); 
			        $('.app').hide();
			    }
        		
	        	var arr = list;//用于深拷贝
	        	/* 对象数组 深拷贝函数 */
	        	var objDeepCopy = function (source) {
				    var sourceCopy = source instanceof Array ? [] : {};
				    for (var item in source) {
				        sourceCopy[item] = typeof source[item] === 'object' ? objDeepCopy(source[item]) : source[item];
				    }
				    return sourceCopy;
				}
	        	//编辑  导航列表复制
	        	$scope.tabBarList = list;
	        	//保留原始值 （深拷贝）
	        	$scope.tabBarOriginArr = objDeepCopy(list);
				/* 添加nav框限制 */
				angular.forEach(list,function(val,index){
	                if(val.urlTitle === "一键参团"){
	                	angular.forEach($scope.navSelectData.navList,function(val1,index1){
			                if(val1.title === "一键参团"){
			                    $scope.navSelectData.navList.splice(index1, 1);
			                }
			            })
	                }
	                if(val.urlTitle === "购物车"){
	                	angular.forEach($scope.navSelectData.navList,function(val1,index1){
			                if(val1.title === "购物车"){
			                    $scope.navSelectData.navList.splice(index1, 1);
			                }
			            })
	                }
	            })
        	}
        }else if(data.errCode<0){
        	tipshow(data.errMsg,'warm')
        }
    })
})