$(function(){
    // 上传成功
   
    $('body').click(function(){
        $('.ui-popover').hide();
        console.log(1)
    });
    $('.ui-popover').click(function(e){
        e.stopPropagation();
    });

    //图片懒加载 新增
    $("img.lazy").lazyload({effect: "fadeIn"});
}); 

var app = angular.module('myApp', []);
app.controller('myCtrl', function ($scope, $http) {
    angular.forEach(_thumbs[0]['data'], function (val, key) {
        if(val['FileInfo']['s_path'].indexOf(imgUrl) != -1){
            val['FileInfo']['s_path'] = val['FileInfo']['s_path'];
        }else{
            val['FileInfo']['s_path'] = imgUrl + val['FileInfo']['s_path'];
        }
        val['ischoose'] = false
    })
    $scope.groupDetail = {
        title: '未分组',
        id: '0',
        isshow: true,
        show: false
    }
    // $scope.videos = _thumbs[0]['data'];//图片
    $scope.videos = [];
    $scope.allChoose = false;//是否全选
    $scope.imageIndex = null;//记录图片位置
    $scope.file_classfiy_id = null;//记录图片分组
    $scope.videoShow = false; //记录视频弹窗是否显示
    $scope.imgUrl = imgUrl;
    $scope.isEditor = false; //判断是否为编辑视频
    $scope.video = null;
    // $scope.isfirstAjax = false; //是否为第一次请求，优化暂无数
    angular.forEach(_data, function (val, key) {
        if (key == 0) {
            val['isactive'] = true;
        } else {
            val['isactive'] = false;
        }
    })
    $scope.grounps = [];
    $scope.deletePosition = 1;//1代表删除一张，2代表删除全部
    
    
    // 上传点击确定
    $scope.upload_sure = function () {
        $('.modal_content_2').hide();
        $('.modal-backdrop').hide();
    }
    //关闭上传model
    $scope.close_upload = function () {
        $('.modal_content_2').hide();
        $('.modal-backdrop').hide();
    }
    $scope.hideModel = function () {
        $('#myModal-adv').hide();
        $('.modal-backdrop').hide();
    }
    $scope.chooseAdvSureBtn = function () {
        if (!uploadSuccess) {
            $.each($('.filelist li'), function (key, val) {
                $scope.images.push({
                    title: $(this).find('.title').html(),
                    thumbnail: $(this).find('.imgWrap').children('img').attr('src'),
                    linkUrl: '',
                    grounp: '',
                    ischoose: false
                });
                
            })
            $scope.hideModel();
        }
    }
    $scope.$watch('allChoose', function (newVal, oldVal) {
        if ($scope.videos.length > 0) {
            $scope.userFileIds = [];
            angular.forEach($scope.videos, function (val, key) {
                if (newVal) {
                    val.ischoose = true;
                    $scope.userFileIds.push(val.id);
                    $('.action-bar .batch-opt').removeClass('b-gray');
                } else {
                    val.ischoose = false;
                    $scope.userFileIds = [];
                    $('.action-bar .batch-opt').addClass('b-gray');
                }
            })
        }
    })
    // 改变图片名字
    $scope.changeName = function ($index, image,$event) {
         $('.ui-popover').hide();
        $scope.imageIndex = $index;
        $scope.fileId = image.file_info_id;
        $('#changeNameTitle').val(image.FileInfo.name);
        $('#changeNameProver').show();
        $('#changeNameProver').css('top', $('#changeName_' + $index).offset().top + 15);
        $('#changeNameProver').css('left', $('#changeName_' + $index).offset().left - $('#changeNameProver').width() / 2 + 10);
        $event.stopPropagation();
    }
    $scope.changeSureName = function () {
        if ($('#changeNameTitle').val() != '') {
            $http.post('/merchants/myfile/modifyFileName', { name: $('#changeNameTitle').val(), fileId: $scope.fileId }).success(function (data) {
                // console.log(data);
                if (parseInt(data.status)) {
                    angular.forEach($scope.images, function (val, key) {
                        if (val.file_info_id == $scope.fileId) {
                            val.FileInfo.name = $('#changeNameTitle').val();
                        }
                    })
                }
            })
        }
        $scope.images[$scope.imageIndex].title = $('#changeNameTitle').val();
        $('#changeNameProver').hide();
    }
    $scope.cancelChangeName = function () {
        $('#changeNameProver').hide();
    }
    //删除一张图片
    $scope.removeImage = function ($index, image,$event) {
        $('.ui-popover').hide();
        $scope.userFileIds = [];
        $scope.userFileIds.push(image.id);
        $scope.deletePosition = 1;
        $scope.imageIndex = $index;
        $('#image_prover').show();
        $('#image_prover').css('top', $('#delete_' + $index).offset().top + 15);
        $('#image_prover').css('left', $('#delete_' + $index).offset().left - $('#image_prover').height() / 2 - 15);
        // $scope.images.splice($index,1);
        $event.stopPropagation()
    }

    $scope.isChoose = function () {
        $('.action-bar .batch-opt').addClass('b-gray');
        angular.forEach($scope.videos, function (val, key) {
            if (val.ischoose) {
                $('.action-bar .batch-opt').removeClass('b-gray');
            }
        })
    }
    // 确认删除
    $scope.sureDeBtn = function () {
        if ($scope.deletePosition == 2) {
            $scope.userFileIds = [];
            angular.forEach($scope.videos, function (val, key) {
                if (val.ischoose) {
                    $scope.userFileIds.push(val.id);
                }
            })
            $http.post('/merchants/myfile/delFile', { userFileIds: $scope.userFileIds }).success(function (data) {
                var arr = [], m;
                $.each($scope.videos, function (index, ele) {
                    console.log(this.ischoose);
                    if (!this.ischoose) {
                        m = $scope.videos.slice(index, index + 1);
                        arr.push(m[0])
                    }
                })
                angular.forEach($scope.grounps, function (val, key) {
                    if (val.isactive) {
                        val.number = parseInt(val.number) - $scope.userFileIds.length;
                    }
                })
                $scope.videos = arr;
            })
        } else {
            $http.post('/merchants/myfile/delFile', { userFileIds: $scope.userFileIds }).success(function (data) {
                var arr = [], m;
                console.log($scope.userFileIds[0]);
                $.each($scope.videos, function (index, ele) {
                    if (this.id != $scope.userFileIds[0]) {
                        m = $scope.videos.slice(index, index + 1);
                        // console.log(m)
                        arr.push(m[0])
                    }
                })
                angular.forEach($scope.grounps, function (val, key) {
                    if (val.isactive) {
                        val.number = parseInt(val.number) - 1;
                    }
                })
                $scope.videos = arr;
            })
        }
        $('#image_prover').hide();
    }
    // 取消删除
    $scope.cancelDeBtn = function () {
        $('#image_prover').hide();
    }
    // 改变分组
    $scope.changeGrounp = function ($index, image,$event) {
        $('.ui-popover').hide();
        $scope.deletePosition = 1;//1代表删除一张，2代表删除全部
        $scope.userFileIds = [];
        $scope.userFileIds.push(image.id);
        $scope.imageIndex = $index;
        angular.forEach($scope.grounps, function (val, key) {
            val.checked = false;
            if (val.id == image.file_classify_id) {
                val.checked = true;
            }
        })
        $('#changeGrounp').show();
        $('#changeGrounp').css('top', $('#changeGrounp_' + $index).offset().top + 15);
        $('#changeGrounp').css('left', $('#changeGrounp_' + $index).offset().left - $('#changeGrounp').width() / 2 + 10);
        $event.stopPropagation();
    }
    $scope.changeSureBtn = function () {
        // 选中分组id
        $scope.classifyId = $('input[name="category"]:checked').val();
        if ($scope.deletePosition == 1) {
            $http.post('/merchants/myfile/modifyClassify', { userFileIds: $scope.userFileIds, classifyId: $scope.classifyId }).success(function (data) {
                angular.forEach($scope.grounps, function (val, key) {
                    if (val.id == $scope.classifyId) {
                        val.number = parseInt(val.number) + 1;
                    }
                    if (val.isactive) {
                        val.number = parseInt(val.number) - 1;
                    }
                })
                var arr = [], m;
                $.each($scope.videos, function (index, ele) {
                    if (this.id != $scope.userFileIds[0]) {
                        m = $scope.videos.slice(index, index + 1);
                        // console.log(m)
                        arr.push(m[0])
                    }

                })
                $scope.videos = arr;
            });
        } else {
            $scope.userFileIds = [];
            angular.forEach($scope.videos, function (val, key) {
                if (val.ischoose) {
                    $scope.userFileIds.push(val.id);
                }
            })
            // console.log( $scope.userFileIds);
            $http.post('/merchants/myfile/modifyClassify', { userFileIds: $scope.userFileIds, classifyId: $scope.classifyId }).success(function (data) {
                if(data.status){
                    tipshow(data.info);
                    angular.forEach($scope.grounps, function (val, key) {
                        if (val.id == $scope.classifyId) {
                            val.number = parseInt(val.number) + $scope.userFileIds.length;
                        }
                        if (val.isactive) {
                            val.number = parseInt(val.number) - $scope.userFileIds.length;
                        }
                    })
                    $scope.temp = [];
                    angular.forEach($scope.videos, function (val, key) {
                        if ($.inArray(val.id, $scope.userFileIds) == -1) {
                            $scope.temp.push(val);
                        }
                    })
                    $scope.videos = $scope.temp;
                }else{
                    tipshow(data.info,'warn');
                }
            });
        }
        $('#changeGrounp').hide();
    }
    $scope.cancelCgBtn = function () {
        $('#changeGrounp').hide();
    }
    // 添加分组
    $scope.addGrounp = function ($event) {
        $('.ui-popover').hide();
        $('#grounp_title').val('');
        $('#addGpProver').show();
        $('#addGpProver').css('top', $('#addGrounp').offset().top + $('#addGrounp').height());
        $('#addGpProver').css('left', $('#addGrounp').offset().left - $('#addGpProver').outerWidth() / 2 + $('#addGrounp').outerWidth() / 2);
        $event.stopPropagation();
    }
    $scope.addGpSureBtn = function () {
        var name = $('#grounp_title').val();
        $http.post('/merchants/myfile/addClassify', { name: name,file_mine:2 }).success(function (data) {
            $scope.grounps.push({ name: name, number: 0,id: data.data.id});
            $('#addGpProver').hide();
        })
    }
    $scope.cancelGpBtn = function () {
        $('#addGpProver').hide();
    }
    // 改变分组名称
    $scope.changeGroupName = function ($event) {
        $('.ui-popover').hide();
        $('#grounp_title').val('');
        $('#chanageGruopNameProver').show();
        $scope.groupDetail.isshow = true;
        $('#chanageGruopNameProver').css('top', $('.group_name').offset().top + $('.group_name').height());
        $('#chanageGruopNameProver').css('left', $('.group_name').offset().left - $('#chanageGruopNameProver').outerWidth() / 2 + $('.group_name').outerWidth() / 2);
        $event.stopPropagation();
    }
    // 改变分组名称确定点击
    $scope.changeGroupNameSure = function () {
        $scope.groupDetail.isshow = false;
        var classifyId = $scope.groupDetail.id;
        $http.post("/merchants/myfile/addClassify", { name: $scope.groupDetail.title, classifyId: classifyId,file_mine:2 })
            .then(function (res) {
                if (parseInt(res.data.status) == 1) {
                    tipshow(res.data.info);
                    angular.forEach($scope.grounps, function (val, key) {
                        if ($scope.groupDetail.id == val.id) {
                            val.name = $scope.groupDetail.title;
                        }
                    })
                } else {
                    tipshow(res.data.info, 'warn');
                }
            }, 'json')

    }
    // 改变分组名称取消
    $scope.changeGroupNameCancel = function () {
        $scope.groupDetail.isshow = false;
    }
    // 改变所有分组
    $scope.changeAllGrounp = function (pos,$event) {
        $('.ui-popover').hide();
        $scope.deletePosition = 2;//1代表删除一张，2代表删除全部
        // 默认选中
        angular.forEach($scope.grounps, function (val, key) {
            val.checked = false;
            if (val.isactive) {
                val.checked = true;
            }
        })
        $('#changeGrounp').show();
        if (pos == 1) {
            $('#changeGrounp').css('top', $('#changeGrounptop').offset().top + 15);
            $('#changeGrounp').css('left', $('#changeGrounptop').offset().left - $('#changeGrounp').width() / 2 + 20);
        } else if (pos == 2) {
            $('#changeGrounp').css('top', $('#changeGrounpbottom').offset().top + 15);
            $('#changeGrounp').css('left', $('#changeGrounpbottom').offset().left - $('#changeGrounp').width() / 2 + 20);
        }
        $event.stopPropagation();
    }
    //获取分组
    $http.post('/merchants/myfile/getUserFileByClassify', { classifyId: 0,file_mine:2 }).success(function (data) {
        angular.forEach(data.data[0].data, function (val, key) {
            if(val.file_cover == ''){
                val.file_cover = _host + '/hsshop/image/static/video_bg.jpg';
            }else{
                if(val.file_cover.indexOf(imgUrl) != -1){
                    val.file_cover = val.file_cover;
                }else{
                    val.file_cover = imgUrl + val.file_cover;
                }
            }
            if(val['FileInfo']['path'].indexOf(videoUrl) != -1){
                val['FileInfo']['path'] = val['FileInfo']['path'];
            }else{
                val['FileInfo']['path'] = videoUrl + val['FileInfo']['path'];
            }
            val['FileInfo']['size'] = (val['FileInfo']['size']/1000/1000).toFixed(2) + 'MB';
        })
        $scope.videos = data.data[0].data;
        console.log($scope.videos);
        var totalCount = data.data[0].total, showCount = 10,
            limit = data.data[0].per_page;
        $('.pagenavi').extendPagination({
            totalCount: totalCount,
            showCount: showCount,
            limit: limit,
            callback: function (page, limit, totalCount) {
                $http.post('/merchants/myfile/getUserFileByClassify', { classifyId: 0, page: page,file_mine:2 }).success(function (data) {
                    angular.forEach(data.data[0].data, function (val, key) {
                        if(val.file_cover == ''){
                            val.file_cover = _host + '/hsshop/image/static/video_bg.jpg';
                        }else{
                            if(val.file_cover.indexOf(imgUrl) != -1){
                                val.file_cover = val.file_cover;
                            }else{
                                val.file_cover = imgUrl + val.file_cover;
                            }
                        }
                        if(val['FileInfo']['path'].indexOf(videoUrl) != -1){
                            val['FileInfo']['path'] = val['FileInfo']['path'];
                        }else{
                            val['FileInfo']['path'] = videoUrl + val['FileInfo']['path'];
                        }
                        val['FileInfo']['size'] = (val['FileInfo']['size']/1000/1000).toFixed(2) + 'MB';
                    })
                    $scope.videos = data.data[0].data;
                })
            }
        });
    })
    // 分组点击
    $scope.chooseGroup = function (grounp) {
        angular.forEach($scope.grounps, function (val, key) {
            val.isactive = false;
        })
        var classifyId = grounp.id;
        $scope.groupDetail.title = grounp.name;
        $scope.groupDetail.id = grounp.id;
        if ($scope.groupDetail.id == 0) {
            $scope.groupDetail.show = false;
        } else {
            $scope.groupDetail.show = true;
        }
        $http.post('/merchants/myfile/getUserFileByClassify', { classifyId: classifyId,file_mine:2 }).success(function (data) {
            // console.log(data);
            angular.forEach(data.data[0].data, function (val, key) {
                console.log(val);
                if(val['FileInfo']['path'].indexOf(videoUrl) != -1){
                    val['FileInfo']['path'] = val['FileInfo']['path'];
                }else{
                    val['FileInfo']['path'] = videoUrl + val['FileInfo']['path'];
                }
                val['FileInfo']['size'] = (val['FileInfo']['size']/1000/1000).toFixed(2) + 'MB';
                if(val['file_cover'].indexOf(imgUrl) != -1){
                    val['file_cover'] = val['file_cover'];
                }else{
                    val['file_cover'] = imgUrl + val['file_cover'];
                }
            })

            $scope.videos = data.data[0].data;
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
            $('.pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $http.post('/merchants/myfile/getUserFileByClassify', { classifyId: classifyId, page: page,file_mine:2 }).success(function (data) {
                        angular.forEach(data.data[0].data, function (val, key) {
                            if(val['FileInfo']['path'].indexOf(videoUrl) != -1){
                                val['FileInfo']['path'] = val['FileInfo']['path'];
                            }else{
                                val['FileInfo']['path'] = videoUrl + val['FileInfo']['path'];
                            }
                            val['FileInfo']['size'] = (val['FileInfo']['size']/1000/1000).toFixed(2) + 'MB';
                            if(val['file_cover'].indexOf(imgUrl) != -1){
                                val['file_cover'] = val['file_cover'];
                            }else{
                                val['file_cover'] = imgUrl + val['file_cover'];
                            }
                        })
                        $scope.videos = data.data[0].data;
                    })
                }
            });
        })
        grounp.isactive = true;
    }
    // 删除所有图片
    $scope.removeAllImages = function (pos,$event) {
         $('.ui-popover').hide();
        $scope.deletePosition = 2;
        $('#image_prover').show();
        if (pos == 1) {
            $('#image_prover').css('top', $('#delete_top').offset().top + 15);
            $('#image_prover').css('left', $('#delete_top').offset().left - $('#image_prover').height() / 2 - 15);
        } else if (pos == 2) {
            $('#image_prover').css('top', $('#delete_bottom').offset().top + 15);
            $('#image_prover').css('left', $('#delete_bottom').offset().left - $('#image_prover').height() / 2 - 15);
        }
        $event.stopPropagation();
    }
    $scope.safeApply = function (fn) {
        var phase = this.$root.$$phase;
        if (phase == '$apply' || phase == '$digest') {
            if (fn && (typeof (fn) === 'function')) {
                fn();
            }
        } else {
            this.$apply(fn);
        }
    };
    // 删除分组
    $scope.delGroup = function ($event) {
        $('.ui-popover').hide();
        $('#delGroup').show();
        $('#delGroup').css('top', $('.delGroup').offset().top + 15);
        $('#delGroup').css('left', $('.delGroup').offset().left - $('#image_prover').height() / 2 - 15);
        $event.stopPropagation();
    }
    // 确定删除分组
    $scope.sureDelGroup = function () {
        $('#delGroup').hide();
        var id = $scope.groupDetail.id;
        $http.post("/merchants/myfile/delClassify", { classifyId: id })
            .then(function (res) {
                if (res.status == 200) {
                    window.location.reload();
                    $scope.groupDetail = {
                        title: '未分组',
                        id: '0',
                        isshow: true,
                        show: false
                    }
                    for (var i = 0; i < $scope.grounps.length; i++) {
                        if (id == $scope.grounps[i].id) {
                            $scope.grounps.splice(i, 1);
                        }
                    }
                }
            })
    }
    // 隐藏删除分组弹框
    $scope.cancelDelGroup = function () {
        $('#delGroup').hide();
    }
    //关闭视频
    $scope.closeVideo = function(){
        $('.zent-dialog-r-backdrop').hide();
        $('.video_model').hide();
    }
    //播放视频
    $scope.playVideo = function(video){
        
        $('.zent-dialog-r-backdrop').show();
        $('.video_model').show();
        var videoObject = {
            container: '#video', //容器的ID或className
            variable: 'player',//播放函数名称
            poster: video.file_cover,//封面图片
            video: [//视频地址列表形式
                [video.FileInfo.path, 'video/mp4', '中文标清', 0]
            ]
        };
        var player = new ckplayer(videoObject);
    }
    //上传视频
    $scope.uploadVideo = function(){
        if(isCreate == 1){
            $scope.isEditor = false;
            initUploadVideo();
            $('.upload_video').show();
            $('.zent-dialog-r-backdrop').show();
        }else{
            tipshow('视频数量已超过上限，请联系客服升级处理','warn')
        }
    }
    //关闭上传视频弹窗
    $scope.closeUploadVideo = function(){
        $('.upload_video').hide();
        $('.zent-dialog-r-backdrop').hide();
    }
    $('#upload_video').change(function(e){
        var formData = new FormData(),
            sucFormData = {};
        var size = ($(this)[0].files[0]['size']/1204/1024).toFixed(1);
        var default_size = (wid == 303 || wid == 3714) ? 500 : 30;
        if(size>default_size){
            tipshow('上传文件不能大于30M','warn');
            return;
        }
        formData.append('file', $(this)[0].files[0]);
        formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
        formData.append('file_mine', 2);
        $('.rc-video-upload__progress-item-detail-name').html($(this)[0].files[0]['name']);
        $('input[name="video_name"]').val($(this)[0].files[0]['name']);
        if($(this)[0].files[0]['name'].length>10){
            $('.video_name').addClass('has-error');
        }
        
        var filename = hex_md5(new Date().getTime() + parseInt(10000*Math.random())+$(this)[0].files[0]['type'].split("/")[0]) + '.' + $(this)[0].files[0]['type'].split("/")[1];
        
        sucFormData['path'] = filename;
        sucFormData['type'] = $(this)[0].files[0]['type'];
        sucFormData['size'] = $(this)[0].files[0]['size'];
        sucFormData['_token'] = $('meta[name="csrf-token"]').attr('content'); 
        $('.add_video').hide();
        $.get('/merchants/store/getVideoSign',{save_key:filename},function(data){
            if(typeof data == 'string'){
                data = JSON.parse(data);
            }
            formData.append('policy', data.policy);
            formData.append('authorization', data.authorization);
            formData.append('vcodec', 'libmp3lame');
            $.ajax({
                url: 'https://v0.api.upyun.com/huisoucn',
                type: 'POST',
                data: formData,
                dataType:'json',
                cache: false,
                processData: false,
                contentType: false,
                xhr:   function() {
                  var xhr = $.ajaxSettings.xhr(e);
                  if(xhr.upload){
                    $('.rc-video-upload__progress').show();
                    xhr.upload.onprogress = function (e) {
                        var position = e.position || e.loaded, total = e.totalSize || e.total;
                        // var total = event.total,
                        // position = event.loaded || event.position,
                        console.log(position);
                        percent = '';
                        var html = '已上传：' + (position/1204/1024).toFixed(2) + '/' + '共' + (total/1204/1024).toFixed(2) + 'MB';
                        $('.rc-video-upload__progress-item-detail-total').html(html);
                      
                    if(event.lengthComputable){
                        // console.log(position);
                        percent = Math.ceil(position / total * 100)
                        console.log(percent);
                        if(percent == 100 && $('input[name="aggree_input"]').is(':checked') && !$('.video_name').hasClass('has-error')){
                            $('.zent-btn').removeAttr('disabled');
                            $('.zent-btn').addClass('zent-btn-primary')
                        }
                        $('.rc-video-upload__progress-item-progress').css('width',percent + '%');
                      }
                    };
                   
                  }
                  return xhr;
                }
            }).done(function(data, textStatus) {
                
                $.post('/merchants/myfile/setUpxVideo',sucFormData,function(data){
                    if(typeof data == 'string'){
                        data = JSON.parse(data);
                    }
                    $scope.video = data.data;
                    $scope.video['FileInfo']['path'] = videoUrl + $scope.video['FileInfo']['path'];
                    $('input[name="id"]').val(data.data.id);
                    $('input[name="video_url"]').val(data.data.FileInfo.path);
                })
            }).fail(function(res, textStatus, error) {
                try {
                    var body = JSON.parse(res.responseText);
                    alert('error: ' + body.message);
                } catch(e) {
                    console.error(e);
                }
            });
        })
      
    })
    //重新上传视频
    $scope.reUploadVideo = function(){
        $('.rc-video-upload__progress').hide();
        $('input[name="video_name"]').val('');
        $('.add_video').show();
        $('#upload_video').val('');
        $('.video_name').removeClass('has-error');
        $('.rc-video-upload__progress-item-progress').css('width', '0px');
    }
    // //获取分组
    $.get('/merchants/myfile/getClassify',{file_mine:2},function(data){
        if(data.data.length){
            for(var i=0;i<data.data.length;i++){
                $scope.$apply(function(){
                    $scope.grounps.push(data.data[i]);
                })
            }
        }
    })
    //同意点击
    $('input[name="aggree_input"]').click(function(){
        if(!$scope.isEditor){
            if($(this).is(':checked') && $('#upload_video')[0].files[0] && !$('.video_name').hasClass('has-error')){
                btnStatus(2);
            }else{
                btnStatus(1);
            }
        }else{
            if($(this).is(':checked') && !$('.video_name').hasClass('has-error')){
                btnStatus(2);
            }else{
                btnStatus(1);
            }
        }
        
    })
    //商品名称失焦
    $('input[name="video_name"]').keydown(function(){
        if($(this).val().length>10){
            $('.video_name').addClass('has-error');
            btnStatus(1);
        }else{  
            $('.video_name').removeClass('has-error');
            if(!$scope.isEditor){
                if($('input[name="aggree_input"]').is(':checked') && $('#upload_video')[0].files[0]){
                    btnStatus(2);
                }
            }else{
                if($('input[name="aggree_input"]').is(':checked')){
                    btnStatus(2);
                }
            }
        }
    })
    //商品名称聚焦
    $('input[name="video_name"]').focus(function(){
        if($(this).val().length<10){
            if($('input[name="aggree_input"]').is(':checked') && $('#upload_video')[0].files[0]){
                btnStatus(2);
            }
        }
    })
    function btnStatus(type){
        //type1为禁止点击2为允许点击
        if(type == 1){
            $('.zent-btn').attr('disabled','disabled');
            $('.zent-btn').removeClass('zent-btn-primary');
            $('.zent-btn').addClass('zent-btn-disabled');
        }else if(type == 2){
            $('.zent-btn').removeAttr('disabled','disabled');
            $('.zent-btn').addClass('zent-btn-primary');
            $('.zent-btn').removeClass('zent-btn-disabled');
        }
    }
    //封面修改 2018-10-23 by 倪凯嘉
    $('#upload_image').change(function(e){
        var formData = new FormData();
        // var size = $(this)[0].files[0]['size']/1204/1024.toFixed(1);
        var reader = new FileReader();
        reader.readAsDataURL(this.files[0]);
        console.log($(this)[0].files[0]);
        formData.append('file', $(this)[0].files[0]);
        formData.append('_token',$('meta[name="csrf-token"]').attr('content'));
        formData.append('file_mine', 1);
        reader.onload = function(e){
            console.log(e.target);
            var image = new Image();
            image.src = e.target.result;
			image.onload=function(){
				if (image.width>=310) {
                    $.ajax({
                        url:'/merchants/myfile/upfile',
                        type: 'POST',
                        cache: false,
                        processData: false,
                        dataType:'json',
                        data: formData,
                        contentType: false
                    }).done(function(res) {                  
                        //拼接返回的视频地址，这里的vframe/jpb/offset/1是七牛的视频截取图片的接口          
                        // $('.v-box').find('img').attr('src',vistUrl+res.hash+'?vframe/jpg/offset/1').show();                    
                        // $('#videoForm').val(''); 
                        $('.image_views').css('display','inline-block');
                        $('.image_views img').attr('src',imgUrl + res.data.FileInfo.path);              
                        $('input[name="image_url"]').val(res.data.FileInfo.path);
                        $('.add-goods i').html('修改图片');
                        $('.add-goods').addClass('add-goods2').removeClass('add-goods');
                    }).fail(function(res) {
                        console.log(res);
                    });
                }else{
                    tipshow("图片尺寸不符合，请重新上传图片","warn");
							return;
                }
            }
        }
    })
    //保存视频表单
    $('.zent-btn').click(function(){
        var formData = {};
        formData.name = $('input[name="video_name"]').val();
        formData.id = $('input[name="id"]').val();
        formData.classifyId = $('select[name="grounp"]').val();
        formData.file_cover = $('input[name="image_url"]').val();
        formData._token = $('meta[name="csrf-token"]').attr('content');
        // if($scope.isEditor){
        //     formData.id = $('input[name="id"]').val();
        // }
        $.post('/merchants/myfile/modifyVedio',formData,function(data){
            if(data.status == 1){
                tipshow(data.info);
                $('.upload_video').hide();
                $('.zent-dialog-r-backdrop').hide();
                if($scope.isEditor){
                    //编辑
                    angular.forEach($scope.videos,function(val,key){
                        if(val.id == $scope.video.id){
                            $scope.$apply(function(){
                                val.FileInfo.name = formData.name;
                                if(formData.file_cover.indexOf(imgUrl) != -1){
                                    val.file_cover = formData.file_cover;
                                }else{
                                    val.file_cover = imgUrl + formData.file_cover;
                                }
                            })
                        }
                    });
                }else{
                    //新增
                    $scope.active_id = 0;
                    angular.forEach($scope.grounps,function(val,key){
                        if(val.isactive){
                            $scope.active_id = val.id
                        }
                    })
                    if($scope.active_id != $('select[name="grounp"]').val()){
                        return;
                    }
                    $scope.$apply(function(){
                     
                        $scope.videos.unshift($scope.video);
                        $scope.videos[0]['FileInfo']['name'] = formData.name;
                        if(formData.file_cover.indexOf(imgUrl) != -1){
                            $scope.videos[0]['file_cover'] = formData.file_cover;
                        }else{
                            if(formData.file_cover != ''){
                                $scope.videos[0]['file_cover'] = imgUrl + formData.file_cover;
                            }else{
                                $scope.videos[0]['file_cover'] = _host + '/hsshop/image/static/video_bg.jpg';
                            }
                        }
                        $scope.videos[0]['FileInfo']['size'] = ($scope.videos[0]['FileInfo']['size']/1000/1000).toFixed(2) + 'MB';
                    })
                }
            }else{
                tipshow(data.info,'warn');
            }
        })
    })
    //编辑视频上传
    $scope.editVideo = function(video){
        $scope.video = video;
        $scope.isEditor = true;
        $('.add_video').hide();
        $('.rc-video-upload__progress').hide();
        $('input[name="video_name"]').val(video.FileInfo.name);
        if(video.FileInfo.name.length>10){
            $('.video_name').addClass('has-error');
            $('.zent-btn').removeClass('zent-btn-primary');
            $('.zent-btn').attr('disabled');
        }
        $('select[name="grounp"]').val(video.file_classify_id);
        $('input[name="image_url"]').val(video.file_cover);
        //设置编辑id
        $('input[name="id"]').val(video.id);
        if(video.file_cover != ''){
            $('.image_views').css('display','inline-block');
            $('.image_views img').attr('src',video.file_cover);
        }else{
            $('.image_views').css('display','none');
        }
        $('.zent-dialog-r-backdrop').show();
        $('.upload_video').show();
    }
    //初始化上传
    function initUploadVideo(){
        $('.rc-video-upload__progress').hide();
        $('input[name="video_name"]').val('');
        $('.add_video').show();
        $('#upload_video').val('');
        $('.image_views').css('display','none');
        $('.image_views img').attr('src','');
        $('input[name="aggree_input"]').removeAttr("checked");
        $('.video_name').removeClass('has-error');
        $('.zent-btn').removeClass('zent-btn-primary');
        $('.zent-btn').attr('disabled');
        $('.rc-video-upload__progress-item-progress').css('width', '0px');
    }
});