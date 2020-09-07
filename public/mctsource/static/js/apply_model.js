var app = angular.module('myApp', ['ngDraggable']);

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
        templateUrl: HOST + 'mctsource/template/cr_apply_title.html',
        link:function(scope,ele,attr){
        }
    }
})
// 富文本
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

//分割线
app.directive('separatorLine',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_line.html',
        link:function(scope,ele,attr){
        }
    }
})
// 分割线右侧
app.directive('crseparatorLine',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_line.html',
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
        templateUrl: host + 'mctsource/template/cr_apply_cube.html',
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
        templateUrl: HOST + 'mctsource/template/cr_apply_advs.html',
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

//联系方式
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