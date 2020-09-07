var app = angular.module('myApp', ['ngDraggable']);

app.directive('editorText',function($compile){
    return{
        restrict:'AE',
        templateUrl: HOST + 'mctsource/template/richtext.html',
        scope:true,
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crRichtext',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crrichtext.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('goods',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/goodsHtml.html',
        link:function(scope,ele,attr){
            // ele.on("click", function() {
            //     scope.$apply(function() {
            //         // var content = $compile(template)(scope);
            //         element.append(content);
            //    })
            // });
            scope.safeApply = function(fn){
                var phase = this.$root.$$phase;
                if (phase == '$apply' || phase == '$digest') {
                    if (fn && (typeof(fn) === 'function')) {
                        fn();
                    }
                } else {
                    this.$apply(fn);
                }
            };
            
        }
    }
})
app.directive('crGoods',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crgoods.html',
        link:function(scope,ele,attr){
        }
    }
})

// 广告图添加
app.directive('advs',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/advs.html',
        link:function(scope,ele,attr){
        }
    }
})

//广告添加编辑部分
app.directive('cradvs',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cradvs.html',
        link:function(scope,ele,attr){
        }
    }
})

//标题
app.directive('addTitle',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/addTitle.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crtitle',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crtitle.html',
        link:function(scope,ele,attr){
        }
    }
})

// 进入店铺
app.directive('shop',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/addshop.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crshop',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crshop.html',
        link:function(scope,ele,attr){
        }
    }
})

// 会员中心默认模板定义
app.directive('crMember',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_member.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('member',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/memebertext.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('memberlist',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/memberlist.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crMemberlist',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_memberList.html',
        link:function(scope,ele,attr){
        }
    }
})
//新建页面导航默认模板定义
app.directive('category',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/category.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crcategory',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crcategory.html',
        link:function(scope,ele,attr){
        }
    }
})

//基本信息
app.directive('baseinfo',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/baseinfo.html',
        link:function(scope,ele,attr){
        }
    }
})

// 商品分组左侧
app.directive('goodsGroup',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/goodsGroupHtml.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品分组右侧
app.directive('crgoodsGroup',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crgoodsGroupHtml.html',
        link:function(scope,ele,attr){
        }
    }
})

// 优化券左侧
app.directive('coupon',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/coupon.html',
        link:function(scope,ele,attr){
        }
    }
})
//优惠券右侧
app.directive('crcoupon',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crcoupon.html',
        link:function(scope,ele,attr){
        }
    }
})
// 公告左侧
app.directive('notice',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/notice.html?t=11',
        link:function(scope,ele,attr){
        }
    }
})
//公告右侧
app.directive('crnotice',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crnotice.html?t=11',
        link:function(scope,ele,attr){
        }
    }
})
// 商品搜索左侧
app.directive('search',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/search.html',
        link:function(scope,ele,attr){
        }
    }
})
//商品搜索右侧
app.directive('crsearch',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crsearch.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品列表
app.directive('goodslist',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/goodslist.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品列表右侧
app.directive('crgoodslist',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crgoodslist.html',
        link:function(scope,ele,attr){
        }
    }
})
// 自定义模块左侧
app.directive('model',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/model.html',
        link:function(scope,ele,attr){
        }
    }
})
// 自定义模块右侧
app.directive('crmodel',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crmodel.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品分组左侧
app.directive('goodgroup',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/goodgroup.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品分右侧
app.directive('crgoodgroup',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crgoodgroup.html',
        link:function(scope,ele,attr){
        }
    }
})
// 图片导航左侧
app.directive('imagelink',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/imagelink.html',
        link:function(scope,ele,attr){
        }
    }
})
// 图片导航右侧
app.directive('crimagelink',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crimagelink.html',
        link:function(scope,ele,attr){
        }
    }
})
// 文本导航
app.directive('textlink',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/textlink.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crtextlink',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crtextlink.html',
        link:function(scope,ele,attr){
        }
    }
})
//营销活动左侧
app.directive('active',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/active.html',
        link:function(scope,ele,attr){
        }
    }
})
//营销活动右侧
app.directive('cractive',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl:  HOST + 'mctsource/template/cractive.html',
        link:function(scope,ele,attr){
        }
    }
})
// 头部导航
// 文本导航
app.directive('header',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/header.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crheader',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crheader.html',
        link:function(scope,ele,attr){
        }
    }
})
// 图文素材官网模板
app.directive('imageTextModel',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/imageTextModel.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crimageTextModel',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crimageTextModel.html',
        link:function(scope,ele,attr){
        }
    }
})
// 会员卡
app.directive('membercard',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: host + 'mctsource/template/membercard.html',
        link:function(scope,ele,attr){
        }
    }
})
// 会员卡右侧
app.directive('crmembercard',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crmembercard.html',
        link:function(scope,ele,attr){
        }
    }
})
// 团购商品列表
app.directive('spellgoods',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/spellGoods.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crspellgoods',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crspellGoods.html',
        link:function(scope,ele,attr){
        }
    }
})
//团购分类
app.directive('spelltitle',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/spellTitle.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crspelltitle',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crspellTitle.html',
        link:function(scope,ele,attr){
        }
    }
})
//视频模块
app.directive('cvideo',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/video.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crvideo',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crvideo.html',
        link:function(scope,ele,attr){
        }
    }
})
//魔方
app.directive('cube',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: host + 'mctsource/template/cube.html',

        link:function(scope,ele,attr){
        }
    }
})
app.directive('crcube',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: host + 'mctsource/template/crcube.html',
        link:function(scope,ele,attr){
        }
    }
})



