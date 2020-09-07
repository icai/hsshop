//上传图片
var classifyId=0;//分组 id
var resultSrc = [];//结果数组
var picTextType =false;//false 默认单挑图文
var _token =$("meta[name='csrf-token']").attr("content");//_token值
var select_num = $("#select_num").val();//选择数 1-20 填写1为单选，其他多选
var callback = getQueryString("callback");
var typeId = getQueryString('type')
/*获取参数*/
function getQueryString(name) { 
    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i"); 
    var r = window.location.search.substr(1).match(reg); 
    if (r != null) return unescape(r[2]); return null; 
}
$(".btn_left").on('click', function () {
    var name = $('.add_group_input').val();
    if(!name){
        return false
    }
    $.ajax({
        url:'/merchants/myfile/addClassify',
        type: 'POST',
        data:{
            name:name,
            _token:_token,
        },
        success:function (data) {
            console.log(data);
            if(data.status == 1){
                var _group = '<li class="js-category-item" data-id="'+data.data.id+'">'+data.data.name+'\
                            <span>0</span>\
                        </li>';
                $('.category-list').append(_group);
                $(".add_group_box").addClass('hide')
            }
        }
    })
})
$(".btn_right").on('click',function () {
    $(".add_group_box").addClass('hide')
    $('.add_group_list').attr('data-id','1');
})
$(".add_group_list").on('click',function () {
    var id = $(this).attr('data-id');
    if(id == 1){
        $(this).attr('data-id','2');
        $(".add_group_box").removeClass('hide')
    }else {
        $(this).attr('data-id','1');
        $(".add_group_box").addClass('hide')
    }
})
function myupload(fileNumLimit){
    (function( $ ){
        // 当domReady的时候开始初始化
        $(function() {
            var $wrap = $('#uploader'),
                $queue = $( '<ul class="filelist"></ul>' ).appendTo( $wrap.find( '.queueList' ) ),
                $statusBar = $wrap.find( '.statusBar' ),// 状态栏，包括进度和控制按钮
                $info = $statusBar.find( '.infos' ),// 文件总体选择信息。
                $upload = $wrap.find( '.uploadBtn' ),// 上传按钮
                $placeHolder = $wrap.find( '.placeholder' ),// 没选择文件之前的内容。
                $progress = $statusBar.find( '.progress' ).hide(),
                fileCount = 0,// 添加的文件数量
                fileSize = 0,// 添加的文件总大小
                ratio = window.devicePixelRatio || 1,// 优化retina, 在retina下这个值是2
                thumbnailWidth = 110 * ratio,// 缩略图大小
                thumbnailHeight = 110 * ratio,
                state = 'pedding',// 可能有pedding, ready, uploading, confirm, done.
                percentages = {}, // 所有文件的进度信息，key为file id
                isSupportBase64 = ( function() { // 判断浏览器是否支持图片的base64
                    var data = new Image();
                    var support = true;
                    data.onload = data.onerror = function() {
                        if( this.width != 1 || this.height != 1 ) {
                            support = false;
                        }
                    } 
                    data.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";
                    return support;
                } )(),
                flashVersion = ( function() {// 检测是否已经安装flash，检测flash的版本
                    var version;
                    try {
                        version = navigator.plugins[ 'Shockwave Flash' ];
                        version = version.description;
                    } catch ( ex ) {
                        try {
                            version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                                    .GetVariable('$version');
                        } catch ( ex2 ) {
                            version = '0.0';
                        }
                    }
                    version = version.match( /\d+/g );
                    return parseFloat( version[ 0 ] + '.' + version[ 1 ], 10 );
                } )(),
                supportTransition = (function(){
                    var s = document.createElement('p').style,
                        r = 'transition' in s ||
                                'WebkitTransition' in s ||
                                'MozTransition' in s ||
                                'msTransition' in s ||
                                'OTransition' in s;
                    s = null;
                    return r;
                })(),
                uploader;
            if ( !WebUploader.Uploader.support('flash') && WebUploader.browser.ie ) {

                // flash 安装了但是版本过低。
                if (flashVersion) {
                    (function(container) {
                        window['expressinstallcallback'] = function( state ) {
                            switch(state) {
                                case 'Download.Cancelled':
                                    alert('您取消了更新！')
                                    break;

                                case 'Download.Failed':
                                    alert('安装失败')
                                    break;

                                default:
                                    alert('安装已成功，请刷新！');
                                    break;
                            }
                            delete window['expressinstallcallback'];
                        };

                        var swf = './expressInstall.swf';
                        // insert flash object
                        var html = '<object type="application/' +
                                'x-shockwave-flash" data="' +  swf + '" ';

                        if (WebUploader.browser.ie) {
                            html += 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
                        }

                        html += 'width="100%" height="100%" style="outline:0">'  +
                            '<param name="movie" value="' + swf + '" />' +
                            '<param name="wmode" value="transparent" />' +
                            '<param name="allowscriptaccess" value="always" />' +
                        '</object>';

                        container.html(html);

                    })($wrap);

                // 压根就没有安转。
                } else {
                    $wrap.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
                }

                return;
            } else if (!WebUploader.Uploader.support()) {
                alert( 'Web Uploader 不支持您的浏览器！');
                return;
            }
            var _token = $('meta[name="csrf-token"]').attr('content');
            // 实例化
            uploader = WebUploader.create({
                pick: {
                    id: '#filePicker',
                    innerHTML: '点击选择图片',
                    multiple: false
                },
                formData: {
                    uid: 123,
                    _token:_token,
                    classifyId:classifyId
                },
                dnd: '#dndArea',
                paste: '#uploader',
                swf: './js/Uploader.swf',
                chunked: false,
                chunkSize: 512 * 1024,
                server: '/merchants/myfile/upfile',
                // runtimeOrder: 'flash',
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/gif,image/jpg,image/jpeg,image/bmp,image/png'
                },
                // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
                disableGlobalDnd: true,
                fileNumLimit: fileNumLimit,
                fileSizeLimit: 200 * 1024 * 1024,    // 200 M
                fileSingleSizeLimit: 3 * 1024 * 1024    // 3 M
            });

            // 拖拽时不接受 js, txt 文件。
            uploader.on( 'dndAccept', function( items ) {
                var denied = false,
                    len = items.length,
                    i = 0,
                    // 修改js类型
                    unAllowed = 'text/plain;application/javascript ';

                for ( ; i < len; i++ ) {
                    // 如果在列表里面
                    if ( ~unAllowed.indexOf( items[ i ].type ) ) {
                        denied = true;
                        break;
                    }
                }

                return !denied;
            });
            // uploader.on('filesQueued', function() {
            //     uploader.sort(function( a, b ) {
            //         if ( a.name < b.name )
            //           return -1;
            //         if ( a.name > b.name )
            //           return 1;
            //         return 0;
            //     });
            // });

            // 添加“添加文件”的按钮，
            // uploader.addButton({
            //     id: '#filePicker2',
            //     innerHTML: '继续添加',
            //     multiple: false
            // });

            uploader.on('ready', function() {
                window.uploader = uploader;
            });

            // 当有文件添加进来时执行，负责view的创建
            function addFile( file ) {
                var $li = $( '<li id="' + file.id + '">' +
                        '<p class="title">' + file.name + '</p>' +
                        '<p class="imgWrap"></p>'+
                        '<p class="progress"><span></span></p>' +
                        '</li>' ),

                    $btns = $('<div class="file-panel">' +
                        '<span class="cancel">删除</span>' +
                        '<span class="rotateRight">向右旋转</span>' +
                        '<span class="rotateLeft">向左旋转</span></div>').appendTo( $li ),
                    $prgress = $li.find('p.progress span'),
                    $wrap = $li.find( 'p.imgWrap' ),
                    $info = $('<p class="error"></p>'),

                    showError = function( code ) {
                        switch( code ) {
                            case 'exceed_size':
                                text = '文件大小超出';
                                break;

                            case 'interrupt':
                                text = '上传暂停';
                                break;

                            default:
                                text = '上传失败，请重试';
                                break;
                        }

                        $info.text( text ).appendTo( $li );
                    };

                if ( file.getStatus() === 'invalid' ) {
                    showError( file.statusText );
                } else {
                    // @todo lazyload
                    $wrap.text( '预览中' );
                    uploader.makeThumb( file, function( error, src ) {
                        var img;

                        if ( error ) {
                            $wrap.text( '不能预览' );
                            return;
                        }

                        if( isSupportBase64 ) {
                            img = $('<img src="'+src+'">');
                            $wrap.empty().append( img );
                        } else {
                            $.ajax('../../server/preview.php', {
                                method: 'POST',
                                data: src,
                                dataType:'json'
                            }).done(function( response ) {
                                if (response.result) {
                                    img = $('<img src="'+response.result+'">');
                                    $wrap.empty().append( img );
                                } else {
                                    $wrap.text("预览出错");
                                }
                            });
                        }
                    }, thumbnailWidth, thumbnailHeight );

                    percentages[ file.id ] = [ file.size, 0 ];
                    file.rotation = 0;
                }

                file.on('statuschange', function( cur, prev ) {
                    if ( prev === 'progress' ) {
                        $prgress.hide().width(0);
                    } else if ( prev === 'queued' ) {
                        $li.off( 'mouseenter mouseleave' );
                        $btns.remove();
                    }

                    // 成功
                    if ( cur === 'error' || cur === 'invalid' ) {
                        console.log( file.statusText );
                        showError( file.statusText );
                        percentages[ file.id ][ 1 ] = 1;
                    } else if ( cur === 'interrupt' ) {
                        showError( 'interrupt' );
                    } else if ( cur === 'queued' ) {
                        percentages[ file.id ][ 1 ] = 0;
                    } else if ( cur === 'progress' ) {
                        $info.remove();
                        $prgress.css('display', 'block');
                    } else if ( cur === 'complete' ) {
                        $li.append( '<span class="success"></span>' );
                    }

                    $li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
                });

                $li.on( 'mouseenter', function() {
                    $btns.stop().animate({height: 30});
                });

                $li.on( 'mouseleave', function() {
                    $btns.stop().animate({height: 0});
                });

                $btns.on( 'click', 'span', function() {
                    var index = $(this).index(),
                        deg;

                    switch ( index ) {
                        case 0:
                            uploader.removeFile( file );
                            return;
                        case 1:
                            file.rotation += 90;
                            break;

                        case 2:
                            file.rotation -= 90;
                            break;
                    }

                    if ( supportTransition ) {
                        deg = 'rotate(' + file.rotation + 'deg)';
                        $wrap.css({
                            '-webkit-transform': deg,
                            '-mos-transform': deg,
                            '-o-transform': deg,
                            'transform': deg
                        });
                    } else {
                        $wrap.css( 'filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation='+ (~~((file.rotation/90)%4 + 4)%4) +')');
                        
                    }
                });

                $li.appendTo( $queue );
            }

            // 负责view的销毁
            function removeFile( file ) {
                var $li = $('#'+file.id);

                delete percentages[ file.id ];
                updateTotalProgress();
                $li.off().find('.file-panel').off().end().remove();
            }

            function updateTotalProgress() {
                var loaded = 0,
                    total = 0,
                    spans = $progress.children(),
                    percent;

                $.each( percentages, function( k, v ) {
                    total += v[ 0 ];
                    loaded += v[ 0 ] * v[ 1 ];
                } );

                percent = total ? loaded / total : 0;


                spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
                spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
                updateStatus();
            }

            function updateStatus() {
                var text = '', stats;

                if ( state === 'ready' ) {
                    text = '选中' + fileCount + '张图片，共' +
                            WebUploader.formatSize( fileSize ) + '。';
                } else if ( state === 'confirm' ) {
                    stats = uploader.getStats();
                    if ( stats.uploadFailNum ) {
                        text = '已成功上传' + stats.successNum+ '张照片至XX相册，'+
                            stats.uploadFailNum + '张照片上传失败，<a class="retry" href="#">重新上传</a>失败图片或<a class="ignore" href="#">忽略</a>'
                    }

                } else {
                    stats = uploader.getStats();
                    text = '共' + fileCount + '张（' +
                            WebUploader.formatSize( fileSize )  +
                            '），已上传' + stats.successNum + '张';

                    if ( stats.uploadFailNum ) {
                        text += '，失败' + stats.uploadFailNum + '张';
                    }
                }

                $info.html( text );
            }

            function setState( val ) {
                var file, stats;

                if ( val === state ) {
                    return;
                }

                $upload.removeClass( 'state-' + state );
                $upload.addClass( 'state-' + val );
                state = val;

                switch ( state ) {
                    case 'pedding':
                        $placeHolder.removeClass( 'element-invisible' );
                        $queue.hide();
                        $statusBar.addClass( 'element-invisible' );
                        uploader.refresh();
                        break;

                    case 'ready':
                        $placeHolder.addClass( 'element-invisible' );
                        $( '#filePicker2' ).removeClass( 'element-invisible');
                        $queue.show();
                        $statusBar.removeClass('element-invisible');
                        uploader.refresh();
                        break;

                    case 'uploading':
                        $( '#filePicker2' ).addClass( 'element-invisible' );
                        $progress.show();
                        $upload.text( '暂停上传' );
                        break;

                    case 'paused':
                        $progress.show();
                        $upload.text( '继续上传' );
                        break;

                    case 'confirm':
                        $progress.hide();
                        $( '#filePicker2' ).removeClass( 'element-invisible' );
                        $upload.text( '开始上传' );
                       
                        stats = uploader.getStats();
                        if ( stats.successNum && !stats.uploadFailNum ) {
                            setState( 'finish' );
                            return;
                        }
                        break;
                    case 'finish':
                        stats = uploader.getStats();
                        if ( stats.successNum ) {
          //                   resultSrc = $('.imgWrap img').attr('src');
                            // $('.content_second .modal-footer .js-confirm').hide();
                            // $('.modal-footer .ui-btn-primary').removeClass('no');
                        } else {
                            // 没有成功的图片，重设
                            state = 'done';
                            location.reload();
                        }
                        break;
                }

                updateStatus();
            }
            uploader.on('uploadSuccess',function (file,response) {//上传成功后接收数据
                console.log(response);
                var imgData = response.data
                var imgSize = imgData.FileInfo.img_size.split("x")
                var obj = {
                    imgSrc: '/'+imgData.FileInfo.path,
                    imgWidth: imgSize[0],
                    imgHeight: imgSize[1],
                    imgId: imgData.FileInfo.id
                }
                resultSrc.push(obj);
                console.log(resultSrc)
                $("#"+file.id).append('<input type="hidden" value=\''+JSON.stringify(response.data.FileInfo)+'\' />');
                $('.content_second .modal-footer .js-confirm').hide();
                $('.modal-footer .ui-btn-primary').removeClass('no');
            })
            uploader.onUploadProgress = function( file, percentage ) {
                var $li = $('#'+file.id),
                    $percent = $li.find('.progress span');

                $percent.css( 'width', percentage * 100 + '%' );
                percentages[ file.id ][ 1 ] = percentage;
                updateTotalProgress();
            };

            uploader.onFileQueued = function( file ) {

                    fileCount++;
                    fileSize += file.size;

                    if ( fileCount === 1 ) {
                        $placeHolder.addClass( 'element-invisible' );
                        $statusBar.show();
                    }

                    addFile( file );
                    setState( 'ready' );
                    updateTotalProgress();


                // let url = window.URL || window.webkitURL;
                // console.log(url.createObjectURL(file));
                // if(file._info.width >400 || file._info.width.height > 400){
                //     console('图片尺寸过大')
                //     return false
                // }
            };

            uploader.onFileDequeued = function( file ) {
                fileCount--;
                fileSize -= file.size;

                if ( !fileCount ) {
                    setState( 'pedding' );
                }

                removeFile( file );
                updateTotalProgress();

            };

            uploader.on( 'all', function( type ) {
                var stats;
                switch( type ) {
                    case 'uploadFinished':
                        setState( 'confirm' );
                        break;

                    case 'startUpload':
                        setState( 'uploading' );
                        break;

                    case 'stopUpload':
                        setState( 'paused' );
                        break;

                }
            });

            uploader.onError = function( code ) {
                if(code == "F_EXCEED_SIZE"){
                    tipshow('图片容量超过3M，请重新上传','warm');
                }else{
                    alert( 'Eroor: ' + code );
                }
            };

            $upload.on('click', function() {
                if ( $(this).hasClass( 'disabled' ) ) {
                    return false;
                }
                if ( state === 'ready' ) {
                    uploader.upload();
                } else if ( state === 'paused' ) {
                    uploader.upload();
                } else if ( state === 'uploading' ) {
                    uploader.stop();
                }
            });
            $info.on( 'click', '.retry', function() {
                uploader.retry();
            } );

            $info.on( 'click', '.ignore', function() {
                alert( 'todo' );
            } );

            $upload.addClass( 'state-' + state );
            updateTotalProgress();
        });

    })( jQuery );
}
//上传成功后清空列表
function closeUploader() {   
    // 移除所有缩略图并将上传文件移出上传序列
    for (var i = 0; i < uploader.getFiles().length; i++) {
        // 将图片从上传序列移除
        uploader.removeFile(uploader.getFiles()[i]);
        //uploader.removeFile(uploader.getFiles()[i], true);
        //delete uploader.getFiles()[i];
        // 将图片从缩略图容器移除
        var $li = $('#' + uploader.getFiles()[i].id);
        $li.off().remove();
    }
     
    setState('pedding');
     
    // 重置文件总个数和总大小
    fileCount = 0;
    fileSize = 0;
    // 重置uploader，目前只重置了文件队列
    uploader.reset();
    // 更新状态等，重新计算文件总个数和总大小
    updateStatus();
} 
$(function(){
    getClassify();
}); 

