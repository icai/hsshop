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
app.directive('detaTime',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_detaTime.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('crdetaTime',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_detaTime.html',
        link:function(scope,ele,attr){
        }
    }
})

// 广告图添加
app.directive('text',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_text.html',
        link:function(scope,ele,attr){
        }
    }
})

//广告添加编辑部分
app.directive('crtext',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_text.html',
        link:function(scope,ele,attr){
        }
    }
})
// 文本预约添加
app.directive('txtbooking',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_txtBooking.html',
        link:function(scope,ele,attr){
        }
    }
})

//文本预约编辑部分
app.directive('crtxtbooking',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_txtBooking.html',
        link:function(scope,ele,attr){
        }
    }
})
// 图片预约添加
app.directive('imgbooking',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_imgBooking.html',
        link:function(scope,ele,attr){
        }
    }
})

//图片预约编辑部分
app.directive('crimgbooking',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_imgBooking.html',
        link:function(scope,ele,attr){
        }
    }
})

// 分隔线
app.directive('separator',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_separator.html',
        link:function(scope,ele,attr){
        }
    }
})

//分隔线
app.directive('crseparator',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_separator.html',
        link:function(scope,ele,attr){
        }
    }
})


//标题
app.directive('tel',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_tel.html',
        link:function(scope,ele,attr){
        }
    }
})

app.directive('crtel',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_tel.html',
        link:function(scope,ele,attr){
        }
    }
})

// 进入店铺
app.directive('email',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_email.html',
        link:function(scope,ele,attr){
        }
    }
})
app.directive('cremail',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_email.html',
        link:function(scope,ele,attr){
        }
    }
})

// 公告左侧
app.directive('textVote',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_textVote.html',
        link:function(scope,ele,attr){
        }
    }
})
//公告右侧
app.directive('crtextVote',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_textVote.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品搜索左侧
app.directive('imgVote',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_imgVote.html',
        link:function(scope,ele,attr){
        }
    }
})
//商品搜索右侧
app.directive('crimgVote',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_imgVote.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品列表
app.directive('upload',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_upload.html',
        link:function(scope,ele,attr){
        }
    }
})
// 商品列表右侧
app.directive('crupload',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_upload.html',
        link:function(scope,ele,attr){
        }
    }
})
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
//数字
app.directive('num',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_num.html',
        link:function(scope,ele,attr){
        }
    }
})
// 数字右侧
app.directive('crnum',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_num.html',
        link:function(scope,ele,attr){
        }
    }
})
//预约时段
app.directive('timebooking',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_timebooking.html',
        link:function(scope,ele,attr){
        }
    }
})
// 预约时段右侧
app.directive('crtimebooking',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_timebooking.html',
        link:function(scope,ele,attr){
        }
    }
})
//外观样式
app.directive('faceType',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_faceType.html',
        link:function(scope,ele,attr){
        }
    }
})
// 外观样式右侧
app.directive('crfaceType',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_faceType.html',
        link:function(scope,ele,attr){
        }
    }
})
//图片设置
app.directive('imgSet',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/res_imgSet.html',
        link:function(scope,ele,attr){
        }
    }
})
// 图片设置右侧
app.directive('crimgSet',function($compile){
    return{
        restrict:'AE',
        scope:true,
        templateUrl: HOST + 'mctsource/template/cr_res_imgSet.html',
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