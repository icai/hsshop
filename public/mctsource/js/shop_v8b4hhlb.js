app.factory('commonServer',function($timeout){
    var commonServer = {};
    commonServer.index = null;//editing当前索引值
    commonServer.color = '#ffffff';//富文本设置背景颜色
    commonServer.temp = [];//临时转存数组
    commonServer.tempSure = false;//选择商品确定按钮
    commonServer.chooseSureBtn = false; //选择广告图片确定按钮
    commonServer.tempUploadImage = [];//临时转存数组
    commonServer.eventKind = 1;//区分点击事件1，为添加广告多图，2为重新上传单图。
    commonServer.advImageIndex = null //重新上传图片索引记录
    commonServer.changeImange = false; //判断是否是member修改图片
    commonServer.addeditor = function($scope){
        $scope.removeClassEditing();
        $scope.editors.push(
            {
                'showRight':true,
                'cardRight':3, //3为富文本，4商品，5商品列表
                'type':'rich_text',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'bgcolor':'#fff'
            }
        );
        // $scope.crSetting['cardRight'] = 3;
        // console.log($('.editing').offset());
        // var ele = document.getElementsByClassName('editing');
        // console.log(ele[0].offsetTop);
        // console.log($('.editing'));
        $scope.color = '#ffffff';
        $scope.initCartRight();
        // console.log(ele[0].offsetTop); 
        // console.log(ele);   
        // $('.card_right').css('margin-top',$('.editing').offset().top-70)
        // console.log($scope.editors);
    };
    // 左侧点击
    commonServer.tool = function(event,editor,$scope){
        // console.log(event.target.parentNode);
        // console.log($event.target.parentNode.offsetTop);
        // console.log(event.currentTarget.parentNode.parentNode);
        $scope.baseInfo = false;    
        editor['editing'] = 'editing';
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        event.currentTarget.className += ' editing';
        event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-80);
        $timeout(function(){
             $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                }
                $scope.editors[$scope.index]['showRight'] = true;
                if(event.currentTarget.getAttribute('data-type')=='member'){
                    $scope.editors[$scope.index]['cardRight'] = 1;
                    $timeout(function(){
                        ue.setContent($('.editing').children('editor-text').html());
                        $scope.color = event.currentTarget.style.background;
                    },200);
                }else if(event.currentTarget.getAttribute('data-type')=='category'){
                    $scope.editors[$scope.index]['cardRight'] = 2;
                    $timeout(function(){
                        var desc = document.getElementsByClassName('page_desc')[0];
                        ue_category.setContent(desc.innerHTML);
                        $scope.color = event.currentTarget.style.background;
                    },200);
                }else if(event.currentTarget.getAttribute('data-type')=='rich_text'){
                    $scope.editors[$scope.index]['cardRight'] = 3;
                    $timeout(function(){
                        ue.setContent($('.editing').children('editor-text').html());
                        $scope.color = event.currentTarget.style.background;
                    },200);
                }else if(event.currentTarget.getAttribute('data-type')=='goods'){
                    $scope.editors[$scope.index]['cardRight'] = 4;
                }else if(event.currentTarget.getAttribute('data-type')=='image_ad'){
                    $scope.editors[$scope.index]['cardRight'] = 5;
                }else if(event.currentTarget.getAttribute('data-type')=='title'){
                    $scope.editors[$scope.index]['cardRight'] = 6;
                }else if(event.currentTarget.getAttribute('data-type')=='store'){
                    $scope.editors[$scope.index]['cardRight'] = 7;
                }
            }) 
        },100)
    }
    // 初始化右边栏
    commonServer.initCartRight = function(index,$scope){
        $scope.baseInfo = false;
        $timeout(function(){
            var ele = document.getElementsByClassName('editing');
            $('.card_right_list').css('margin-top',ele[0].offsetTop-80);
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                }
            })
        },100);
    }
    commonServer.removeClassEditing = function($scope){
        if($scope.editors.length>0){
            angular.forEach($scope.editors,function(data){
                data.editing ='';
            })
        }
    }
    commonServer.addboder = function(editor,$scope){
        // console.log(editor.currentTarget);
        // editor.currentTarget.parentNode.parentNode.childNodes[9].style.display ='block'
        editor.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'
    }
    commonServer.removeboder = function($event,editor,$scope){
        // console.log(editor);
        $timeout(function(){
            if(editor['editing'] !='editing'){
                $event.currentTarget.style.border = '2px dashed rgba(255,255,255,0.5)';
            }
        },100)
    }
    return commonServer;
});