app.filter('to_trusted', ['$sce', function ($sce) {
    return function (text) {
        return $sce.trustAsHtml(text);
    };
}]);
app.directive('ensureInteger',function($http) {
    return {
        require: 'ngModel',
        link: function(scope, ele, attrs, c) {
            scope.$watch(attrs.ngModel,
            function(n) {
                var reg =  /^(0|([1-9]\d*))(\.\d+)?$/;
                // console.log(n);
                if (reg.test(String(n))){
                    c.$setValidity('integer',true);
                }else{
                    c.$setValidity('integer',false);
                }
            });

        }

    }

});

//手机号
app.directive('mobile',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: host + 'mctsource/template/mobile.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crmobile',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: host + 'mctsource/template/crmobile.html',
        link:function(scope,ele,attr){
        }
    }
})
app.filter('to_trusted', ['$sce', function ($sce) {
    return function (text) {
        return $sce.trustAsHtml(text);
    };
}]);
app.directive('ensureInteger',function($http) {
    return {
        require: 'ngModel',
        link: function(scope, ele, attrs, c) {
            scope.$watch(attrs.ngModel,
            function(n) {
                var reg =  /^1\d{10}$/g;
                // console.log(n,11111);
                if (reg.test(String(n))){
                    c.$setValidity('integer',true);
                }else{
                    c.$setValidity('integer',false);
                }
            });

        }

    }})

//享立减商品
app.directive('sharegoods',function($compile){

    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/shareGoods.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crsharegoods',function($compile){
    
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crshareGoods.html',
        link:function(scope,ele,attr){
        }
    }
})

//留言板
app.directive('research',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_research.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crResearch',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_cr_research.html',
        link:function(scope,ele,attr){
        }
    }
})

// 在线投票
app.directive('researchVote',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_research_vote.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crResearchVote',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_cr_research_vote.html',
        link:function(scope,ele,attr){
        }
    }
})

// 在线预约
app.directive('researchAppoint',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_research_appoint.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crResearchAppoint',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_cr_research_appoint.html',
        link:function(scope,ele,attr){
        }
    }
})

// 在线报名
app.directive('researchSign',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_research_sign.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crResearchSign',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/xcx_cr_research_sign.html',
        link:function(scope,ele,attr){
        }
    }
})
/** 
 * author 华亢 at 2018/08/28
 * toDO create a link about secondkill
*/
app.directive('secondKill',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/secondKill.html',
        link:function(scope,ele,attr){
            
        }
    }
})
app.directive('crSecondKill',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crSecondKill.html',
        link:function(scope,ele,attr){
            
        }
    }
})
/**
 * author 韩瑜 
 * date 2018-9-13
 * 分类模板组件
*/
app.directive('crgroupPage',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crgroupPage.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('groupPage',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/groupPage.html',
        link:function(scope,ele,attr){
        }
    }
})
//end
/**
 * author 韩瑜 
 * date 2018-11-28
 * 商品分组模板组件
*/
app.directive('crgroupTemplate',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/crgroupTemplate.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('groupTemplate',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/groupTemplate.html',
        link:function(scope,ele,attr){
        }
    }
})
//end