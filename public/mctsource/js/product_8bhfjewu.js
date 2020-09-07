$(function () {

    $('body').on('click', '.product_detail_url', function () {
        var _this = $(this);
        var _url = $(this).data('url');             // 要复制的连接
        var html = '<div class="input-group">';
        html += '<input type="text" class="link_copy form-control" value="' + _url + '" disabled >';
        html += '<a class="copy_btn input-group-addon">复制</a>';
        html += '</div>';
        showDelProver(_this, function () {
        }, html, 'false');
    });
    // 复制链接
    $('body').on('click', '.copy_btn', function () {
        var obj = $(this).siblings('.link_copy');
        copyToClipboard(obj);
        tipshow('复制成功', 'info');
        $(this).parents('.del_popover').remove();
    });

    // 选择框全选
    $('#all_material').change(function () {
        if ($(this).is(':checked')) {
            $(this).prop("checked", true);
            $('.shop').prop("checked", true);
        } else {
            $(this).prop("checked", false);
            $('.shop').prop("checked", false);
        }
    })
    // 删除单个商品
    $('.out_delete').click(function (e) {
        e.stopPropagation();
        var id = $(this).data('id');
        var _token = $('input[name="_token"]').val();
        showDelProver($(this), function () {
            $.post('/merchants/product/del', {id: id, _token: _token}, function (data) {
                if (data.status == 1) {
                    tipshow(data.info);
                    window.location.reload();
                } else {
                    tipshow(data.info, 'warn');
                }
                //居中弹窗
                $('.layui-layer').css('top', window.screen.availHeight / 2 - $('.layui-layer').height() / 2)
            }, 'json')
            $('.del_popover').hide();
        })
    })
    //删除
    $('.delete').click(function () {
        if (!$('.shop').is(':checked')) {
            tipshow('请先选择商品！', 'warn')
            return;
        }
        showprover($('#delete_prover'));
    })
    $('.delete_sure').click(function (e) {
        $.post('/merchants/product/delbatch', $('form[name="shop_form"]').serialize(), function (data) {
            if (data.status == 1) {
                $('input[type="checkbox"]').prop('checked', false);
                tipshow(data.info);
                window.location.reload();
            } else {
                tipshow(data.info, 'warn');
            }
            //居中弹窗
            $('.layui-layer').css('top', window.screen.availHeight / 2 - $('.layui-layer').height() / 2)
        }, 'json')
        hideprover($('#delete_prover'));
        e.stopPropagation();
    })
    $('.delete_cancel').click(function (e) {
        hideprover($('#delete_prover'));
        e.stopPropagation();
    })

    //批量上架
    $('.on_sell').click(function () {
        if (!$('.shop').is(':checked')) {
            tipshow('请先选择商品！', 'warn')
            return;
        }
        showprover($('#on_sell_prover'));
    })
    $('.on_sell_sure').click(function (e) {
        $.post('/merchants/product/onoffsale', $('form[name="shop_form"]').serialize(), function (data) {
            if (data.status == 1) {
                $('input[type="checkbox"]').prop('checked', false);
                tipshow(data.info);
                window.location.reload();
            } else {
                tipshow(data.info, 'warn');
            }
            //居中弹窗
            $('.layui-layer').css('top', window.screen.availHeight / 2 - $('.layui-layer').height() / 2)
        }, 'json')
        hideprover($('#on_sell_prover'));
        e.stopPropagation();
    })
    $('.on_sell_cancel').click(function (e) {
        hideprover($('#on_sell_prover'));
        e.stopPropagation();
    })

    //链接弹出框
    $('.ads').click(function () {
        alert('a')
    })
    $('.action').click(function () {
        showModel($('#myModal'), $('#modal-dialog'));
    })

    $('.close').click(function () {
        hideModel($('#myModal'));
    })
    var input = document.getElementById("upload_flie");
    //文件域选择文件时, 执行readFile函数
    input.addEventListener('change', readFile, false);
    //提交表单
    $('.submit').click(function () {

    })
    $(".btn.submit").each(function (index, ele) {
        $(this).click(function () {
            var fileVal = $(".fileupload").eq(index).val();
            if (fileVal) {
                return true;
            } else {
                tipshow('请选择正确的文件', 'warning');
                return false;
            }
        })
    })
})

