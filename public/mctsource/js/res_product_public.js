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
    commonServer.voteImgIndex = null;//记录选择商品链接位置1为图片广告，2为标题
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
        console.log(editor);
        editor['editing'] = 'editing';
        editor['is_add_content'] = false;
        $('.app-field').css('border','2px dashed rgba(255,255,255,0.5)');
        $('.app-field').removeClass('editing');
        event.currentTarget.className += ' editing';
        event.currentTarget.style.border = '2px dashed rgba(255,0,0,0.5)'; 
        $('.card_right_list').css('margin-top',event.currentTarget.offsetTop-55);
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
            console.log(ele[0].offsetTop);
            $('.card_right_list').css('margin-top',ele[0].offsetTop-55);
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



    //crmember右侧修改背景图
    commonServer.changeBg = function($scope){
        $scope.choosePosition = 1;
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
        hideModel($('#goodslist_model'));
        hideModel($('#component_model'));
        hideModel($('#kill_model'));
        hideModel($('#activity_model'));
        hideModel($('#text_image_model'));
        hideModel($('#my_card_model'));
        hideModel($('#my_card_model'));
        hideModel($('#qq_model'));
        hideModel($('#spell_Modal'));
        hideModel($('#page_spell_model'));
        hideModel($('#video_model'))
        hideModel($('#activity_appointment'))
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
    commonServer.addShareImages = function($scope,$index){
        $scope.uploadShow = false;
        $scope.grounps = [];
        $scope.voteImgIndex = $index
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

    // 添加日期
    commonServer.adddataTime = function($scope){
        $scope.removeClassEditing();
        $scope.editors.push({
            'showRight':true,
            'cardRight':4, //3为富文本，4商品，5商品列表
            'type':'time',
            // 'content':$sce.trustAsHtml(html),
            'editing':'editing',
            'nodate':true,
            'textLength':[
                {
                    'value': 0,
                    'key': '单选时间'
                },
                {
                    'value': 1,
                    'key': '区间时间'
                },
            ],
            'rule_time_type':0,
            'selectedSite':'',
            'title':'时间说明', //标题
            'required':false, //是否必填
            'is_add_content':false
        });
        $scope.initCartRight();//初始化右边
    }

    // 文本框添加
    commonServer.addtext = function($scope){
        $scope.removeClassEditing();
        $scope.editors.push({
            'showRight':true,
            'cardRight':5, //3为富文本，4商品，5商品列表
            'type':'text',
            'editing':'editing',
            'title':'未命名的文字调查',
            'subtitle':'', //副标题
            'required':false, //是否必填
            'rule_text_height': '0',
            'rule_text_width': 0,
            'selectedSite':'',
            'textLength':[
                {
                    'value': 0,
                    'key': '长'
                },
                {
                    'value': 1,
                    'key': '中'
                },
                {
                    'value': 2,
                    'key': '短'
                }
            ],
            'is_add_content':false
        });
        $scope.initCartRight();
    }

    // 添加电话
    commonServer.addTel = function($scope){
        $scope.removeClassEditing();
        $scope.editors.push({
            'showRight':true,
            'cardRight':6, //3为富文本，4商品，5商品列表
            'type':'phone',
            // 'content':$sce.trustAsHtml(html),
            'editing':'editing',
            'nodate':true,
            'min_mobile':'',
            'mobile':'',
            'rule_phone_value':'', //副标题
            'title':'电话咨询', //标题
            'is_add_content':false
        });
        $scope.initCartRight();//初始化右边
    }

    // 添加邮箱
    commonServer.addEmail = function($scope){
        $scope.removeClassEditing();
        $scope.editors.push({
            'showRight':true,
            'cardRight':7, //3为富文本，4商品，5商品列表
            'type':'email',
            // 'content':$sce.trustAsHtml(html),
            'editing':'editing',
            'nodate':true,
            'subhead':'', //副标题
            'title':'你的邮箱？', //标题
            'required':false, //是否必填
            'is_add_content':false
        });
        $scope.initCartRight();//初始化右边
    }

    // 添加图片
    commonServer.addUpload = function($scope){
        $scope.removeClassEditing();
        $scope.editors.push({
            'showRight':true,
            'cardRight':12, //3为富文本，4商品，5商品列表
            'type':'image',
            // 'content':$sce.trustAsHtml(html),
            'editing':'editing',
            'nodate':true,
            'subtitle':'', //副标题
            'title':'请上传一张图片？', //标题
            'required':false, //是否必填
            'is_add_content':false
        });
        $scope.initCartRight();//初始化右边
    }

    //添加文本投票
    commonServer.addTextOption = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':9, //3为富文本，4商品，5商品列表，6为标题
                    'type':'vote_text',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'未命名的投票', //标题
                    'required':false, //是否必填
                    'multiple': '0', //单选 or 多选
                    'qita': false,
                    'max_options':'0',
                    'min_options':'0',
                    'sub_rules':[
                        {
                            'title': '选项名称',
                            'type':'option'
                        },
                        {
                            'title': '选项名称',
                            'type':'option'
                        },
                    ],
                    'is_add_content':false,
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':9, //3为富文本，4商品，5商品列表，6为标题
                'type':'vote_text',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'未命名的投票', //标题
                'required':false, //是否必填
                'multiple': '0', //单选 or 多选
                'qita': false,
                'max_options':'0',
                'min_options':'0',
                'sub_rules':[
                    {
                        'title': '选项名称',
                        'type':'option'
                    },
                    {
                        'title': '选项名称',
                        'type':'option'
                    },
                ],
                'is_add_content':false,
            })
        }
        $scope.initCartRight();
        console.log($scope.editors);
    }
    //删除文本投票中的某一项
    commonServer.closeOpn = function($scope,$index){
        console.log($scope.editors[$scope.index]['sub_rules']);
        if($scope.editors[$scope.index]['sub_rules'][$index].type == 'other'){
            $scope.editors[$scope.index]['qita'] = false
        }
        $scope.editors[$scope.index]['sub_rules'].splice($index,1);
    }
    //添加一个文本投票选项
    commonServer.addOption = function($scope){
        console.log($scope.editors[$scope.index]);
        $scope.editors[$scope.index]['sub_rules'].push({
            'title': '选项名称',
            'type':'option'
        })
    }
    //添加一个文本其他选项
    commonServer.addQita = function($scope){
        console.log($scope.editors[$scope.index]['sub_rules']);
        $scope.editors[$scope.index]['sub_rules'].push({
            'title': '其他',
            'type':'other'
        })
        $scope.editors[$scope.index]['qita'] = true
    }


    // 添加图片投票
    commonServer.addImages = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':11, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'vote_image',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'未命名的投票', //标题
                    'required':false, //是否必填
                    'multiple': '0', //单选 or 多选
                    'max_options':'0',
                    'min_options':'0',
                    'rule_image_type': '0', // 单列 or 多列
                    'sub_rules':[
                        {
                            'image': '../../../../../mctsource/images/demo.gif',
                            'imgflag': false,
                            'title': '',
                            'type':'option'
                        },
                        {
                            'image': '../../../../../mctsource/images/demo.gif',
                            'imgflag': false,
                            'title': '',
                            'type':'option'
                        }
                    ],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':11, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'vote_image',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'未命名的投票', //标题
                'required':false, //是否必填
                'multiple': '0', //单选 or 多选
                'max_options':'0',
                'min_options':'0',
                'rule_image_type': '0', // 单列 or 多列
                'sub_rules':[
                    {
                        'image': '../../../../../mctsource/images/demo.gif',
                        'imgflag': false,
                        'title': '',
                        'type':'option'
                    },
                    {
                        'image': '../../../../../mctsource/images/demo.gif',
                        'imgflag': false,
                        'title': '',
                        'type':'option'
                    }
                ],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    // 选择投票图片
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
        }
    }
    //选择图片确定按钮
    commonServer.chooseAdvSureBtn = function($scope){
        console.log($scope.tempUploadImage);
        console.log($scope.eventKind);
        console.log($scope.editors[$scope.index]);
        console.log($scope.voteImgIndex);
        if($scope.tempUploadImage.length>0){
            angular.forEach($scope.tempUploadImage,function(val,key){
                val.image_id = val.FileInfo.id;
            })
        }
        console.log($scope.editors[$scope.index]['sub_rules'][$scope.voteImgIndex]);
        $scope.editors[$scope.index]['sub_rules'][$scope.voteImgIndex].image = $scope.tempUploadImage[0].FileInfo.path
        $scope.editors[$scope.index]['sub_rules'][$scope.voteImgIndex].imgflag = true
        $scope.editors[$scope.index]['sub_rules'][$scope.voteImgIndex].rule_image_flag  = true
        $scope.hideModel();
    }
    //删除图片确定按钮
    commonServer.removeShareImg = function($scope,$index){
        console.log($scope.editors[$scope.index]);

        console.log($scope.editors[$scope.index]['sub_rules'][$index]);
        $scope.editors[$scope.index]['sub_rules'][$index].image = '../../../../../mctsource/images/demo.gif'
        $scope.editors[$scope.index]['sub_rules'][$index].imgflag = false
        $scope.editors[$scope.index]['sub_rules'][$index].rule_image_flag  = false
    }
    //图片重新上传
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
    //添加一个图片选项
    commonServer.addImgOpn = function($scope){
        console.log($scope.editors[$scope.index]['sub_rules']);
        $scope.editors[$scope.index]['sub_rules'].push({
            'image': '../../../../../mctsource/images/demo.gif',
            'imgflag': false,
            'title': '',
            'type':'option'
        })
        // $scope.editors[$scope.index]['thVote'].splice($index,1);
    }
    //删除图片投票中的某一项
    commonServer.removeImgVote = function($scope,$index){
        console.log($scope.editors[$scope.index]['sub_rules']);
        $scope.editors[$scope.index]['sub_rules'].splice($index,1);
    }

    //删除活动
    commonServer.deleteActive = function($scope){
        $scope.editors[$scope.index].content = [];//清空数据
    }

    //文本预约添加
    commonServer.txtBooking = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':13, //3为富文本，4商品，5商品列表，6为标题
                    'type':'appoint_text',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'选择预约人', //标题
                    'required':false, //是否必填
                    'selectedSite':'',
                    'rule_appoint_default':'',
                    'rule_appoint_type':'0',
                    'default_num':0,
                    'sub_rules':[
                        {
                            'title': '请选择',
                            'type':'option'
                        },
                        {
                            'title': '选项',
                            'type':'option'
                        },
                        {
                            'title': '选项',
                            'type':'option'
                        }
                    ],
                    'is_add_content':false,
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':13, //3为富文本，4商品，5商品列表，6为标题
                'type':'appoint_text',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'选择预约人', //标题
                'required':false, //是否必填
                'selectedSite':'',
                'rule_appoint_default':'',
                'rule_appoint_type':'0',
                'sub_rules':[
                    {
                        'title': '请选择',
                        'type':'option'
                    },
                    {
                        'title': '选项',
                        'type':'option'
                    },
                    {
                        'title': '选项',
                        'type':'option'
                    }
                ],
                'is_add_content':false,
            })
        }
        $scope.initCartRight();
        console.log($scope.editors);
    }
    //添加一个文本预约选项
    commonServer.addBookingOpt = function($scope){
        console.log($scope.editors[$scope.index]);
        if($scope.editors[$scope.index]['sub_rules'].length <= 7){
            $scope.editors[$scope.index]['sub_rules'].push({
                'title': '选项',
                'type':'option'
            })
        }else{
            tipshow('预约人选项最多不能超过6个','warn');
        }

    }
    //删除文本预约中的某一项
    commonServer.removeBookingOpt = function($scope,$index){
        console.log($scope.editors[$scope.index]['sub_rules']);
        $scope.editors[$scope.index]['sub_rules'].splice($index,1);
    }

    // 添加图片预约
    commonServer.addImaBooking = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':14, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'appoint_image',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'选择预约人', //标题
                    'required':false, //是否必填
                    'sub_rules':[
                        {
                            'image': '../../../../../mctsource/images/demo.gif',
                            'imgflag': false,
                            'title': '',
                            'type':'option'
                        },
                        {
                            'image': '../../../../../mctsource/images/demo.gif',
                            'imgflag': false,
                            'title': '',
                            'type':'option'
                        }
                    ],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':14, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'appoint_image',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'选择预约人', //标题
                'required':false, //是否必填
                'sub_rules':[
                    {
                        'image': '../../../../../mctsource/images/demo.gif',
                        'imgflag': false,
                        'title': '',
                        'type':'option'
                    },
                    {
                        'image': '../../../../../mctsource/images/demo.gif',
                        'imgflag': false,
                        'title': '',
                        'type':'option'
                    }
                ],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //添加一个图片预约选项
    commonServer.addimgBookingOpn = function($scope){
        console.log($scope.editors[$scope.index]['sub_rules']);
        if($scope.editors[$scope.index]['sub_rules'].length <= 6){
            $scope.editors[$scope.index]['sub_rules'].push({
                'image': '../../../../../mctsource/images/demo.gif',
                'imgflag': false,
                'title': '',
                'type':'option'
            })
        }else{
            tipshow('预约人选项最多不能超过6个','warn');
        }

        // $scope.editors[$scope.index]['thVote'].splice($index,1);
    }
    //删除图片预约中的某一项
    commonServer.removeimgBooking = function($scope,$index){
        $scope.editors[$scope.index]['sub_rules'].splice($index,1);
    }

    // 添加图片预约
    commonServer.addSeparator = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':15, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'address',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'未命名的地域调查', //标题
                    'required':false, //是否必填
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':15, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'address',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'未命名的地域调查', //标题
                'required':false, //是否必填
                'is_add_content':false
            })
        }
        $scope.initCartRight();
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
    //数字
    commonServer.addNum = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':17, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'num',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'请输入标题', //标题
                    'unit':'', //单位
                    'required':false, //是否必填
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':17, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'num',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'请输入标题', //标题
                'unit':'', //单位
                'required':false, //是否必填
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //预约时段
    commonServer.timeBooking = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':18, //3为富文本，4商品，5商品列表，6为标题
                    'type':'appoint_time',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'预约时段', //标题
                    'required':false, //是否必填
                    'selectedSite':'',
                    'rule_appoint_default':'',
                    'rule_appoint_type':'0',
                    'default_num':0,
                    'sub_rules':[
                        {
                            'title': '请选择',
                            'type':'option'
                        },
                        {
                            'title': '选项',
                            'type':'option',
                            'rule_appoint_type':0
                        },
                        {
                            'title': '选项',
                            'type':'option',
                            'rule_appoint_type':0
                        }
                    ],
                    'is_add_content':false,
                }
            );
        }else if(position == 2){
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':18, //3为富文本，4商品，5商品列表，6为标题
                'type':'appoint_time',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'预约时段', //标题
                'required':false, //是否必填
                'selectedSite':'',
                'rule_appoint_default':'',
                'rule_appoint_type':'0',
                'sub_rules':[
                    {
                        'title': '请选择',
                        'type':'option'
                    },
                    {
                        'title': '选项',
                        'type':'option',
                        'rule_appoint_type':0
                    },
                    {
                        'title': '选项',
                        'type':'option',
                        'rule_appoint_type':0
                    }
                ],
                'is_add_content':false,
            })
        }
        $scope.initCartRight();
    }
    //外观样式
    commonServer.faceType = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':19, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'face_type',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'表头标题', //标题
                    'required':false, //是否必填
                    'bg_color':'#ffffff',
                    // 'sub_rules':[
                    //     {
                    //         'image': '',
                    //         'rule_image_flag': false,
                    //         'title': '',
                    //         'type':'option'
                    //     }
                    // ],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':19, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'face_type',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'表头标题', //标题
                'required':false, //是否必填
                'bg_color':'#ffffff',
                'sub_rules':[
                    {
                        'image': '',
                        'rule_image_flag': false,
                        'title': '',
                        'type':'option'
                    }
                ],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    //图片设置
    commonServer.imgSet = function($scope,position){
        $scope.removeClassEditing();
        if(position == 1){
            $scope.editors.push(
                {
                    'showRight':true,
                    'cardRight':20, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                    'type':'set_image',
                    // 'content':$sce.trustAsHtml(html),
                    'editing':'editing',
                    'subtitle':'', //副标题
                    'title':'请输入标题', //标题
                    'required':false, //是否必填
                    'sub_rules':[
                        {
                            'image': '',
                            'rule_image_flag': false,
                            'title': '',
                            'type':'option'
                        }
                    ],
                    'is_add_content':false
                }
            );
        }else{
            $scope.editors.splice($scope.index+1,0,{
                'showRight':true,
                'cardRight':20, //3为富文本，4商品，5商品列表，6为标题 10为商品分组右侧
                'type':'set_image',
                // 'content':$sce.trustAsHtml(html),
                'editing':'editing',
                'subtitle':'', //副标题
                'title':'请输入标题', //标题
                'required':false, //是否必填
                'sub_rules':[
                    {
                        'image': '',
                        'rule_image_flag': false,
                        'title': '',
                        'type':'option'
                    }
                ],
                'is_add_content':false
            })
        }
        $scope.initCartRight();
    }
    return commonServer;
});
