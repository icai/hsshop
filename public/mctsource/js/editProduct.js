// add by 黄新琴 2018/8/20
var app = angular.module('myApp', []);
app.controller('myCtrl',['$scope','$sce','$timeout','$http',function($scope, $sce,$timeout,$http) {
    $scope.searchTitle = ''; // 搜索关键字
    $scope.tempSure = false; // 选择商品确定按钮
    $scope.temp = [];
    $scope.tempSure2 = false; // 选择商品确定按钮
    $scope.temp2 = [];
    $scope.tempIds = [];
    $scope.ids = []; //最后保存的商品ids
    $scope.isLookMore = false; //查看更多
    $scope.addProduct = false; //添加商品显示
    $scope.showPro = [];  //页面显示的商品
    $scope.delId = 0;
    $scope.groupModal = true;
    $scope.hasIds = [];
    //添加商品分组
    $scope.chooseShopGroup = function(){
        $scope.goodsGroupList = [];
        $scope.searchTitle = '';
        $scope.temp = [];
        $scope.groupModal = true;
        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page=1'+ '&title=' + $scope.searchTitle).success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                $scope.goodsGroupList.push({
                    "id":val.id,
                    "name":val.title,
                    "created_at":val.created_at
                })
            })
            if(data.status == 1){
                showModel($('#goodslist_model'),$('#goodslist_model_dialog'));
            }
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
            $('.page_shopgroup').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                        if(response.status ==1){
                            $scope.goodsGroupList = [];
                            angular.forEach(response.data[0].data,function(val,key){
                                $scope.goodsGroupList.push({
                                    "id":val.id,
                                    "name":val.title,
                                    "created_at":val.created_at
                                })
                            })
                        }
                    });
                }
            });
        })
    }
    // 商品分组搜索
    $scope.searchShopGroup = function(){
        $scope.goodsGroupList = [];
        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page=1'+ '&title=' + $scope.searchTitle).success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                $scope.goodsGroupList.push({
                    "id":val.id,
                    "name":val.title,
                    "created_at":val.created_at
                })
            })
            if(data.status == 1){
                showModel($('#goodslist_model'),$('#goodslist_model_dialog'));
            }
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.page_shopgroup').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/linkTo/get?type=2&wid='+ wid +'&page='+ page + '&title=' + $scope.searchTitle).success(function(response){
                            if(response.status ==1){
                                $scope.goodsGroupList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.goodsGroupList.push({
                                        "id":val.id,
                                        "name":val.title,
                                        "created_at":val.created_at
                                    })
                                })
                            }
                        });
                    }
                });
        })
    }
    // 隐藏model
    $scope.hideModel = function(){
        hideModel($('#goodslist_model'));
        hideModel($('#myModal'));
    }
     //监听temp有没有数据显示按钮
    $scope.$watch("temp",function(newVal,oldVal){
        if($scope.temp.length==0){
            $scope.tempSure = false;
        }else{
            $scope.tempSure = true;
        }
    },true)
    $scope.$watch("temp2",function(newVal,oldVal){
        if($scope.temp2.length==0){
            $scope.tempSure2 = false;
        }else{
            $scope.tempSure2 = true;
        }
    },true)
    //商品分组选择选取按钮
    $scope.chooseShopGroupSure = function($index,list){
        if(list.isActive){
            list.isActive = false;
            angular.forEach($scope.temp,function(val,key){
                if(list.id == val.id){
                    $scope.temp.splice(key,1);
                }
            })
           
        }else{
            $scope.temp.push(list);
            list.isActive = true;
        }
    }
      //选择分组确定
    $scope.chooseGroupSure = function(){
        if($scope.temp.length>0){
            $scope.tempIds = [];
            angular.forEach($scope.temp,function(val,key){
                $scope.tempIds.push(val.id);
            });
            $http.post('/merchants/marketing/getProductByGroupId',{ids:$scope.tempIds, _token:$('meta[name="csrf-token"]').attr('content')}).success(function(res){
                if(res.status == 1){
                    angular.forEach(res.data,function(val,key){
                        $scope.ids.push(+val.id);
                        val.img = imgUrl + val.img;
                    });
                    if($scope.ids.length>0){
                        $scope.addProduct = true;
                        if ($scope.showPro.length==0){
                            for (var i=0;i<3;i++){
                                res.data[i] && ($scope.showPro[i] = res.data[i])
                            }
                        } else if ($scope.showPro.length==1){
                            $scope.showPro[1] = res.data[0];
                            res.data[1] && ($scope.showPro[2] = res.data[1]);
                        } else if ($scope.showPro.length==2){
                            $scope.showPro[2] = res.data[0];
                        }
                        if($scope.ids.length>3){
                            $scope.isLookMore = true;
                            // $scope.moreNum = $scope.ids.length - 3;
                        }else {
                            $scope.isLookMore = false;
                        }
                    } else {
                        $scope.addProduct = false;
                    }
                }
            });
        }
        hideModel($('#goodslist_model'));
    }
    // 商品列表
    $scope.showProWraper = function(){
        $scope.groupModal = false;
        $scope.searchTitle = '';
        $scope.goodList = [];
        $scope.temp2 = [];
        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page=1&flag=1', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
                var obj = {
                    "id":val.id,
                    "name":val.title,
                    "thumbnail":imgUrl + val.img,
                    "info":"",
                    "price":val.price,
                    "timeDay":val.created_at,
                    "timestamp":"15:57:27" ,
                    "url":'/shop/product/detail/'+val.wid+'/'+val.id
                };
                if ($scope.ids.indexOf(+val.id)>-1){
                   obj.ischecked = true;
                } else {
                    obj.ischecked = false;
                }
                $scope.$apply(function(){
                    $scope.goodList.push(obj)
                })
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page='+page + '&flag=1',function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    var obj = {
                                        "id":val.id,
                                        "name":val.title,
                                        "thumbnail":imgUrl + val.img,
                                        "info":"",
                                        "price":val.price,
                                        "timeDay":val.created_at,
                                        "timestamp":"15:57:27" ,
                                        "url":'/shop/product/detail/'+val.wid+'/'+val.id
                                    };
                                    if ($scope.ids.indexOf(+val.id)>-1){
                                       obj.ischecked = true;
                                    } else {
                                        obj.ischecked = false;
                                    }
                                    $scope.$apply(function(){
                                        $scope.goodList.push(obj)
                                    })
                                })
                            }
                        })
                    }
                });
        },'json')

    }
    // 商品搜索
    $scope.searchGoods = function(){
        $scope.goodList = [];
        $http.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page=1'+ '&title=' + $scope.searchTitle + '&flag=1').success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                var obj = {
                    "id":val.id,
                    "name":val.title,
                    "thumbnail":imgUrl + val.img,
                    "info":"",
                    "price":val.price,
                    "timeDay":val.created_at,
                    "timestamp":"15:57:27" ,
                    "url":'/shop/product/detail/'+val.wid+'/'+val.id
                };
                if ($scope.ids.indexOf(+val.id)>-1){
                   obj.ischecked = true;
                } else {
                    obj.ischecked = false;
                }

                 $scope.goodList.push(obj);

            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page='+ page + '&title=' + $scope.searchTitle + '&flag=1').success(function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                angular.forEach(response.data[0].data,function(val,key){
                                    var obj = {
                                        "id":val.id,
                                        "name":val.title,
                                        "thumbnail":imgUrl + val.img,
                                        "info":"",
                                        "price":val.price,
                                        "timeDay":val.created_at,
                                        "timestamp":"15:57:27" ,
                                        "url":'/shop/product/detail/'+val.wid+'/'+val.id
                                    };
                                    if ($scope.ids.indexOf(+val.id)>-1){
                                       obj.ischecked = true;
                                    } else {
                                        obj.ischecked = false;
                                    }
                                    $scope.goodList.push(obj)

                                })
                            }
                        });
                    }
                });
        })
    }
    //商品选取按钮
    $scope.chooseShopSure = function($index,list){
        if(list.isActive){
            list.isActive = false;
            angular.forEach($scope.temp2,function(val,key){
                if(list.id == val.id){
                    $scope.temp2.splice(key,1);
                } 
            })
        }else{
            $scope.temp2.push({
                id:list.id,
                price: list.price,
                img: list.thumbnail,
                title: list.name
            });
            list.isActive = true;
        }
        
    }
       //选择商品确定
    $scope.chooseSure = function(){
        if($scope.temp2.length>0){
            angular.forEach($scope.temp2,function(val,key){
                if ($scope.ids.indexOf(+val.id) > -1){
                    return;
                } else{
                    $scope.ids.push(+val.id);
                }
            });
            if($scope.ids.length>0){
                $scope.addProduct = true;
                if ($scope.showPro.length==0){
                    for (var i=0;i<3;i++){
                        $scope.temp2[i] && ($scope.showPro[i] = $scope.temp2[i])
                    }
                } else if ($scope.showPro.length==1){
                    $scope.showPro[1] = $scope.temp2[0];
                    $scope.temp2[1] && ($scope.showPro[2] = $scope.temp2[1]);
                } else if ($scope.showPro.length==2){
                    $scope.showPro[2] = $scope.temp2[0];
                }
                if($scope.ids.length>3){
                    $scope.isLookMore = true;
                    // $scope.moreNum = $scope.ids.length - 3;
                }else {
                    $scope.isLookMore = false;
                }
            } else {
                $scope.addProduct = false;
            }
        }
        hideModel($('#goodslist_model'));
    }
    $scope.showProGroupWraper = function(){
        $scope.groupModal = true;
    }
    // 查看更多
    $scope.lookMore = function(){
        $scope.goodList = [];
        $.post('/merchants/marketing/getMore',{ids:$scope.ids,page:1,_token:$('meta[name="csrf-token"]').attr('content')}, function(data) {
           if (data.status == 1){
            angular.forEach(data.data[0].data,function(val,key){
                if ($scope.hasIds.length>0){
                    var obj = {
                        "id":val.id,
                        "name":val.title,
                        "thumbnail":imgUrl + val.img,
                        "info":"",
                        "price":val.price,
                        "timeDay":val.created_at,
                        "timestamp":"15:57:27" ,
                        "url":'/shop/product/detail/'+val.wid+'/'+val.id 
                    };
                    for (var i=0;i<$scope.hasIds.length;i++){
                        if (val.id == $scope.hasIds[i]){
                            obj.isFlag = true;
                        }
                    }
                    $scope.$apply(function(){
                        $scope.goodList.push(obj);
                    })
                } else {
                    $scope.$apply(function(){
                        $scope.goodList.push({
                            "id":val.id,
                            "name":val.title,
                            "thumbnail":imgUrl + val.img,
                            "info":"",
                            "price":val.price,
                            "timeDay":val.created_at,
                            "timestamp":"15:57:27" ,
                            "url":'/shop/product/detail/'+val.wid+'/'+val.id 
                        })
                    })
                }
               
            })
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
            $('.good_pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $.post('/merchants/marketing/getMore', {ids:$scope.ids,page:page, _token:$('meta[name="csrf-token"]').attr('content')},function(response){
                        if(response.status ==1){
                            $scope.goodList = [];
                            angular.forEach(response.data[0].data,function(val,key){
                                if ($scope.hasIds.length>0){
                                    var obj = {
                                        "id":val.id,
                                        "name":val.title,
                                        "thumbnail":imgUrl + val.img,
                                        "info":"",
                                        "price":val.price,
                                        "timeDay":val.created_at,
                                        "timestamp":"15:57:27" ,
                                        "url":'/shop/product/detail/'+val.wid+'/'+val.id 
                                    };
                                    for (var i=0;i<$scope.hasIds.length;i++){
                                        if (val.id == $scope.hasIds[i]){
                                            obj.isFlag = true;
                                        }
                                    }
                                    $scope.$apply(function(){
                                        $scope.goodList.push(obj);
                                    })
                                } else {
                                    $scope.$apply(function(){
                                        $scope.goodList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "thumbnail":imgUrl + val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_at,
                                            "timestamp":"15:57:27" ,
                                            "url":'/shop/product/detail/'+val.wid+'/'+val.id 
                                        })
                                    })
                                }
                            })
                        } else {
                            tipshow(response.info,'warn');
                        }
                    })
                }
            });
            showModel($('#myModal'),$('#modal-dialog'));
           } else {
               tipshow('没有更多商品！','warn');
           }
        },'json')
    }
    $scope.delProduct = function($index,list,type){
        $scope.type = type;
        $scope.delId = list.id;
        $scope.delIndex = $index;
        $('.del-modal').show();
    }
    $scope.delCancle = function(){
        $('.del-modal').hide();
    }
    $scope.delSure = function(){
        if ($scope.ids.indexOf(+$scope.delId) > -1){
            $scope.ids.splice($scope.ids.indexOf(+$scope.delId),1);
        }
        if ($scope.type == 1) {
            $scope.showPro.splice($scope.delIndex,1);
        } else if ($scope.type == 2){
            $scope.goodList.splice($scope.delIndex,1);
            angular.forEach($scope.showPro,function(val,key){
                if (val.id == $scope.delId) {
                    $scope.showPro.splice(key,1);
                }
            })
        }
        $('.del-modal').hide();
    }
    // 保存
    $scope.savePro = function(){
        var title = $('.J_title').val(),
            start_time = $('#start_time').val(),
            end_time = $('#end_time').val(),
            type = $('input[name="discount-type"]:checked').data('type'),
            content = [],
            use_type = $('input[name="product-type"]:checked').data('type'),
            use_content = $scope.ids;
        var obj ={},
            condition,discount,
            timer_type = $('input[name="time"]:checked').data('type');
        if (title == ''){
            tipshow('请设置满减名称','warn');
            return;
        }
        if (title.length>15){
            tipshow('满减名称请控制在15个字以内！','warn');
            return;
        }
        if (timer_type == 1){
            if (start_time == '' || end_time == ''){
                tipshow('请设置满减时间','warn');
                return;
            }
        } else {
            if (start_time == ''){
                tipshow('请设置满减时间','warn');
                return;
            }
        }
        if (type == 1){
            $('.J_profit_money').each(function(i){
                condition = $(this).val();
                discount = $($('.J_desc_money')[i]).val();
                if (condition && discount){
                    obj.condition = condition;
                    obj.discount = discount;
                    content.push(obj);
                    obj = {};
                } else if ((discount && !condition) || (!discount && condition)){
                    tipshow('请补充完成满减利益点','warn');
                    return;
                }
               
            })
        }else if (type == 2){
            $('.J_profit_amount').each(function(i){
                condition = $(this).val();
                discount = $($('.J_desc_amount')[i]).val();
                if (condition && discount){
                    obj.condition = condition;
                    obj.discount = discount;
                    content.push(obj);
                    obj = {};
                } else if ((discount && !condition) || (!discount && condition)){
                    tipshow('请补充完成满减利益点','warn');
                    return;
                }
            })
        }
        if(content.length==0){
            tipshow('至少设置一条满减利益点','warn');
            return;
        }
        if (use_type == 2 && use_content.length == 0) {
            tipshow('请先选择满减商品','warn');
            return;
        }
        var data = {
            title,
            start_time,
            type,
            content,
            use_type,
            use_content,
            _token:$('meta[name="csrf-token"]').attr('content')
        }
        if (timer_type == 1){
            data.end_time = end_time;
        }
        if (tempData.id){
            data.id = tempData.id;
        }
        $http.post('/merchants/marketing/edit',data).success(function(res){
            if (res.status == 1){
                tipshow('保存成功');
                window.location.href = '/merchants/marketing/discountList';
            }else if (res.status == 0 && res.data.length>0){
                $scope.hasIds = res.data;
                tipshow(res.info,'warn');
            } else {
                tipshow(res.info,'warn');
            }
        })

    }
    // 编辑模式渲染数据
    if (tempData.id){
        $scope.ids = tempData.use_content;
        $scope.addProduct = true;
        angular.forEach(tempData.product,function(val){
            val.img = imgUrl + val.img;
        })
        for (var i=0;i<3;i++) {
            tempData.product[i] && ($scope.showPro[i] = tempData.product[i])
        }
        if ($scope.ids.length > 3){
            $scope.isLookMore = true;
        }
    }
}])