function submitForm() {
    var files = document.getElementById('upload_flie');
    if (files.files[0] == undefined) {
        $('.js-notice').html('请选择一个CSV文件,或者包含csv文件的zip压缩文件!');
        $('.js-notice').show();
        $(this).val("")
        return false;
    }
    if (files.files[0].name.substring(files.files[0].name.indexOf('.')) != '.csv' && files.files[0].name.substring(files.files[0].name.indexOf('.')) != '.rar') {
        $('.js-notice').html('请选择一个CSV文件,或者包含csv文件的rar压缩文件!');
        $('.js-notice').show();
        $(this).val("")
        return false;
    }
    if (files.files[0].size > 1024 * 1024) {
        $('.js-notice').html('请选择一个小于1M的文件！');
        $('.js-notice').show();
        $(this).val("")
        return false;
    }
    return true;
}

function center(obj) {
    var window_height = $(document).height();
    var height = obj.height();
    obj.css('margin-top', window.screen.availHeight / 2 - height / 2);
}

function showModel(obj, obj2) {
    obj.show();
    $('.modal-backdrop').show();
    // center(obj2);
}

function hideModel(obj) {
    obj.hide();
    $('.modal-backdrop').hide();
}

function bytesToSize(bytes) {
    if (bytes === 0) return '0 B';
    var k = 1000, // or 1024
        sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
        i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}

function readFile() {
    var file = this.files[0];
    // 许立 2018年11月28日 文件名检查
    if (file == undefined || (file.name.substr(-4) != '.csv' && file.name.substr(-4) != '.rar')) {
        $('#myModal .js-notice').html('请选择一个CSV文件,或者包含csv文件的rar压缩文件!');
        $('#myModal .js-notice').show();
        $(this).val("")
        return;
    }
    if (file.size > 1024 * 1024) {
        $('#myModal .js-notice').html('请选择一个小于1M的文件！');
        $('#myModal .js-notice').show();
        $(this).val("")
        return;
    }
    $('#myModal .file_name').html(file.name);
    $('#myModal .file_size').html(bytesToSize(file.size));
    $('#myModal .name_container').show();
    $('#myModal .size_container').show();
    $('#myModal .js-notice').hide();

}

// prover
function hideprover(obj) {
    obj.hide();
}

function showprover(obj) {
    obj.show();
}


/*
* 导入淘宝商品
*/
$('.import_taobao').click(function () {
    showModel($('#myModal-taobao'), $('#modal-dialog-taobao'));
});

$('#close_taobao').click(function () {
    hideModel($('#myModal-taobao'));
});

var input = document.getElementById("upload_taobao");
//文件域选择文件时, 执行readFile函数
input.addEventListener('change', readTaobaoFile, false);

function readTaobaoFile() {
    var file = this.files[0];
    // 许立 2018年11月28日 压缩包文件名检查
    if (file == undefined || file.name.substr(-4) != '.zip') {
        $('#myModal-taobao .js-notice').html('请选择一个包含csv文件和tbi图片的ZIP文件');
        $('#myModal-taobao .js-notice').show();
        $(this).val("")
        return;
    }
    if (file.size > 50 * 1024 * 1024) {
        $('#myModal-taobao .js-notice').html('请选择一个小于50M的文件！');
        $('#myModal-taobao .js-notice').show();
        $(this).val("")
        return;
    }
    $('#myModal-taobao .file_name').html(file.name);
    $('#myModal-taobao .file_size').html(bytesToSize(file.size));
    $('#myModal-taobao .name_container').show();
    $('#myModal-taobao .size_container').show();
    $('#myModal-taobao .js-notice').hide();
}


/*
 * 导入阿里巴巴商品
 */
$('.import_ali').click(function () {
    showModel($('#myModal-ali'), $('#modal-dialog-ali'));
});

$('#close_ali').click(function () {
    hideModel($('#myModal-ali'));
});

var inputAli = document.getElementById("upload_ali");
//文件域选择文件时, 执行readFile函数
inputAli.addEventListener('change', readAliFile, false);

