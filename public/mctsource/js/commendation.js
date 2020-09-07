
$(function(){
    var index = localStorage.getItem('type') || 1;
    $.get('/merchants/commend/showCommendation',{type:index},function(data){
        
        if(data.errCode == 0){
            if(data.data[0] !== undefined){
                if(data.data[0]['is_auto'] == 1){
                    $('.switch').addClass('actived');
                }else{
                    $('.switch').removeClass('actived');
                }
            }else{
                $('.switch').removeClass('actived')
            }
        }
    })
    //分类点击
    $('.common_nav li').click(function(){
     
        localStorage.setItem('type',$(this).data('type'));
        window.location.reload();
    })
    $('.common_nav li').eq(index-1).addClass('hover');
    var _token = $('meta[name="csrf-token"]').attr('content');
    // 推荐切换
    $('.switch').click(function(){
      var that = $(this);
      if($(this).hasClass('actived')){
        $.post('/merchants/commend/update',{type:index,isAuto:0,_token:_token},function(data){
            that.removeClass('actived');
        })
      }else{
        $.post('/merchants/commend/update',{type:index,isAuto:1,_token:_token},function(data){
            that.addClass('actived');
        })
      }   
    })
})
var app = angular.module('app',[]);
app.controller('myCtrl', function($scope,$timeout,$http) {
    $scope.closeModel = false;
    $scope.type = localStorage.getItem('type') || 1; //1表示享立减，2表示拼团
    $scope.lists = [] // 数据列表
    $scope.navList = [
        {title:'享立减推荐',isActive:true,type:1},
        {title:'拼团推荐',isActive:false,type:2},
    ] //顶部导航 
    $scope.recomments = [];
    
    //添加活动
    $scope.addActivity = function(){
        $scope.closeModel = true;
    }
    $scope.safeApply = function(fn) {
        var phase = this.$root.$$phase;
        if (phase == '$apply' || phase == '$digest') {
            if (fn && (typeof(fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };
    //选择确定
    $scope.chooseSure = function(data){
        $scope.lists = $scope.lists.concat(data);
    }
    //获取数据列表
    $http.get('/merchants/commend/show',{params:{type:$scope.type}}).success(function(data){
        if(data.errCode == 0){
            $scope.lists = data.data;
            $('.text-right .pager_list').extendPagination({
                totalCount: data.total,
                showPage: 10,
                limit: data.pageSize,
                callback: function (page, limit, totalCount) {
                    $http.get('/merchants/commend/show',{params:{type:$scope.type,page:page}}).success(function(res){
                        if(res.errCode == 0){
                            $scope.safeApply(function(){
                                $scope.lists = res.data;
                            })
                        }
                    });
                }
            });
        }
    })
    //移除改推荐
    $scope.removeRecomment = function($index,list,$event){
        showDelProver($($event.target), function(){
            $http.post('/merchants/commend/delete',{id:list.id}).then(function(data){
                if(data.data.errCode == 0){
                    tipshow('移除成功！');
                    setTimeout(function(){
                        window.location.reload();
                    },2000)
                    // $scope.lists.splice($index,1);           
                }else{
                    tipshow(data.data.errMsg,'warn');
                }         
            })
        },'确定要删除吗?')
        $event.stopPropagation();
    }
})
app.directive("testDirective", function ($http) {
    return {
        restrict:"ECMA",
        scope: {
            closeModel: "=",
            type:'=',
            chooseSucc:'=',
            recomments:'='
        },
        templateUrl: HOST + 'mctsource/template/activity.html',
        controller:function($scope){
          
        },
        link:function(scope,ele,attr){
            // 数据
            scope.params = {
                lists:[], //数据列表
                chooseList:[], //选取数据列表
                searchTitle:'' //搜索关键字
            }
            // 关闭弹窗
            scope.close = function(){
                scope.closeModel = false;
            }
            //数据请求
            scope.getData = function(url){
                return $http.get(url).success(function(res){})
            }
            // 循环数据判断是否已经添加了
            scope.eachData = function(){
                if(scope.params.lists.length > 0){
                    angular.forEach(scope.params.lists,function(val,key){
                        if(scope.recomments.indexOf(parseInt(val.id)) != -1){
                            val.isComment = true;
                        }else{
                            val.isComment = false;
                        }
                    })
                }
            }
            //设置分页
            scope.page = function(totalCount,showCount,limit){
                $('.myModalPage').extendPagination({
                    totalCount: totalCount,
                    showPage: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        //享立减
                        if(scope.type == 1){
                            scope.getData('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page='+ page +'&title='+ scope.params.searchTitle).success(function(res){
                                if(res.status == 1){
                                    scope.params.lists = res.data[0].data;
                                    scope.eachData();
                                }
                            });
                        }else if(scope.type == 2){
                            scope.getData('/merchants/group/showGroupList?status=4&page='+ page +'&title='+ scope.params.searchTitle).success(function(res){
                                if(res.errCode == 0){
                                    scope.params.lists = res.data;
                                    scope.eachData();
                                }
                            });
                        }
                    }
                });
            }
            //推荐选择
            scope.recomment = function(list){
                if(list.isActive){
                    list.isActive = false;
                    scope.params.chooseList.splice(scope.params.chooseList.indexOf({id:list.id,title:list.title,recommendation_id:list.recommendation_id}),1);
                    scope.recomments.splice(scope.recomments.indexOf(parseInt(list.id)));
                }else{
                    list.isActive = true;
                    scope.params.chooseList.push({id:list.id,title:list.title,recommendation_id:list.recommendation_id});
                    scope.recomments.push(parseInt(list.id));
                }       
            }
            //选择确定
            scope.chooseSure = function(){
                var ids = [];
                if(scope.params.chooseList.length){
                   
                    $http.post('/merchants/commend/process',{type:scope.type,ids:scope.params.chooseList}).success(function(res){
                        if(res.errCode == 0){
                            
                            window.location.reload();
                        }
                        scope.closeModel = false;
                    })
                }
            }
            //获取已添加活动
            $http.get('/merchants/commend/showDetailID',{params:{type:scope.type}}).success(function(data){
                if(data.errCode == 0){
                    scope.recomments = data.data;
                    scope.applyData();
                }
            })
            
            scope.applyData = function(){
                if(scope.type == 1){
                    //享立减
                    scope.getData('/merchants/linkTo/get?type=16&platform=2&wid='+ wid +'&page=1&title='+ scope.params.searchTitle).success(function(res){
                        if(res.status == 1){
                            scope.params.lists = res.data[0].data;
                            if(scope.params.lists.length == 0){
                                scope.noData = true;
                            }
                            scope.eachData();
                            scope.page(res.data[0]['total'],10,res.data[0]['per_page']);
                        }
                    });
                }
                if(scope.type == 2){
                    // 拼团
                    scope.getData('/merchants/group/showGroupList?status=4&page=1&title='+ scope.params.searchTitle).success(function(res){
                        if(res.errCode == 0){
                            scope.params.lists = res.data;
                            if(scope.params.lists.length == 0){
                                scope.noData = true;
                            }
                            scope.eachData();
                            scope.page(res['total'],10,res['pageSize']);
                        }
                    });
                }
            }
            //搜索活动
            scope.search = function(){
                scope.applyData();
            }
        }
    }
})
angular.bootstrap(document,['app']);