require.config({ 
    baseUrl: '/static/js',
    paths: {
        "jquery": "jquery-1.11.2.min",
        "jquery_raty":"jquery.raty.min",
        "bootstrap": "bootstrap",
        "echarts":"echarts.min",
        "layer": "layer/layer",
        "laydate": "layer/laydate",
        "chosen":"chosen.jquery.min",
        "bootstrapValidator":"bootstrapValidator.min",
        "angular":"angular.min",
        "ueditor":"UEditor/ueditor.config",
        "ueditorall":"UEditor/ueditor.all.min",
        "zh_cn":"UEditor/lang/zh-cn/zh-cn",
        "webuploader":"webuploader",
        "model":"model",
        "moment":"moment/moment.min",
        "locales":"moment/locales.min",
        "bootstrap_datetimepicker":"bootstrap-datetimepicker.min",
        "lazyload":"jquery.lazyload",
        "codemirror":"UEditor/third-party/codemirror/codemirror",
        "ZeroClipboard":"UEditor/third-party/zerozlipboard/ZeroClipboard",
    },
    shim: { //（1）exports值（输出的变量名），表明这个模块外部调用时的名称；（2）deps数组，表明该模块的依赖性。
        jquery: {
            exports: 'jquery'
        },
        bootstrap: {
            deps: ['jquery']
        },
        layer:{
            exports:"layer",
            deps: ['jquery']
        },
        chosen:{
            exports:'chosen',
            deps: ['jquery']
        },
        bootstrapValidator:{
            exports:'bootstrapValidator',
            deps: ['jquery']
        },
        angular:{
            exports:'angular'
        },
        laydate:{
            exports:'aydate',
            deps: ['jquery']
        },
        ueditor:{
            exports:'ueditor'
        },
        ueditorall:{
            exports:'ueditorall',
            deps:['ueditor','ueditorall']
        },
        zh_cn:{
            exports:'zh_cn'
        },
        webuploader:{
            exports:'webuploader',
            deps: ['jquery']
        },
        model:{
            exports:'model',
            deps:['angular','model']
        },
        moment:{
            exports:'moment',
            deps: ['jquery']
        },
        locales:{
            exports:'locales',
            deps: ['jquery']
        },
        bootstrap_datetimepicker:{
            exports:'bootstrap-datetimepicker',
            deps: ['locales','moment']
        },
        jquery_raty:{
            exports:'jquery_raty',
            deps: ['jquery']
        },
        lazyload:{
            exports:'lazyload',
            deps:['jquery']
        },
        codemirror:{
            exports:'codemirror'
        },
        ZeroClipboard:{
            exports:'ZeroClipboard'
        },
        echarts:{
            exports:'echarts'
        }
    }
});