function readAliFile() {
    var file = this.files[0];
    // 许立 2018年11月28日 压缩包文件名检查
    if (file == undefined || file.name.substr(-4) != '.zip') {
        $('#myModal-ali .js-notice').html('请选择一个包含csv文件和阿里巴巴文件的ZIP文件');
        $('#myModal-ali .js-notice').show();
        $(this).val("")
        return;
    }
    if (file.size > 50 * 1024 * 1024) {
        $('#myModal-ali .js-notice').html('请选择一个小于50M的文件！');
        $('#myModal-ali .js-notice').show();
        $(this).val("")
        return;
    }
    $('#myModal-ali .file_name').html(file.name);
    $('#myModal-ali .file_size').html(bytesToSize(file.size));
    $('#myModal-ali .name_container').show();
    $('#myModal-ali .size_container').show();
    $('#myModal-ali .js-notice').hide();
}

/*
 * 导入阿凡提商品
 */
$('.import_afanti').click(function () {
    showModel($('#myModal-afanti'), $('#modal-dialog-afanti'));
});

$('#close_afanti').click(function () {
    hideModel($('#myModal-afanti'));
});

var inputAfanti = document.getElementById("upload_afanti");
//文件域选择文件时, 执行readFile函数
inputAfanti.addEventListener('change', readAfantiFile, false);

function readAfantiFile() {
    var file = this.files[0];
    // 许立 2018年11月28日 文件名检查
    if (file == undefined || file.name.substr(-4) != '.xls') {
        $('#myModal-afanti .js-notice').html('请选择一个XLS文件');
        $('#myModal-afanti .js-notice').show();
        $(this).val("")
        return;
    }
    /*if(file.size>2*1024*1024){
        $('#myModal-afanti .js-notice').html('请选择一个小于2M的文件！');
        $('#myModal-afanti .js-notice').show();
        $(this).val("")
        return;
    }*/
    $('#myModal-afanti .file_name').html(file.name);
    $('#myModal-afanti .file_size').html(bytesToSize(file.size));
    $('#myModal-afanti .name_container').show();
    $('#myModal-afanti .size_container').show();
    $('#myModal-afanti .js-notice').hide();
}

/*
 * 导入小程序商品
 */
$('.import_xcx').click(function () {
    showModel($('#myModal-xcx'), $('#modal-dialog-xcx'));
});

$('#close_xcx').click(function () {
    hideModel($('#myModal-xcx'));
});

var inputxcx = document.getElementById("upload_xcx");
//文件域选择文件时, 执行readFile函数
inputxcx.addEventListener('change', readxcxFile, false);

function readxcxFile() {
    var file = this.files[0];
    // 许立 2018年11月28日 文件名检查
    if (file == undefined || file.name.substr(-4) != '.xls') {
        $('#myModal-xcx .js-notice').html('请选择一个XLS文件');
        $('#myModal-xcx .js-notice').show();
        $(this).val("")
        return;
    }

    $('#myModal-xcx .file_name').html(file.name);
    $('#myModal-xcx .file_size').html(bytesToSize(file.size));
    $('#myModal-xcx .name_container').show();
    $('#myModal-xcx .size_container').show();
    $('#myModal-xcx .js-notice').hide();
}

/*
 * 导入会搜云新零售系统商品
 * @author 吴晓平 2019年10月08日
 */
$('.import_aiCard').click(function () {
    showModel($('#myModal-aiCard'), $('#modal-dialog-aiCard'));
});

$('#close_aiCard').click(function () {
    hideModel($('#myModal-aiCard'));
});

var inputAiCard = document.getElementById("upload_aiCard");
// 文件域选择文件时, 执行readFile函数
inputAiCard.addEventListener('change', readAiCardFile, false);

function readAiCardFile() {
    var file = this.files[0];
    // 吴晓平 2019年10月08日 文件名检查
    if (file == undefined || file.name.substr(-4) != '.xls') {
        $('#myModal-aiCard .js-notice').html('请选择一个XLS文件');
        $('#myModal-aiCard .js-notice').show();
        $(this).val("")
        return;
    }

    $('#myModal-aiCard .file_name').html(file.name);
    $('#myModal-aiCard .file_size').html(bytesToSize(file.size));
    $('#myModal-aiCard .name_container').show();
    $('#myModal-aiCard .size_container').show();
    $('#myModal-aiCard .js-notice').hide();
}