//获取分组信息
function getClassify(){
    $.get('/merchants/myfile/getClassify',function(data){
        $('.category-list').empty();
        classifyId = data.data[0].id;//默认分组
        var _group = '';
        for( var i = 0;i < data.data.length;i++ ){
            if (i == 0){
                _group += '<li class="js-category-item active" data-id="'+data.data[i].id+'">'+data.data[i].name+'\
                            <span>'+data.data[i].number+'</span>\
                        </li>';
            }else{
                _group += '<li class="js-category-item" data-id="'+data.data[i].id+'">'+data.data[i].name+'\
                            <span>'+data.data[i].number+'</span>\
                        </li>';
            }
        }
        if(i == data.data.length){
            $('.category-list').append(_group);
        }
        getPagingInfo(classifyId,_token);
    }); 
}


//分组点击事件
$(document).on('click','.js-category-item',function(){
    $('.js-category-item').removeClass('active');
    $(this).addClass('active');
    classifyId = $(this).data('id');
    if($(this).children('span').text() == 0){console.log(1)
        $('.imgData').hide();
        $('#layerContent_right').show();
    }else{
        $('.imgData').show();
        $('#layerContent_right').hide();
    }
    getPagingInfo(classifyId,_token);
});
// 获取分页信息
function getPagingInfo(classifyId,_token){ 
    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token},function(data){//默认第一组
        console.log(data);
        getPicture(data);
        $('.picturePage').extendPagination({
            totalCount: data.data[0].total,
            showCount: data.data[0].last_page,
            limit: data.data[0].per_page,
        });
    });
}
//点击分页事件
$('.modal .attachment-pagination').on('click','.picturePage .pagination li a', function(event) { 
    var page = $(this).text()//下标切换页码数
    if(!parseInt(page)&& $(this).parent().index() == 0){
        page =  $('.picturePage .pagination .active').text();
    }else if(!parseInt(page)&& $(this).parent().index() != 0){
        page =  parseInt($('.picturePage .pagination .active').text());
    }else if($(this).parents('li').hasClass('disabled')){
        return false;
    }
    $.post('/merchants/myfile/getUserFileByClassify',{'classifyId': classifyId,_token:_token,page:page},function(data){
        getPicture(data); 
    });

});
//内容一选择图片显示边框
$(document).on('click','.imgData .image-item',function(){
    if(select_num==1){
        resultSrc=[];
        $(this).siblings('li').children('.attachment-selected').addClass('no');
        $(this).children('.attachment-selected').removeClass('no');
        var objWidth = $(this).children('.image-box').attr('data-width')
        var objHeight = $(this).children('.image-box').attr('data-height')
        var objSrc = $(this).children('.image-box').attr('src')
        var objId = $(this).children('.image-box').attr('data-id')
        var obj = {
            imgSrc: objSrc,
            imgWidth: objWidth,
            imgHeight: objHeight,
            imgId: objId
        }
        resultSrc.push(obj);
    }else{
        if($(this).children('.attachment-selected').is(':hidden')){ 
            //隐藏状态 显示出来 
            var yselect_num = $(".attachment-selected:not('.no')").length;
            if(yselect_num<select_num){
                $(this).children('.attachment-selected').removeClass("no");
                var objWidth = $(this).children('.image-box').attr('data-width')
                var objHeight = $(this).children('.image-box').attr('data-height')
                var objSrc = $(this).children('.image-box').attr('src')
                var objId = $(this).children('.image-box').attr('data-id')
                var obj = {
                    imgSrc: objSrc,
                    imgWidth: objWidth,
                    imgHeight: objHeight,
                    imgId: objId
                }
                resultSrc.push(obj);
            }else{
                // tipshow("只能选择"+select_num+"张图片!");
            }
        }else{
            //显示状态 隐藏掉
            $(this).children('.attachment-selected').addClass("no");
            console.log(resultSrc);
            var objSrc = $(this).children('.image-box').attr('src')
            var objId = $(this).children('.image-box').attr('data-id')
            for(var j = resultSrc.length - 1; j >= 0; j--){
                if(resultSrc[j].imgId == objId){
                    console.log(resultSrc[j])
                    resultSrc.splice(j,1)
                }
            }
            console.log(resultSrc)
        }
    }
    showBtn();
}); 

