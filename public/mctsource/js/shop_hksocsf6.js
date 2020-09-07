$(function(){
    $('.js-switch').click(function(){
        if($(this).hasClass('ui-switcher-off')){
            $(this).removeClass('ui-switcher-off');
            $(this).addClass('ui-switcher-on');
        }else{
            $(this).removeClass('ui-switcher-on');
            $(this).addClass('ui-switcher-off');
        }
    })
    $("#active_span span").on('click',function () {
        $(this).addClass('active_span').siblings('span').removeClass('active_span')
        var id = $(this).attr('data-id')
        if(id == 1){
            $("#btmNav").show();
            $("#topNav").hide();
        }else{
            $("#btmNav").hide();
            $("#topNav").show();
        }
    })
})
app.controller('myCtrl',['$scope','$http','commonServer',function($scope,$http,commonServer) {
    $scope.menus = {
        menusType:1,//1.公众号自定义样式，2，APP导航模板，3，带购物车的导航模板
        menu:[
            {
                title:'标题',
                linkUrl:'',
                linkUrlName:'',
                submenusShow:false,
                submenusLeft:0,
                submenus:[
                    // {
                    //     title:'标题',
                    //     linkUrl:'',
                    //     linkUrlName:''
                    // }
                ],
                width:'33.3333%',
                icon:'',
                iconActive:'',
                dropDown:false
            },
            {
                title:'标题',
                linkUrl:'',
                linkUrlName:'',
                submenusShow:false,
                submenusLeft:0,
                submenus:[
                    // {
                    //     title:'标题',
                    //     linkUrl:'',
                    //     linkUrlName:''
                    // }
                ],
                width:'33.3333%',
                icon:'',
                iconActive:'',
                dropDown:false
            },
            {
                title:'标题',
                linkUrl:'',
                linkUrlName:'',
                submenusShow:false,
                submenusLeft:0,
                submenus:[],
                width:'33.3333%',
                icon:'',
                iconActive:'',
                dropDown:false
            }
        ],
        bgColor:'#ffffff',
        title:''
    }
    $scope.tabBarList = [//导航数据
        {
//			"id":0,//导航id
            "title":"主页",//标题
			"pagePath":_host+"shop/index/"+store.id,//页面路径
            "urlTitle":"店铺主页",//链接名称
            "isCanReviseUrl":false,//能否修改url
            "pageId":0,//微页面 id  非微页面为0
            "is_home":0
        },
    ];
    // update 2018/8/27 by huakang todo 导航栏增加颜色设置
    $scope.colorSeting = {
        background_font_color:'#ffffff',// 背景颜色
        checked_font_color:'#ff0000',
        font_color:'#dddddd'
    }
    // end
    $scope.tabBarIndex = -1;//当前选择添加图片的数据下边
    $scope.pageData = {
        searchTitle:"",//搜索内容
        list:[]//微页面列表
    }
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
    $scope.pages = [
        {title:'店铺主页',isCheck:true,value:1},
        {title:'会员主页',isCheck:true,value:2},
        {title:'微页面',isCheck:true,value:3}
        //{title:'商品分组',isCheck:true,value:4},
        //{title:'商品搜索',isCheck:true,value:5},
    ]
    // 图片库
    $scope.uploadImages = [] // 默认选中
    $scope.is_used = 1 //1未启用底部导航2为启用。
    $scope.aaa = 1
    console.log($scope.is_used)
    $scope.uploadShow = false; //判断上传可图片model显示
    $scope.tempUploadImage = [];
    $scope.menuIndex = null;//记录当前位置
    $scope.cTPosition = 1;//1.为修改一级标题，2为修二级标题
    $scope.submenuIndex = null; //二级标题位置。
    $scope.cgIconPositon = 1;//1.为修改普通，2修改高亮图标
    $scope.nav_style = 1//1.公众号自定义样式，2，APP导航模板，3，带购物车的导航模板
	$scope.id=0;
    $scope.outerIndex = 0; //记录一级导航位置
    $scope.activity_list = [] //营销活动列表
    $scope.activityNavList = ["幸运大转盘","砸金蛋"];//营销活动导航列表
    $scope.activityIndex = 0;//营销活动 活动选择
    /*@author huoguanghui start*/
    $scope.host = imgUrl;
    //商品及商品分组弹框
    $scope.productModal = {
        list : [],//商品或分组列表
        navList : ["已上架商品","商品分组"],//商品或分组列表
        navIndex : 0,//商品弹框导航下标
        new : [//新建
            {
                href: "/merchants/product/create",
                title: "新建商品"
            },
            {
                href: "/merchants/product/productGroup",
                title: "新建分组"
            }
        ]
    };
    $scope.is_used_nav = 0 //顶部导航 0未开启 1开启
    /*@author huoguanghui end*/
	//获取店铺导航信息
	$http({ method: 'GET',url: '/merchants/store/selectStoreNav'
    }).success(function(response){
        console.log(response,'this is flag');
        if(response.errCode==0&&response.data.length!=0){
            if(response.data.is_used == 1){
                $scope.is_used = 1;
                $('.js-switch-bar').addClass('ui-switcher-on');
                $('.js-switch-bar').removeClass('ui-switcher-off');
            }else{
                $scope.is_used = 0;
                $('.js-switch-bar').removeClass('ui-switcher-on');
                $('.js-switch-bar').addClass('ui-switcher-off');
            }
            angular.forEach($scope.pages,function(val,key){
                if(!response.data.apply_page){
                    val.isCheck = false;
                }else{
                if(response.data.apply_page.indexOf(val.value) != -1){
                    val.isCheck = true;
                }else{
                    val.isCheck = false;
                }
                }
            })
    		$scope.id=response.data.id; 
    		$scope.menus=response.data.menu;
            angular.forEach($scope.menus.menu,function(val,key){
                val.dropDown = true;
                val.icon = imgUrl + val.icon;
                val.iconActive = imgUrl + val.iconActive
            })
    	}
	}) 
	//添加店铺导航信息 此处需要冬冬完善参数
	$scope.processNav = function(){
        $scope.apply_page = [];
        angular.forEach($scope.pages,function(val,key){
            if(val.isCheck){
                $scope.apply_page.push(val.value);
            }
        })
        $scope.postMenus = angular.copy($scope.menus);
        if($scope.postMenus.menu.length > 0){
            angular.forEach($scope.postMenus.menu,function(val,key){
                val.submenusShow = false
                if(val.icon !== null && val.icon !== undefined){
                    val.icon = val.icon.replace(_host,'');
                    val.iconActive = val.iconActive.replace(_host,'');
                    val.icon = val.icon.replace(imgUrl,'');
                    val.iconActive = val.iconActive.replace(imgUrl,'');
                }
            })
        }
        $('.btn_grounp button').attr('disabled','disabled');
		$http({  
		   method:'post',  
		   url:'/merchants/store/processStoreNav',  
		   data:{id:$scope.id,is_used:$scope.is_used,apply_page:$scope.apply_page,page_nav_data:$scope.postMenus}
		}).success(function(response){ 
		    if(response.errCode==0){
                tipshow('保存成功!');
                $('.btn_grounp button').removeAttr('disabled');
       //          setTimeout(function(){
       //              window.location.href = '/merchants/store';
       //          },1000)
			    // layer.alert('保存成功！',function(){
       //              window.location.reload();
       //          });
			}else{
                $('.btn_grounp button').removeAttr('disabled');
                tipshow(response.errMsg);
            }
		}).error(function(response){
            $('.btn_grounp button').removeAttr('disabled');
        })  
	}
    $scope.hasClass = function(element, cls) {
        return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
    }
    // 是否启用店铺导航点击
    $scope.isOn = function(e){
        if($scope.hasClass(e.target,'ui-switcher-on')){
            $scope.is_used = 0 //0未启用底部导航
        }else{
            $scope.is_used = 1 //开启底部导航
        }
        console.log($scope.is_used);
    }
    // 上传图片
    $scope.upload = function(){
        $scope.uploadShow = true;
        $('.webuploader-pick').next('div').css({
            'top': '19px',
            'width': '168px',
            'height': '44px',
            'left':'40%'
        })
    }
    // 点击修改模板类型
    $scope.changeModelShow = function(){
        showModel($('#changeModel'),$('#modal-dialog'));
    }
    // 点击一级导航显示二级导航
    $scope.showSubmenus = function($index){
        angular.forEach($scope.menus.menu,function(val,key){
            val.submenusShow = false;
        })
        $scope.menus['menu'][$index]['submenusShow'] = true;
    }
    // 选择模板类型确定按钮
    $scope.sureChooseModel = function(){
        $scope.menus.menusType = $scope.nav_style;
        hideModel($('#changeModel'),$('#modal-dialog'));
        if($scope.menus.menusType == "2"){
            $scope.menus.title = 'APP导航样式';
        }else{
            $scope.menus.title = '微信公众号自定义菜单样式';
        }
    }

    //选择模板类型取消按钮
    $scope.cancelChooseModel = function(){
         hideModel($('#changeModel'),$('#modal-dialog'));
    }
    // 添加二级导航
    $scope.addSubmenus = function($index){
        if($scope.menus['menu'][$index]['submenus'].length>=5){
            return;
        }
        $scope.menus['menu'][$index]['submenus'].push(
            {
                title:'标题',
                linkUrl:'',
                linkUrlName:''
            }
        )
    }
    //删除一个二级导航
    $scope.removeOneSubmenus = function($index,outerIndex){
        $scope.menus['menu'][outerIndex]['submenus'].splice($index,1);
    }

    //删除一个一级导航
    $scope.removemenus = function($index){
        $scope.menus['menu'].splice($index,1);
        var menuslength = $scope.menus['menu'].length;
        angular.forEach($scope.menus['menu'],function(val,key){
            val.width = 100/menuslength + '%';
            if(menuslength == 1){
                val.submenusLeft = '90px';
            }else if(menuslength == 2){
                val.submenusLeft = '22px';
            }else if(menuslength == 3){
                val.submenusLeft = '0';
            }else if(menuslength == 4){
                val.submenusLeft = '-15px';
            }
        })
    }

    //添加一个一级导航
    $scope.addmenus = function(){
        var menuslength = $scope.menus['menu'].length + 1;
        if(menuslength>=5){
            return;
        }
        var submenusLeft = 0;
        if(menuslength == 1){
            submenusLeft = '90px';
        }
        angular.forEach($scope.menus['menu'],function(val,key){
            val.width = 100/menuslength + '%';
            if(menuslength == 1){
                val.submenusLeft = '90px';
                submenusLeft = '90px';
            }else if(menuslength == 2){
                val.submenusLeft = '22px';
                submenusLeft = '22px';
            }else if(menuslength == 3){
                val.submenusLeft = '0';
                submenusLeft = '0';
            }else if(menuslength == 4){
                val.submenusLeft = '-15px';
                submenusLeft = '-15px';
            }
        })
        $scope.menus['menu'].push(
            {
                title:'标题',
                linkUrl:'',
                linkUrlName:'',
                submenusShow:false,
                submenusLeft:submenusLeft,
                submenus:[],
                width:100/menuslength+'%',
            }
        )
    }
    // 改变一级导航的标题
    $scope.changeTitle = function(menu,$index){
        $scope.cTPosition = 1;
        $scope.menuIndex = $index;
        $('#changeTitleProver').show();
        $('#changeTitleProver').css('top',$('#menus_'+$index).offset().top+15);
        $('#changeTitleProver').css('left',$('#menus_'+$index).offset().left-$('#changeTitleProver').width()/2+10);
    }
    $scope.changesubMenuTitle = function(submenu,$index,outerIndex){
        $scope.cTPosition = 2;
        $scope.menuIndex = outerIndex;
        $scope.submenuIndex = $index;
        $('#changeTitleProver').show();
        $('#changeTitleProver').css('top',$('#subTitle_'+ outerIndex + '_' + $index).offset().top+15);
        $('#changeTitleProver').css('left',$('#subTitle_'+ outerIndex + '_' +$index).offset().left-$('#changeTitleProver').width()/2+10);
    }
    $scope.sureChangeTitle = function(){
        if($scope.cTPosition==1){
            $scope.menus['menu'][$scope.menuIndex]['title'] = $('#title_input').val(); 
        }else if($scope.cTPosition==2){
            $scope.menus['menu'][$scope.menuIndex]['submenus'][$scope.submenuIndex]['title'] = $('#title_input').val(); 
        }
        $('#changeTitleProver').hide();
        $('#title_input').val('');
    }
    $scope.cancelChnageTitle = function(){
        $('#title_input').val('');
        $('#changeTitleProver').hide();
    }

    // 自定义外链
    $scope.setLinkUrl = function($index,pos){
        $scope.cTPosition = 1;
        $scope.menuIndex = $index;
        $('#setWaiLink').show();
        if(pos == 1){
            $('#setWaiLink').css('top',$('#menuLink_'+$index).offset().top+15);
            $('#setWaiLink').css('left',$('#menuLink_'+$index).offset().left-$('#changeTitleProver').width()/2+10);
        }else if(pos == 2){
            $scope.cTPosition =1;
            $('#setWaiLink').css('top',$('#menuAppLink_'+$index).offset().top+20);
            $('#setWaiLink').css('left',$('#menuAppLink_'+$index).offset().left-$('#changeTitleProver').width()/2+10);
        }
    }
    $scope.setsubLinkUrl = function($index,submenu,outerIndex){
        $scope.cTPosition = 2;
        $scope.menuIndex = outerIndex;
        $scope.submenuIndex = $index;
        $('#setWaiLink').show();
        $('#setWaiLink').css('top',$('#subLink_'+outerIndex+$index).offset().top+15);
        $('#setWaiLink').css('left',$('#subLink_'+outerIndex+$index).offset().left-$('#setWaiLink').width()/2+10);
    }
    //修改一级链接
    $scope.changeWaiLink = function(menu,$index){
        $('#setWaiLink').show();
        $scope.cTPosition =1;
        $scope.menuIndex = $index;
        $('#setWaiLink').css('top',$('#menuAppLink_'+$index).offset().top+20);
        $('#setWaiLink').css('left',$('#menuAppLink_'+$index).offset().left-$('#changeTitleProver').width()/2+10);
    }
    // 修改二级练级
    $scope.changesubLinkUrl = function($index,submenu,outerIndex){
        $scope.cTPosition = 2;
        $scope.menuIndex = outerIndex;
        $scope.submenuIndex = $index;
        $('#setWaiLink').show();
        $('#setWaiLink').css('top',$('#changesubLinkUrl_'+outerIndex+$index).offset().top+15);
        $('#setWaiLink').css('left',$('#changesubLinkUrl_'+outerIndex+$index).offset().left-$('#setWaiLink').width()/2+10);
    }
    $scope.sureSetLink = function(){
        var url = $('#wailink_input').val();
        if($('#wailink_input').val().substr(0,8).toLowerCase() != "https://" && $('#wailink_input').val().substr(0,7).toLowerCase() != 'http://'){
            var url = "https://" + $('#wailink_input').val();
        }
        if($scope.cTPosition ==1){
            console.log($('#wailink_input').val())
            $scope.menus['menu'][$scope.menuIndex]['linkUrl'] = url; 
            $scope.menus['menu'][$scope.menuIndex]['linkUrlName'] = url;
        }else if($scope.cTPosition ==2){
            $scope.menus['menu'][$scope.menuIndex]['submenus'][$scope.submenuIndex]['linkUrl'] = url;
            $scope.menus['menu'][$scope.menuIndex]['submenus'][$scope.submenuIndex]['linkUrlName'] = url;
        }
        $('#setWaiLink').hide();
        $('#wailink_input').val(''); 
    }
    $scope.cancelSetLink = function(){
        $('#setWaiLink').hide();
        $('#wailink_input').val(''); 
    }
    // 修改底部图标
    $scope.changeNormal = function($index){
        $scope.menuIndex = $index;
        $scope.cgIconPositon = 1;

        // showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        $scope.eventKind=2;
        $scope.changeImange = true;
        $scope.uploadShow = false;
        $scope.grounps = [];
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                console.log($scope.uploadImages);
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                            // console.log($scope.uploadImages);
                        })
                    }
                });
            })
            $scope.initchooseAdvImage();
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
    }
    //修改高亮图标
    $scope.changeActive = function($index){
        $scope.menuIndex = $index;
        $scope.cgIconPositon = 2;
         $scope.eventKind=2;
        $scope.changeImange = true;
        $scope.uploadShow = false;
        $scope.grounps = [];
        $http.get('/merchants/myfile/getClassify').success(function(data){
            angular.forEach(data.data,function(val,key){
                if(key == 0){
                    val.isactive = true;
                }
                $scope.grounps.push(val);
            })
            var classifyId = data.data[0].id;
            $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(response){
                angular.forEach(response.data[0].data,function(val,key){
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                console.log($scope.uploadImages);
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                            // console.log($scope.uploadImages);
                        })
                    }
                });
            })
            $scope.initchooseAdvImage();
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
    }
    // 删除一个底部高亮图标
    $scope.deleteActiveIcon = function(menu){
        console.log(menu)
        menu.iconActive = '';
    }
    // 隐藏model
    $scope.hideModel = function(){
        commonServer.hideModel();
    }
    $scope.initchooseAdvImage = function(){
        $scope.tempUploadImage =[];
        angular.forEach($scope.uploadImages,function(data,index){
            data.isShow = false;
        })
    }
    // 选择图标图片
    $scope.chooseImage = function(image,$index){
        $scope.initchooseAdvImage();
        if($scope.tempUploadImage.length>1){
            $scope.tempUploadImage = [];
        }else{
            $scope.tempUploadImage.push(image);
        }
        image.isShow = true;
    }
    //上传确定按钮
    $scope.uploadSureBtn = function(){
        $('#myModal-adv').hide();
        $('.modal-backdrop').hide();
        closeUploader();
    }
    // 返回选择图片
    $scope.showImage = function(){
        $scope.uploadShow = false; //判断上传可图片model显示
    }
    
    /*
    *@author huoguanghui
    *商品及分类模态框编写
    */
    // 链接选择商品和分类
    $scope.chooseShop = function($index,position,outerIndex){
        $scope.outerIndex = outerIndex;
        commonServer.showShopModel($index,position,$scope);
    }
    //切换商品及分类
    $scope.switchProductNav = function($index){
        commonServer.switchProductNav($index,$scope);
    }
    //选择营销活动搜索
    $scope.searchProductList = function(){
        commonServer.searchProductList($scope);
    }

    //商品搜索
    $scope.searchGoods = function(){
        commonServer.searchGoods($scope);
    }
    //图片广告选择商品链接
    $scope.chooseShopLink = function($index,list){
        commonServer.chooseShopLink($index,$scope,list)
    }
    // 选择店铺主页点击
    $scope.chooseLinkUrl = function($index,menu,position){
 
        if(menu.dropDown == true){
            menu.dropDown = false;
        }else{
            menu.dropDown = true;
        }
        if(position == 1){
            menu.linkUrlName = '店铺主页';
            menu.linkUrl = '/shop/index/'+ store.id;
        }else if(position == 2){
            menu.linkUrlName = '会员主页';
            menu.linkUrl = '/shop/member/index/'+ store.id;
        }else if(position == 3){
            menu.linkUrlName = '购物车';
            menu.linkUrl = '/shop/cart/index/'+ store.id;
        }else if(position == 4){
            menu.linkUrlName = '拼团';
            menu.linkUrl = '/shop/grouppurchase/index/'+ store.id;
        }else if(position == 5){
            menu.linkUrlName = '微社区';
            menu.linkUrl = '/shop/microforum/forum/index/'+ store.id;
        }else if(position == 6){
            menu.linkUrlName = '联系我们';
            menu.linkUrl = '/shop/contact/index/'+ store.id;
        }
        console.log(menu.linkUrlName);   
    }
    //微页面选择
    $scope.choosePageLink = function($index,position,outerIndex){
        $scope.outerIndex = outerIndex;
        commonServer.choosePageLink($index,position,$scope); 
    }
    //微页面选择确定
    $scope.choosePageLinkSure = function($index,list){
        commonServer.choosePageLinkSure($index,list,$scope);
    }
    //营销活动选择
    $scope.chooseActivity = function($index,position,outerIndex){
        $scope.outerIndex = outerIndex;
         commonServer.chooseActivity($index,position,$scope);
    }
    //营销活动选择确定
    $scope.chooseActivitySure = function($index,list){
        commonServer.chooseActivitySure($index,$scope,list);
    }
    //营销活动nav切换
    $scope.switchNav = function($index){
        commonServer.switchNav($index,$scope);
    }
    // 微页面搜索
    $scope.searchPage = function(){
        commonServer.searchPage($scope);
    }
    uploader.on('uploadSuccess', function (file, response) {
        if (response.status == 1) {
            $scope.$apply(function () {
                // response.data['FileInfo']['s_path'] = _host + response.data['FileInfo']['s_path'];
                // $scope.tempUploadImage.unshift(response.data);
                // console.log($scope.tempUploadImage);
                if($scope.cgIconPositon == 1){
                    $scope.menus.menu[$scope.menuIndex]['icon'] = imgUrl + response.data['FileInfo']['path'];
                }else{
                    $scope.menus.menu[$scope.menuIndex]['iconActive'] = imgUrl + response.data['FileInfo']['path'];
                }
            })
            $('#myModal-adv').hide();
            $('.modal-backdrop').hide();
            closeUploader();
        }
    });
    // 选择图标图片确定
    $scope.chooseAdvSureBtn = function(){
        if($scope.cgIconPositon == 1){
            $scope.menus.menu[$scope.menuIndex].icon = $scope.tempUploadImage[0]['FileInfo']['path'];
        }else if($scope.cgIconPositon == 2){
            $scope.menus.menu[$scope.menuIndex].iconActive = $scope.tempUploadImage[0]['FileInfo']['path'];
        }
        $scope.hideModel();
    }
    $scope.$watch("tempUploadImage",function(newVal,oldVal){
        if($scope.tempUploadImage.length==0){
            $scope.chooseSureBtn = false;
        }else{
            $scope.chooseSureBtn = true;
        }
    },true)


    //顶部导航
    /**
     * 打开微页面弹框
     * @param 当前导航下标
     */
    $scope.openPageModal = function(index){
        $scope.tabBarIndex = index;//下标复制
        $scope.pageData.list = [];//初始化数组
        $scope.pageData.searchTitle = "";//初始化搜索信息
        $.get('/merchants/store/selectPage?page=1', function(data) {

            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageData.list.push({
                        "id":val.id,
                        "name":val.page_title,
                        "created_at":val.created_at,
                        "url":val.url,
                        "is_home":val.is_home
                    })
                })
            })

            var totalCount = data.total, showCount = 5,
                limit = data.pageSize;
            $('#page_nav').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    console.log(page);
                    $.get('/merchants/store/selectPage?page=' + page,function(response){
                        if(response.errCode == 0){
                            $scope.pageData.list = [];
                            angular.forEach(response.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageData.list.push({
                                        "id":val.id,
                                        "name":val.page_title,
                                        "created_at":val.created_at,
                                        "url":val.url,
                                        "is_home":val.is_home
                                    })
                                })
                            })
                        }
                    })
                }
            });
            $("#page_model_nav").modal("show");//微页面弹框显示
        },'json')
    }
    /**
     * 搜索微页面
     */
    $scope.searchPageNav = function(){
        $scope.pageData.list = [];//初始化数组
        $.get('/merchants/store/selectPage?page=1&title=' + $scope.pageData.searchTitle, function(data) {
            angular.forEach(data.data,function(val,key){
                $scope.$apply(function(){
                    $scope.pageData.list.push({
                        "id":val.id,
                        "name":val.page_title,
                        "created_at":val.created_at,
                        "url":val.url,
                        "is_home":val.is_home
                    })
                })
            })

            var totalCount = data.total, showCount = 5,
                limit = data.pageSize;
            $('#page_nav').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    console.log(page);
                    $.get('/merchants/store/selectPage?page=' + page +"&title=" + $scope.pageData.searchTitle,function(response){
                        if(response.errCode == 0){
                            $scope.pageData.list = [];
                            angular.forEach(response.data,function(val,key){
                                $scope.$apply(function(){
                                    $scope.pageData.list.push({
                                        "id":val.id,
                                        "name":val.page_title,
                                        "created_at":val.created_at,
                                        "url":val.url,
                                        "is_home":val.is_home
                                    })
                                })
                            })
                        }
                    })
                }
            });
            $("#page_model_nav").modal("show");//微页面弹框显示
        },'json')
    }
    /**
     * 选择微页面
     */
    $scope.choosePageLinkSureNav = function(item){
        $scope.tabBarList[$scope.tabBarIndex]["pageId"] = item.id;
        $scope.tabBarList[$scope.tabBarIndex]["urlTitle"] = item.name;
        $scope.tabBarList[$scope.tabBarIndex]["is_home"] = item.is_home;
        $scope.tabBarList[$scope.tabBarIndex]["pagePath"] = item.url;
        $("#page_model_nav").modal("hide");//隐藏弹框
    }
    /**
     * 删除导航功能
     * @param item 当前导航数据  index 当前导航下标
     */
    $scope.deleteNavBar = function(item,index){
        $scope.tabBarList.splice(index, 1);
        console.log($scope.tabBarList)
    }
    //获取微商城顶部导航
    //获取店铺导航信息
    $http({ method: 'GET',url: '/merchants/store/selectTopNav'
    }).success(function(response){
        if(response.errCode == 0){
            if(response.data.is_on == 1){
                $scope.is_used_nav = 1;
                $('.js-switch_nav').addClass('ui-switcher-on');
                $('.js-switch_nav').removeClass('ui-switcher-off');
            }else{
                $scope.is_used_nav = 0;
                $('.js-switch_nav').removeClass('ui-switcher-on');
                $('.js-switch_nav').addClass('ui-switcher-off');
            }
            var template = JSON.parse(response.data.template_data),
            colorSeting = JSON.parse(response.data.color_setting)
            $scope.navId = response.id
            console.log(template);
            $scope.tabBarList = template;
            $scope.colorSeting = colorSeting
        }
        // if(response.errCode==0&&response.data.length!=0){
        //     if(response.data.is_used == 1){
        //         $scope.is_used = 1;
        //         $('.js-switch').addClass('ui-switcher-on');
        //         $('.js-switch').removeClass('ui-switcher-off');
        //     }else{
        //         $scope.is_used = 0;
        //         $('.js-switch').removeClass('ui-switcher-on');
        //         $('.js-switch').addClass('ui-switcher-off');
        //     }
        //     angular.forEach($scope.pages,function(val,key){
        //         if(!response.data.apply_page){
        //             val.isCheck = false;
        //         }else{
        //             if(response.data.apply_page.indexOf(val.value) != -1){
        //                 val.isCheck = true;
        //             }else{
        //                 val.isCheck = false;
        //             }
        //         }
        //     })
        //
        //     $scope.id=response.data.id;
        //     $scope.menus=response.data.menu;
        //     angular.forEach($scope.menus.menu,function(val,key){
        //         val.dropDown = true;
        //         val.icon = imgUrl + val.icon;
        //         val.iconActive = imgUrl + val.iconActive
        //     })
        // }
    })
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
    // 是否启用店铺导航点击
    $scope.isOnNav = function(e){
        if($scope.hasClass(e.target,'ui-switcher-on')){
            $scope.is_used_nav = 0 //0未启用顶部导航
        }else{
            $scope.is_used_nav = 1 //开启顶部导航
        }
        console.log($scope.is_used_nav);
    }
    $scope.navFlag = true
    $scope.saveData=function () {
        $scope.postMenus = angular.copy($scope.tabBarList);
        for(var i = 0; i < $scope.postMenus.length; i++){
            if(!$scope.postMenus[i].title || !$scope.postMenus[i].urlTitle){
                tipshow('导航名称与链接不能为空，请重新编辑',"warn");
                return false
            }
        }
        if($scope.postMenus.length > 0){
            angular.forEach($scope.postMenus,function(val,key){
                if(val.pageId != 0){
                    val.pagePath = _host + val.pagePath
                }
            })
        }
        // console.log($scope.postMenus);
        
        var data= {
            is_on:$scope.is_used_nav,
            data:$scope.postMenus,
            color_setting:$scope.colorSeting
        }
        if($scope.navId){
            data.id = $scope.navId
        }
        if($scope.navFlag){
            $scope.navFlag = false
            $http({
                method:'post',
                url:'/merchants/store/processTopNav',
                data:data
            }).success(function(response){
                if(response.errCode==0) {
                    $scope.navFlag = true
                    tipshow('保存成功!');
                }else{
                    $scope.navFlag = true
                    tipshow(response.errMsg);
                }
            }).error(function(response){
                $scope.navFlag = true
            })
        }
    }
}])