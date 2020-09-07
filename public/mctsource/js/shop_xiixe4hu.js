$(function(){
    // 上传成功
    // console.log(uploader);
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
        val['FileInfo']['s_path'] = imgUrl + val['FileInfo']['s_path'];
        val['ischoose'] = false
    })
    $scope.groupDetail = {
        title: '未分组',
        id: '0',
        isshow: true,
        show: false
    }
    $scope.images = _thumbs[0]['data'];//图片
    $scope.allChoose = false;//是否全选
    $scope.imageIndex = null;//记录图片位置
    $scope.file_classfiy_id = null;//记录图片分组
    angular.forEach(_data, function (val, key) {
        if (key == 0) {
            val['isactive'] = true;
        } else {
            val['isactive'] = false;
        }
    })
    $scope.grounps = _data;
    $scope.preview_img = false;
    console.log($scope.grounps)
    $scope.deletePosition = 1;//1代表删除一张，2代表删除全部
    $scope.uploadImages = function () {
        if(isCreate == 1){
            closeUploader()
            $('#myModal-adv').show();
            $('.modal-backdrop').show();
            showModel($('#myModal-adv'), $('#modal-dialog-adv'));
            $('.webuploader-pick').next('div').css({
                'top': '19px',
                'width': '168px',
                'height': '44px',
                'left': '40%'
            })
        }else{
            tipshow('图片数量已超过上限，请联系客服升级处理','warn')
        }
    }
    // 初始化分页
    var totalCount = _thumbs[0].total, showCount = 10,
        limit = _thumbs[0].per_page;
    $('.pagenavi').extendPagination({
        totalCount: totalCount,
        showCount: showCount,
        limit: limit,
        callback: function (page, limit, totalCount) {
            $http.post('/merchants/myfile/getUserFileByClassify', { page: page }).success(function (data) {
                angular.forEach(data.data[0].data, function (val, key) {
                    val['FileInfo']['s_path'] = imgUrl + val['FileInfo']['s_path'];
                })
                $scope.images = data.data[0].data;
            })
        }
    });
    uploader.on('uploadSuccess', function (file, response) {
        // console.log(response);
        if (response.status == 1) {
            $scope.$apply(function () {
                response.data['FileInfo']['s_path'] = imgUrl + response.data['FileInfo']['s_path'];
                $scope.images.unshift(response.data); 
                console.log($scope.images);
            })
            hideModel($('#myModal-adv'));
        }
    });
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
                // $('.filelist').html('');
            })
            $scope.hideModel();
        }
    }
    $scope.$watch('allChoose', function (newVal, oldVal) {
        if ($scope.images.length > 0) {
            $scope.userFileIds = [];
            angular.forEach($scope.images, function (val, key) {
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
        angular.forEach($scope.images, function (val, key) {
            if (val.ischoose) {
                $('.action-bar .batch-opt').removeClass('b-gray');
            }
        })
    }
    // 确认删除
    $scope.sureDeBtn = function () {
        if ($scope.deletePosition == 2) {
            $scope.userFileIds = [];
            angular.forEach($scope.images, function (val, key) {
                if (val.ischoose) {
                    $scope.userFileIds.push(val.id);
                }
            })
            $http.post('/merchants/myfile/delFile', { userFileIds: $scope.userFileIds }).success(function (data) {
                var arr = [], m;
                $.each($scope.images, function (index, ele) {
                    console.log(this.ischoose);
                    if (!this.ischoose) {
                        m = $scope.images.slice(index, index + 1);
                        arr.push(m[0])
                    }
                })
                angular.forEach($scope.grounps, function (val, key) {
                    if (val.isactive) {
                        val.number = parseInt(val.number) - $scope.userFileIds.length;
                    }
                })
                $scope.images = arr;
            })
        } else {
            $http.post('/merchants/myfile/delFile', { userFileIds: $scope.userFileIds }).success(function (data) {
                var arr = [], m;
                console.log($scope.userFileIds[0]);
                $.each($scope.images, function (index, ele) {
                    if (this.id != $scope.userFileIds[0]) {
                        m = $scope.images.slice(index, index + 1);
                        // console.log(m)
                        arr.push(m[0])
                    }
                })
                angular.forEach($scope.grounps, function (val, key) {
                    if (val.isactive) {
                        val.number = parseInt(val.number) - 1;
                    }
                })
                $scope.images = arr;
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
                $.each($scope.images, function (index, ele) {
                    if (this.id != $scope.userFileIds[0]) {
                        m = $scope.images.slice(index, index + 1);
                        // console.log(m)
                        arr.push(m[0])
                    }

                })
                $scope.images = arr;
            });
        } else {
            $scope.userFileIds = [];
            angular.forEach($scope.images, function (val, key) {
                if (val.ischoose) {
                    $scope.userFileIds.push(val.id);
                }
            })
            // console.log( $scope.userFileIds);
            $http.post('/merchants/myfile/modifyClassify', { userFileIds: $scope.userFileIds, classifyId: $scope.classifyId }).success(function (data) {
                angular.forEach($scope.grounps, function (val, key) {
                    if (val.id == $scope.classifyId) {
                        val.number = parseInt(val.number) + $scope.userFileIds.length;
                    }
                    if (val.isactive) {
                        val.number = parseInt(val.number) - $scope.userFileIds.length;
                    }
                })
                $scope.temp = [];
                angular.forEach($scope.images, function (val, key) {
                    if ($.inArray(val.id, $scope.userFileIds) == -1) {
                        $scope.temp.push(val);
                    }
                })
                $scope.images = $scope.temp;
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
        $http.post('/merchants/myfile/addClassify', { name: name }).success(function (data) {
            console.log(data);
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
        $http.post("/merchants/myfile/addClassify", { name: $scope.groupDetail.title, classifyId: classifyId })
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

        $('input[name="classifyId"]').val(classifyId);
        $http.post('/merchants/myfile/getUserFileByClassify', { classifyId: classifyId }).success(function (data) {
            angular.forEach(data.data[0].data, function (val, key) {
                val['FileInfo']['s_path'] = imgUrl + val['FileInfo']['s_path'];
            })
            $scope.images = data.data[0].data;
            var totalCount = data.data[0].total, showCount = 10,
                limit = data.data[0].per_page;
            $('.pagenavi').extendPagination({
                totalCount: totalCount,
                showCount: showCount,
                limit: limit,
                callback: function (page, limit, totalCount) {
                    $http.post('/merchants/myfile/getUserFileByClassify', { classifyId: classifyId, page: page }).success(function (data) {
                        angular.forEach(data.data[0].data, function (val, key) {
                            val['FileInfo']['s_path'] = imgUrl + val['FileInfo']['s_path'];
                        })
                        $scope.images = data.data[0].data;
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
    $scope.preview_img_url = '';
    $scope.preview_down = '';
    $scope.previewImg = function (img) {
        console.log(img)
        var img_url = imgUrl + img.FileInfo.path
        var preview = new Image();
        // 打印数据
        // 加载完成之后执行
        $scope.preview_img_url = img_url
        preview.onload = function () {
            // 打印数据
            console.log('width:' + preview.width + ', height:' + preview.height);
            if(preview.width > 1100){
                $('#preview_img').html('<img src="'+img_url+'" alt="" style="width:100%">')
            }else if(preview.height > 620){
                $('#preview_img').html('<img src="'+img_url+'" alt="" style="height:100%">')
            }else{
                $('#preview_img').html('<img src="'+img_url+'" alt="" style="width:'+preview.width+'px;height:'+preview.height+'px">')
            }
            $("#preview").removeClass('hide')
        };
        preview.src = img_url;

    }
    $scope.closePreview = function () {
        $("#preview").addClass('hide')
    }
    $scope.downloadImg = function () {
        window.location.href = '/merchants/download?url=' + $scope.preview_img_url
    }
});