function showBtn(){
    if(resultSrc.length>0){
        $('.modal-footer .js-confirm').hide();
        $('.modal-footer .ui-btn-primary').removeClass('no');    
    }else{
        $('.modal-footer .js-confirm').show();
        $('.modal-footer .ui-btn-primary').addClass('no');  
    } 
}

//点击上传图片切换
$('.js-show-upload-view, .js_addImg').on('click',function(){
    $('.content_first').hide();
    $('.content_second').show();
    $('.myModal-adv .modal-body').addClass('height_auto');
    $('.myModal-adv .module-nav').hide();
    $('.myModal-adv .cap_head').show(); 
    resultSrc =[]; //清空数组 
    $('.attachment-selected').addClass("no");
    // uploader.reset();
    myupload(select_num);
});

//选择图片
$('.js_prev').on('click',function(){
    resultSrc =[];
    uploader.reset();
    // $('#uploader').find( '.queueList' ).find('.filelist').remove()
    modalChange('.content_second');
});
//点击选择图片切换
function modalChange(obj){
    $(obj).hide();
    $('.content_first').show();
    $('.myModal-adv .modal-body').removeClass('height_auto');
    $('.myModal-adv .cap_head').hide();
    $('.myModal-adv .module-nav').show();
}

// 数据请求成功后执行方法
function getPicture(data){ 
    $('.attachment-list-region .image-list').empty();//先清空所有的元素
    var _img_item= '';
    var _imgType;
    for ( var i = 0;i < data.data[0].data.length;i++ ){
        var imgData = data.data[0].data[i]
        var imgSize = imgData.FileInfo.img_size.split("x")
        console.log(imgSize);
        _imgType = data.data[0].data[i].FileInfo.type.slice(data.data[0].data[i].FileInfo.type.lastIndexOf('/')+1)
        _img_item +='<li class="image-item">\
            <img data-id="'+imgData.FileInfo.id+'" data-width="'+imgSize[0]+'" data-height="'+imgSize[1]+'" class="image-box" src="/'+data.data[0].data[i].FileInfo.path+'" />\
            <div class="image-title">'+data.data[0].data[i].FileInfo.name+'.'+_imgType+'</div>\
            <div class="attachment-selected no"><i class="icon-ok icon-white"></i></div>';
        _img_item +='<input type="hidden" class="image-box-hid" value=\''+JSON.stringify(data.data[0].data[i].FileInfo)+'\' /></li>';

    }
    if(i == data.data[0].data.length){
        $('.attachment-list-region .image-list').append(_img_item);
    }
}

//点击确定获取图片信息
$('.ui-btn-primary').click(function(){  
    if(callback){
        parent.window[callback](getImgInfo());
    }else{
        parent.selImgCallBack(resultSrc);
    }
    
    parent.layer.closeAll();
}); 
//获取选择图片的信息
function getImgInfo(){
    var result = [];
    $(".attachment-selected").not(".no").each(function(){
        var value = $(this).siblings('input[type="hidden"]').val();
        if(value)
            result.push(JSON.parse(value)); 
    });
    if(result.length==0){
        $(".state-complete").each(function(){
            var value = $(this).find('input[type="hidden"]').val();
            if(value)
                result.push(JSON.parse(value)); 
        });
    }
    return result;
}
//关闭弹窗 
$(".close").click(function(){
    parent.layer.closeAll();
});

//在数组原型中加入indexOf方法
Array.prototype.indexOf = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] == val) return i;
    }
    return -1;
};
 //在数组原型中加入remove方法
Array.prototype.remove = function(val) {
    var index = this.indexOf(val);
    if (index > -1) {
        this.splice(index, 1);
    }
};
