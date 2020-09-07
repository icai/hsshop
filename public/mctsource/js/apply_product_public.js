app.factory('commonServer',function($timeout,$http){
    //资源路径
    var commonServer = {};
    commonServer.index = 0;//editing当前索引值
    commonServer.color = '#ffffff';//富文本设置背景颜色
    commonServer.temp = [];//临时转存数组
    commonServer.tempSure = false;//选择商品确定按钮
    commonServer.chooseSureBtn = false; //选择广告图片确定按钮
    commonServer.tempUploadImage = [];//临时转存数组P
    commonServer.eventKind = 1;//区分点击事件1，为添加广告多图，2为重新上传单图。
    commonServer.advImageIndex = null //重新上传图片索引记录
    commonServer.changeImange = false; //判断是否是member修改图片
    commonServer.advsImagesIndex = 0;//选择商品图片链接判断广告图片位置
    commonServer.shopLinkPosition = 1;//记录选择商品链接位置1为图片广告，2为标题
    commonServer.switchIndex = 1;//记录选择链接的index
    commonServer.callPhoneIndex = null;//联系方式图片index
    commonServer.callPhoneType = 1;//联系方式图片默认自定义
    //添加富文本
    commonServer.addeditor = function($scope,ue,position){
        $scope.removeClassEditing();
        var html = '<div>';
        html += '<p style="margin: 0 0 1em 0;">点此编辑『富文本』内容 ——&gt;</p>';
        html += '<p style="margin: 0 0 1em 0;">你可以对文字进行<strong>加粗</strong>、<em>斜体</em>、<span style="text-decoration: underline;">下划线</span>、<span style="text-decoration: line-through;">删除线</span>、文字<span style="color: rgb(0, 176, 240);">颜色</span>、<span style="background-color: rgb(255, 192, 0); color: rgb(255, 255, 255);">背景色</span>、以及字号<span style="font-size: 20px;">大</span><span style="font-size: 14px;">小</span>等简单排版操作。</p>';
        html += '<p style="margin: 0 0 1em 0;">还可以在这里加入表格了</p>';
        html += '<table><tbody>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">中奖客户</td><td width="104" valign="top" style="word-break: break-all;">发放奖品</td><td width="104" valign="top" style="word-break: break-all;">备注</td></tr>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">猪猪</td><td width="104" valign="top" style="word-break: break-all;">内测码</td><td width="104" valign="top" style="word-break: break-all;"><em><span style="color: rgb(255, 0, 0);">已经发放</span></em></td></tr>';
        html += '<tr><td width="104" valign="top" style="word-break: break-all;">大麦</td><td width="104" valign="top" style="word-break: break-all;">积分</td><td width="104" valign="top" style="word-break: break-all;"><a href="javascript: void(0);" target="_blank">领取地址</a></td></tr>';
        html += '</tbody></table>';
        html += '<p style="text-align: left;"><span style="text-align: left;">也可在这里插入图片、并对图片加上超级链接，方便用户点击。</span></p></div>';
        // var ue = initUeditor('editor');//初始化编辑器
        ue.ready(function() { 
            ue.setContent(''); 
        });
        if(position == 1){
            //1代表底部添加，2代表加内容添加
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':3, //3为富文本，4商品，5商品列表
                    'type':'rich_text',
                    'content':'',
                    'editing':'editing',
                    'bgcolor':'#ffffff',
                    'initShow':true,
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':3, //3为富文本，4商品，5商品列表
                'type':'rich_text',
                'content':'',
                'editing':'editing',
                'bgcolor':'#ffffff',
                'initShow':true,
                'is_add_content':false
            })
        }
        $scope.color = '#ffffff';
        $scope.initCartRight();
    };
    // 左侧点击
    commonServer.tool = function(event,editor,$scope,top,ue){
        editor['editing'] = 'editing';
        editor['is_add_content'] = false;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        event.currentTarget.className += ' editing';
        event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-top);
        $timeout(function(){
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                    $scope.editors[$scope.index].showRight = true;
                    if(event.currentTarget.getAttribute('data-type')=='member'){
                        $scope.editors[$scope.index]['cardRight'] = 1;
                        $timeout(function(){
                            // ue.setContent($('.editing').children('editor-text').html());
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
                            // alert($('.editing').children('editor-text').find('.custom-richtext').html())
                            console.log($('.editing').find('.custom-richtext').html());
                            ue.setContent($('.editing').find('.custom-richtext').html());
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
                }
            }) 
        },200)
    }
    // 初始化右边栏
    commonServer.initCartRight = function(index,$scope,top){
        $scope.baseInfo = false;
        $timeout(function(){
            var ele = document.getElementsByClassName('editing');
            $('.card_right_list').css('margin-top',ele[0].offsetTop-top);
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                    console.log($scope.index);
                }
            })
        },100);
    }
    commonServer.removeClassEditing = function($scope){
        console.log($scope)
        if($scope.editors.length>0){
            angular.forEach($scope.editors,function(data,key){
                data.editing ='';
            })
        }
        $('.app-field').removeClass('editing');
    }
    commonServer.checkModules = function($scope,$index){
        $scope.editors[$scope.index].list[$index].checked = $scope.editors[$scope.index].list[$index].checked ? false : true
        console.log($scope.editors[$scope.index].list[$index].checked,1111111)
    }
    commonServer.checkCaifuyan = function($scope,$index){
        $scope.is_open_weath = $scope.is_open_weath ? 0 : 1;
        $http.post('/merchants/store/isOpenWeath',{is_open_weath:$scope.is_open_weath}).success(function(res){
            console.log(res)
        })
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
 
    //显示model
    commonServer.showModel = function($scope){
        $scope.searchTitle = '';
        $scope.goodList = [];
        var wid = $('#wid').val();
        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page=1', function(data) {
            angular.forEach(data.data[0].data,function(val,key){
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
            })
            // console.log($scope.goodList);
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
                // alert(totalCount)
                $('.good_pagenavi').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $.get('/merchants/linkTo/get?type=1&wid='+ wid +'&page='+page,function(response){
                            if(response.status ==1){
                                $scope.goodList = [];
                                console.log(response);
                                angular.forEach(response.data[0].data,function(val,key){
                                    $scope.$apply(function(){
                                        $scope.goodList.push({
                                            "id":val.id,
                                            "name":val.title,
                                            "thumbnail":imgUrl + val.img,
                                            "info":"",
                                            "price":val.price,
                                            "timeDay":val.created_at,
                                            "timestamp":"15:57:27",
                                            "url":'/shop/product/detail/'+val.wid+'/'+val.id   
                                        })
                                    })
                                })
                            }
                        })
                        // $http.get('/merchants/product/getproducts?ajax=json&page='+page).success(function(response) {
                        //     console.log(response);
                        //     // $scope.goodList = 
                        //     if(response.status ==1){
                        //         $scope.goodList = [];
                        //         angular.forEach(response.data.list,function(val,key){
                        //             $scope.goodList.push({
                        //                 "name":val.title,
                        //                 "thumbnail":val.img,
                        //                 "info":"",
                        //                 "price":val.price,
                        //                 "timeDay":val.created_at,
                        //                 "timestamp":"15:57:27"  
                        //             })
                        //         })
                        //     }
                        // });
                    }
                });
            showModel($('#myModal'),$('#modal-dialog'));
            $('.js-choose').removeClass('btn-primary');//初始化选择按钮
            $('.js-choose').html('选取');
        },'json')
        // $http.get('/merchants/product/getproducts?ajax=json&page=1').success(function(data){
        //     angular.forEach(data.data.list,function(val,key){
        //         $scope.goodList.push({
        //             "name":val.title,
        //             "thumbnail":val.img,
        //             "info":"",
        //             "price":val.price,
        //             "timeDay":val.created_at,
        //             "timestamp":"15:57:27"  
        //         })
        //     })
        //     var totalCount = data.data.total_count, showCount = 10,
        //         limit = data.data.per_page;
        //         $('.pagenavi').extendPagination({
        //             totalCount: totalCount,
        //             showCount: showCount,
        //             limit: limit,
        //             callback: function (page, limit, totalCount) {
        //                 $http.get('/merchants/product/getproducts?ajax=json&page='+page).success(function(response) {
        //                     console.log(response);
        //                     // $scope.goodList = 
        //                     if(response.status ==1){
        //                         $scope.goodList = [];
        //                         angular.forEach(response.data.list,function(val,key){
        //                             $scope.goodList.push({
        //                                 "name":val.title,
        //                                 "thumbnail":val.img,
        //                                 "info":"",
        //                                 "price":val.price,
        //                                 "timeDay":val.created_at,
        //                                 "timestamp":"15:57:27"  
        //                             })
        //                         })
        //                     }
        //                 });
        //             }
        //         });
        //     showModel($('#myModal'),$('#modal-dialog'));
        //     $('.js-choose').removeClass('btn-primary');//初始化选择按钮
        //     $('.js-choose').html('选取');
        // })
    }
   
    // 隐藏model
    commonServer.hideModel = function(){ 
        hideModel($('#myModal'));
        hideModel($('#upload_model'));
        hideModel($('#myModal-adv'));
        hideModel($('#my_coupon_model'));
        hideModel($('#page_model'));
        hideModel($('#chooseShopModel'));
        hideModel($('#page_current_model'));
        hideModel($('#text_image_model'));
        hideModel($('#page_model_pintuan'));
        hideModel($('#page_model_card'));
        hideModel($('#cube_coupon_model'));
    }
    //选择商品
    commonServer.choose = function($index,$scope,list){
        console.log(list.id);
        if($('.choose_btn_'+list.id).hasClass('btn-primary')){
            $('.choose_btn_'+list.id).removeClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('选取'); //改变按钮显示状态
            angular.forEach($scope.temp,function(val,key){
                if(val.id == list.id){
                    $scope.temp.splice(key,1);//清除数据
                }
            })
        }else{
            $('.choose_btn_'+list.id).addClass('btn-primary');//按钮变色
            $('.choose_btn_'+list.id).html('取消'); //改变按钮显示状态
            $scope.temp.push({'id':list.id,'name':list.name,'thumbnail':list.thumbnail,'price':list.price,'info':list.info,'url':list.url});//添加数据
        }
    }
  
    
   
    //显示删除按钮
    commonServer.showDelete = function($index,$scope){
        $scope.editors[$scope.index]['goods'][$index]['deleteBtn'] = true;
    }
    //隐藏删除按钮
    commonServer.hideDelete = function($index,$scope){
        $scope.editors[$scope.index]['goods'][$index]['deleteBtn'] = false;
    }
    //删除图片
    commonServer.delete = function($index,$scope){
        $scope.editors[$scope.index]['products_id'].splice($index,1);
        $scope.editors[$scope.index]['goods'].splice($index,1);
        // angular.forEach($scope.editors[$scope.index]['goods'],function(val,key){
        //     if(val.delete_id == undefined && val.delete_id == image.delete_id){
        //         $scope.editors[$scope.index]['goods'].splice(key,1);
        //     }
        // })
        // console.log(Math.floor($index/3));
        $scope.editors[$scope.index]['thGoods'][Math.floor($index/3)].splice($index%3,1);
        if($scope.editors[$scope.index]['thGoods'][Math.floor($index/3)].length==0){
            $scope.editors[$scope.index]['thGoods'].splice([Math.floor($index/3)],1)
        }
    }
     //删除模块
    commonServer.deleteAll = function($index,$scope){
        $scope.editors.splice($index,1);
        $scope.index--;
        if($scope.index>=0){
            $scope.editors[$scope.index]['showRight'] = false;
        }
    }
    // 广告图片添加
    commonServer.addAdvImages = function($scope,position){
        $scope.removeClassEditing();
        if(position== 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':5, //3为富文本，4商品，5商品列表
                    'type':'image_ad',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'advsListStyle':2, //1默认，2，折叠轮播，3分开显示
                    'advSize':1, //1为大图，2为小图
                    'resize_image':1, //1放大 0 不放大
                    'images':[],
                    'is_add_content':false    
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':5, //3为富文本，4商品，5商品列表
                'type':'image_ad',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'advsListStyle':2, //1默认，2，折叠轮播，3分开显示
                'advSize':1, //1为大图，2为小图
                'resize_image':1, //1放大 0 不放大
                'images':[],
                'is_add_content':false    
            })
        }
        $scope.initCartRight();
    }
    commonServer.initchooseAdvImage = function($scope){
        $scope.tempUploadImage =[];
        angular.forEach($scope.uploadImages,function(data,index){
            data.isShow = false;
        })
    }
    //点击添加广告弹出model
    commonServer.addAdvs = function($scope){
        $scope.uploadShow = false;
        $scope.eventKind=1;
        $scope.grounps = [];
        $scope.choosePosition = 1;//图片广告
        // $scope.eventKind = 1;
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
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
        $scope.initchooseAdvImage();
    }
    // 分享添加图片
    commonServer.addShareImages = function($scope){
        $scope.uploadShow = false;
        $scope.grounps = [];
        $scope.choosePosition = 1;//图片广告
        // $scope.eventKind = 1;
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
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
        $scope.initchooseAdvImage();
    }
    // 点击图片分组
    commonServer.chooseGroup = function($scope,grounp){
        angular.forEach($scope.grounps,function(val,key){
            val.isactive = false;
        })
        var classifyId = grounp.id;
        $('input[name="classifyId"]').val(classifyId);
        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId}).success(function(data){
            angular.forEach(data.data[0].data,function(val,key){
                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
            })
            $scope.uploadImages = data.data[0].data;
            var totalCount = data.data[0].total, showCount = 10,
            limit = data.data[0].per_page;
            $('.ui-pagination').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(data){
                        angular.forEach(data.data[0].data,function(val,key){
                            val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];

                        })
                        $scope.uploadImages = data.data[0].data;
                    })
                }
            });
        })
        grounp.isactive = true;
    }
    // 选择广告图片
    commonServer.chooseImage = function(image,$index,$scope){
        if($scope.eventKind == 1){
            if(image.isShow==false){
                image.isShow=true;
                image['index'] = $index;
                image['chooseLink'] = false; //控制是否显示已经选过链接
                image['dropDown'] = false;
                image['linkName'] = '';
                image['pageCurrent'] = false;
                $scope.tempUploadImage.push(image);
            }else{
                image.isShow=false;
                for(var i=0;i<$scope.tempUploadImage.length;i++){
                    if($scope.tempUploadImage[i]['index']==$index){
                        $scope.tempUploadImage.splice(i,1);
                    }
                }
            }
        }else if($scope.eventKind == 2){
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 3){
            //微页面分享图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 4){
            // 签到分享图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 5){
            // 魔方添加图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }else if($scope.eventKind == 6){
            //联系方式添加图片
            $scope.initchooseAdvImage();
            if($scope.tempUploadImage.length>1){
                $scope.tempUploadImage = [];
            }else{
                $scope.tempUploadImage.push(image);
            }
            image.isShow = true;
        }  
    }
    //选择广告图片确定按钮
    commonServer.chooseAdvSureBtn = function($scope){
        if($scope.tempUploadImage.length>0){
            angular.forEach($scope.tempUploadImage,function(val,key){
                val.image_id = val.FileInfo.id;
            })
        }
        if($scope.eventKind == 1){
            for(var i=0;i<$scope.tempUploadImage.length;i++){
                $scope.safeApply(function(){
                    $scope.image = angular.copy($scope.tempUploadImage[i]);
                    if($scope.image['FileInfo']['path'].indexOf(imgUrl)>=0){
                        $scope.image['FileInfo']['path'] =  $scope.image['FileInfo']['path'];
                    }else{
                        $scope.image['FileInfo']['path'] =  imgUrl + $scope.image['FileInfo']['path'];
                    }
                    $scope.editors[$scope.index]['images'].push($scope.image);
                })
            }
        }else if($scope.eventKind == 2 && !$scope.changeImange){
            if($scope.choosePosition == 1){
                // 图片广告
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]=$scope.tempUploadImage[0];
            }else if($scope.choosePosition == 2){
                // 图片导航
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[$scope.index]['images'][$scope.advImageIndex]['thumbnail'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[$scope.index]['images'][$scope.advImageIndex]['thumbnail'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
                $scope.editors[$scope.index]['images'][$scope.advImageIndex]['image_id'] = $scope.tempUploadImage[0]['FileInfo']['id'];
            }
        }else if($scope.changeImange){
            if($scope.choosePage == 2){
                //会员主页
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[$scope.index]['thumbnail'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[$scope.index]['thumbnail'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
                $scope.editors[$scope.index]['path'] = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else if($scope.choosePage == 1){
                //美妆小店
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['bg_image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['bg_image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }else if($scope.choosePage == 3){
                //冰冰模板背景选择
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['bg_image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['bg_image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }else if($scope.choosePage == 4){
                //冰冰模板修改图标
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['lists'][$scope.advImageIndex]['icon'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['lists'][$scope.advImageIndex]['icon'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }else if($scope.choosePage == 5){
                if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                    $scope.editors[0]['lists'][$scope.advImageIndex]['bg_image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[0]['lists'][$scope.advImageIndex]['bg_image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }
        }else if($scope.eventKind == 3){
            //微页面分享图片选择
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                $scope.pageSeting.share_img = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else{
                $scope.pageSeting.share_img = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
            }
        }else if($scope.eventKind == 4){
            //签到分享
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                $scope.editors[0].share_img = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else{
                $scope.editors[0].share_img = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
            }
        }else if($scope.eventKind == 6){
            //联系方式添加图片
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                if($scope.callPhoneType == 2){
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['icon'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                }else{
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['image'] = $scope.tempUploadImage[0]['FileInfo']['path'];
                } 
            }else{
                if($scope.callPhoneType == 2){
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['icon'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];;
                }else{
                    $scope.editors[$scope.index]['lists'][$scope.callPhoneIndex]['image'] = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
                }
            }
        }else if($scope.eventKind == 5){
            //魔方功能添加图片
            if($scope.tempUploadImage[0]['FileInfo']['path'].indexOf(imgUrl)>=0){
                $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].img = $scope.tempUploadImage[0]['FileInfo']['path'];
            }else{
                $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].img = imgUrl + $scope.tempUploadImage[0]['FileInfo']['path'];
            }
            $scope.editors[$scope.index].isAddPic = true;
            if($scope.cube.selectLayoutIndex == 0){//判断第一个图片设置宽高比
                var img = $scope.editors[$scope.index].content[$scope.cube.selectLayoutIndex].img;
                var realWidth;//真实的宽度
                var realHeight;//真实的高度
                //这里做下说明，$("<img/>")这里是创建一个临时的img标签，类似js创建一个new Image()对象！
                $("<img/>").attr("src", img).load(function() {
                    /*
                    如果要获取图片的真实的宽度和高度有三点必须注意
                    1、需要创建一个image对象：如这里的$("<img/>")
                    2、指定图片的src路径
                    3、一定要在图片加载完成后执行如.load()函数里执行
                    */
                    realWidth = this.width;
                    realHeight = this.height;
                    //如果真实的宽度大于浏览器的宽度就按照100%显示
                    $scope.$apply(function(){
                        $scope.editors[$scope.index].aspectRatio = realHeight/realWidth;//设置高宽比
                    })
                })
            }
            //验证图片是否添加完整
            var isAllImgAdd = true;
            angular.forEach($scope.editors[$scope.index].content,function(val,index){
                if(!val.img){ //有图片未添加
                    isAllImgAdd = false;
                }
            })
            //若是图片添加完整通过判断
            if(isAllImgAdd){
                $scope.editors[$scope.index].isPromptAddPic = false;
            }
        }
        $scope.hideModel();
    }
     //广告图片重新上传
    commonServer.reUpload = function($index,$scope){
        $scope.eventKind=2;
        $scope.changeImange = false;
        $scope.uploadShow = false;
        // $scope.eventKind = 1;
        $scope.choosePosition = 1;//图片广告
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
                    val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                    val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                    val.isShow = false;
                })
                $scope.uploadImages = response.data[0].data;
                var totalCount = response.data[0].total, showCount = 10,
                limit = response.data[0].per_page;
                $('.ui-pagination').extendPagination({
                    totalCount: totalCount,
                    showCount: showCount,
                    limit: limit,
                    callback: function (page, limit, totalCount) {
                        $http.post('/merchants/myfile/getUserFileByClassify',{classifyId:classifyId,page:page}).success(function(response){
                            angular.forEach(response.data[0].data,function(val,key){
                                val['FileInfo']['m_path'] = imgUrl + val['FileInfo']['m_path'];
                                val['FileInfo']['path'] = imgUrl + val['FileInfo']['path'];
                                val.isShow = false;
                            })
                            $scope.uploadImages = response.data[0].data;
                        })
                    }
                });
            })
            showModel($('#myModal-adv'),$('#modal-dialog-adv'));
        })
        $scope.initchooseAdvImage();
        $scope.advImageIndex = $index
        showModel($('#myModal-adv'),$('#modal-dialog-adv'));
    }
    //删除广告图片
    commonServer.removeAdvImages = function($index,$scope){
        $scope.editors[$scope.index]['images'].splice($index,1);
    }
   
    // 显示dropdown
    commonServer.showDown = function($index,position,$scope){
        if(position==1){
            $scope.editors[$scope.index]['images'][$index]['dropDown'] = true;
        }else if(position==2){
            $scope.editors[$scope.index]['dropDown'] = true;
        }else if(position == 5){
            $scope.editors[$scope.index]['textlink'][$index]['dropDown'] = true;
        }
    }
   
    //添加标题
    commonServer.addTitle = function($scope,position){
        console.log(132)
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':6, //3为富文本，4商品，5商品列表，6为标题
                    'type':'title',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'titleName':'',
                    'titleStyle':1,//1为传统样式，2模仿微信图文样式
                    'subTitle':'',
                    'showPosition':1,//1居左显示，2居中，3居右
                    'bgColor':'#ffffff',
                    'addLink':false,//是否添加一个文本链接
                    'chooseLink':false,
                    'dropDown':false,
                    'linkName':'',//链接名字
                    'linkUrl':'',
                    'date':'',
                    'author':'',
                    'wlinkTitle':'',
                    'wlinkUrlChoose':1,//1引导关注，2其他链接
                    'wlinkUrl':'',
                    'is_add_content':false
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':6, //3为富文本，4商品，5商品列表，6为标题
                'type':'title',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'titleName':'',
                'titleStyle':1,//1为传统样式，2模仿微信图文样式
                'subTitle':'',
                'showPosition':1,//1居左显示，2居中，3居右
                'bgColor':'#ffffff',
                'addLink':false,//是否添加一个文本链接
                'chooseLink':false,
                'dropDown':false,
                'linkName':'',//链接名字
                'linkUrl':'',
                'date':'',
                'author':'',
                'wlinkTitle':'',
                'wlinkUrlChoose':1,//1引导关注，2其他链接
                'wlinkUrl':'',
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    // 传统样式添加一个文本链接
    commonServer.addLink = function($scope){
        $scope.editors[$scope.index]['addLink'] = true;
    }
    
    //绑定laydate
    commonServer.bindDate = function(start){
        laydate(start);
    }
   
    //分割线
    commonServer.separatorLine = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':16, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'line',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'请输入标题', //标题
                    // 'required':false, //是否必填
                    'rule_line_idx':4,    
                    'rule_title_idx':0,
                    'rule_desc_idx':0,
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':16, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'line',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'请输入标题', //标题
                // 'required':false, //是否必填
                'rule_line_idx':4,    
                'rule_title_idx':0,
                'rule_desc_idx':0,
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //分割线样式
    commonServer.lineType = function($scope,$index){
        $scope.editors[$scope.index]['rule_line_idx'] = $index
    }
    //标题对齐方式
    commonServer.alignType = function($scope,$index){
        $scope.editors[$scope.index]['rule_title_idx'] = $index
    }
    //描述对齐方式
    commonServer.contAlign = function($scope,$index){
        $scope.editors[$scope.index]['rule_desc_idx'] = $index
    }
    //添加公告
    commonServer.addNotice = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':9, //3为富文本，4商品，5商品列表，6为标题
                    'type':'notice',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'content':'',
                    'placeholder':'请填写内容，如果过长，将会在手机上滚动显示',
                    'is_add_content':false,
                    'colorBg':'#ffffcc',
                    'txtBg':'#ff9900',
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':9, //3为富文本，4商品，5商品列表，6为标题
                'type':'notice',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'content':'',
                'placeholder':'请填写内容，如果过长，将会在手机上滚动显示',
                'is_add_content':false,
                'colorBg':'#ffffcc',
                'txtBg':'#ff9900',
            })
        }
        $scope.initCartRight();
        console.log($scope.editors);
    }
    
     /** 
     * 添加魔方组件
     */
    commonServer.addCube = function($scope,position){
        $scope.cube.selectLayoutIndex = 0;//初始化魔方布局下标
        // console.log(position)
        $scope.removeClassEditing();
        if(position == 1){//添加
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':25, 
                    'type':'cube',
                    'editing':'editing',
                    'is_add_content':false,
                    "isPromptAddPic":false,//是否提示添加图片  默认false
                    "isAddPic":false,//是否添加图片
                    "telType":0,    //模板类型 默认为0  0~7  1行两个~1行5个
                    "margin": 0,    // 图片间隙
                    "addTitle": true, //是否添加蒙版标题
                    "aspectRatio":0,     //图片高宽比
                    "resize_image":1, //1放大0不放大
                    "position":angular.copy($scope.cube.data.position),   //布局
                    "content":angular.copy($scope.cube.data.content) //数据        
                }
            );
        }else{//加内容
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':25, 
                'type':'cube',
                'editing':'editing',
                'is_add_content':false,
                "isAddPic":false,
                "isPromptAddPic":false,
                "telType":0, 
                "margin": 0,
                "addTitle": true,
                "aspectRatio":0,
                "resize_image":1, //1放大0不放大  
                "position":angular.copy($scope.cube.data.position),
                "content":angular.copy($scope.cube.data.content)
            })
        }
        $scope.initCartRight();
    }
    /**
     * 选择魔方模板
     * @param index  当前元素下标
     * 设置模板下标 布局初始化为选中第一个
     * 修改魔方数据
     */
    commonServer.selectedTel = function($scope,index){
        $scope.cube.selectTelIndex = index;
        $scope.cube.selectLayoutIndex = 0;
        $scope.editors[$scope.index].telType = index;//模板类型赋值
        $scope.editors[$scope.index].position = $scope.cube.template[index].position;//布局赋值
        if (index==0 || index==3 || index==4 || index==5) {
            $scope.editors[$scope.index].addTitle = true;
        } else {
            $scope.editors[$scope.index].addTitle = false;
        }
        if($scope.editors[$scope.index].content.length > $scope.cube.template[index].position.length){//魔方数据超出删除
            $scope.editors[$scope.index].content = $scope.editors[$scope.index].content.slice(0,$scope.cube.template[index].position.length);
        }else if($scope.editors[$scope.index].content.length < $scope.cube.template[index].position.length){//魔方数据不足补充
            var list = $scope.cube.template[index].position;
            angular.forEach(list,function(data,index,array){
                var item ={   //魔方数据  初始数据     放在循环内部  在外部存在深浅copy问题
                    type: 0,        //链接类型 1 商品 2 微页面   默认0
                    id: 0,          //当前活动链接id
                    linkTitle:"",   //链接名称
                    img:"",         //选中图片
                    title: ""       //蒙版标题
                }
                if(!$scope.editors[$scope.index].content[index]){//数据不存在 添加数据
                    $scope.editors[$scope.index].content.push(item);
                }
            })
        }
    }
    /**
     * 选择魔方布局
     * @param index  当前元素下标
     */
    commonServer.selectedLayout = function($scope,index){
        $scope.cube.selectLayoutIndex = index;
    }
     /**
     * 魔方滑动组件点击设置间距
     * add by 黄新琴 2018/8/31
     */
    commonServer.changeMargin = function(e,$scope){
        var offX = e.offsetX,
            width = $(e.currentTarget).width();
        var margin = Math.round(offX/width*30);
        $scope.editors[$scope.index].margin = margin;
    }
   
    /*魔方功能end*/

    
    //微页面加内容
    commonServer.addContent = function(event,$index,editor,$scope,top){
        $scope.index = $index;
        $scope.editors[$index]['is_add_content'] = true;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        // console.log(event.currentTarget.offsetParent.offsetParent);
        event.currentTarget.offsetParent.offsetParent.className += ' editing';
        event.currentTarget.offsetParent.offsetParent.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetParent.offsetParent.offsetTop-top);
        $timeout(function(){
            $('.app-field').each(function(key,val){
                if($(this).hasClass('editing')){
                    $scope.index = key;
                    $scope.editors[$scope.index].showRight = true;
                    editor.is_add_content = true
                    console.log(editor);
                    if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='member'){
                        $scope.editors[$scope.index]['cardRight'] = 1;
                        $timeout(function(){
                            // ue.setContent($('.editing').children('editor-text').html());
                            $scope.color = event.currentTarget.offsetParent.offsetParent.style.background;
                        },200);
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='category'){
                        $scope.editors[$scope.index]['cardRight'] = 2;
                        $timeout(function(){
                            var desc = document.getElementsByClassName('page_desc')[0];
                            ue_category.setContent(desc.innerHTML);
                            $scope.color = event.currentTarget.offsetParent.offsetParent.style.background;
                        },200);
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='rich_text'){
                        $scope.editors[$scope.index]['cardRight'] = 3;
                        $timeout(function(){
                            console.log($('.editing').children('editor-text').find('.custom-richtext').html());
                            ue.setContent($('.editing').children('editor-text').find('.custom-richtext').html());
                            $scope.color = event.currentTarget.offsetParent.offsetParent.style.background;
                        },200);
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='goods'){
                        $scope.editors[$scope.index]['cardRight'] = 4;
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='image_ad'){
                        $scope.editors[$scope.index]['cardRight'] = 5;
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='title'){
                        $scope.editors[$scope.index]['cardRight'] = 6;
                    }else if(event.currentTarget.offsetParent.offsetParent.getAttribute('data-type')=='store'){
                        $scope.editors[$scope.index]['cardRight'] = 7;
                    }  
                }
            }) 
        },200)
        event.stopPropagation();
    }
   
  
   
    return commonServer;
});