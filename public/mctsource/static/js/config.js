require.config({ 
    paths: {
        "jquery": "/static/js/jquery-1.11.2.min", 
        "bootstrap": "/static/js/bootstrap", 
        "datetimepicker":"/static/js/bootstrap-datetimepicker.min",  
        "moment":"/static/js/moment-with-locales", 
        "layer": "/static/js/layer/layer",
        "base":"/mctsource/static/js/base",
        "extendPagination":"/static/js/extendPagination",
        "laydate":"/static/js/layer/laydate"
    },
    shim: { //（1）exports值（输出的变量名），表明这个模块外部调用时的名称；（2）deps数组，表明该模块的依赖性。
        jquery: {
            exports: 'jquery'
        }, 
        bootstrap: { 
            deps: ['jquery']
        }, 
        base:{
            deps: ['jquery']
        },
        datetimepicker: {
            deps: ['jquery']
        },
        moment:{
            deps: ['jquery']
        },
        extendPagination:{
            deps: ['jquery']
            // exports: '$'
        }, 
        layer:{ 
            deps: ['jquery']
        }, 
        laydate:{ 
            deps: ['jquery'],
            exports:"laydate"
        } 
    }
});