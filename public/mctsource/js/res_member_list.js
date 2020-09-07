$(function(){
    
    // 删除列表
    $('body').on('click','.delBtn',function(e){            
        e.stopPropagation();
        var _this = this;
        // var id=$(this).data('id');
        console.log(id);
        var id = []
        id.push($(this).data('id'))
        console.log(id);
        showDelProver($(_this),function(){
            $.ajax({
                type:"post",
                url:'/merchants/marketing/researchDelete',
                data:{
                    ids:id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res){
                    console.log(res);
                    if(res.status===1){
                        tipshow('删除成功','info');
                        // $(_this).parents('.data_content').remove();
                        setTimeout(function () {
                            location.reload();
                        },1500)
                    }else{
                        tipshow('删除失败','warn');
                    }
                },
                error:function(){
                    alert('数据访问异常')
                }
            });
        })
    });


    $(".look").on('click',function () {
        var wid = $(this).attr('data-rid')
        var mid = $(this).attr('data-mid')
        // 许立 2018年7月4日 增加参与次数参数
        var times = $(this).data('times')
        var time_string = $(this).parent('li').siblings('.blue').attr('data-time')
        $("#timesId").html(time_string)
        $.ajax({
            url:'/merchants/marketing/researchRecords/' + wid + '/'+ mid + '/' + times,
            success:function (res) {
                if(res.status == 1){
                    var arr = []
                    var list = res.data.list.records
                    var activity_title = res.data.list.activity_title
                    $('#activity_title').text(activity_title);
                    list.forEach(function (val) {
                        var obj = {}
                        if(val.type == 'time'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                if(content.start_time && content.end_time){
                                    obj['times'] = content.start_time + ' 至 ' + content.end_time
                                }else{
                                    obj['times'] = content.start_time
                                }
                            }else{
                                obj['times'] = ''
                            }
                        } else if(val.type == 'text'){
                                obj['type'] = val.type
                                obj['title'] = val.title
                                if(val.content){
                                    obj['val'] = val.content
                                }else{
                                    obj['val'] = ''
                                }
                        }else if(val.type == 'num'){//何书哲 2018年7月20日 添加预约计数单位类型
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                obj['val'] = val.content
                            }else{
                                obj['val'] = ''
                            }
                        }else if(val.type == 'vote_text'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                obj['content'] = content
                            }else{
                                obj['content'] = ''
                            }
                        }else if(val.type == 'vote_image'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                obj['content'] = content
                            }else{
                                obj['content'] = ''
                            }
                        }else if(val.type == 'image'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                obj['url'] = val.content
                            }else{
                                obj['url'] = ''
                            }
                        }else if(val.type == 'appoint_text'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                obj['option'] = content[0].title
                            }else{
                                obj['option'] = ''
                            }
                        }else if(val.type == 'appoint_image'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                obj['option'] = content[0].title
                                obj['image'] = content[0].image
                            }else{
                                obj['option'] = ''
                                obj['image'] = ''
                            }
                        }else if(val.type == 'appoint_time'){//何书哲 2018年7月20日 添加预约时间段类型
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                var html = ''
                                for (var i = 0; i < content.length; i++) {//何书哲 2018年7月22日 修改appoint_time为多选项时，title的拼接
                                    if (i < content.length - 1) {
                                        html += content[i].title + ';'
                                    } else {
                                        html += content[i].title
                                    }
                                }
                                obj['option'] = html
                            }else{
                                obj['option'] = ''
                            }
                        }else if(val.type == 'address'){
                            obj['type'] = val.type
                            obj['title'] = val.title
                            if(val.content){
                                var content = JSON.parse(val.content)
                                var region = content.region
                                var html = ''
                                for(var i = 0; i < region.length; i++){
                                    if(i < region.length - 1){
                                        html += region[i] + ','
                                    }else{
                                        html += region[i]
                                    }
                                }
                                obj['region'] = html
                            }else{
                                obj['region'] = ''
                            }
                        }
                        arr.push(obj)
                    })
                    console.log(arr);
                    listDom(arr)
                }
            }
        })
    });

    function listDom(arr){
        var html = ''
        arr.forEach(function (val) {
            if(val.type == 'time'){
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div>"+val.times+"</div>" +
                    "</li>"
            } else if(val.type == 'text'){
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div class='per_text'>"+val.val+"</div>" +
                    "</li>"
            }else if(val.type == 'num'){//何书哲 2018年7月20日 添加预约计数单位html
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div class='per_text'>"+val.val+"</div>" +
                    "</li>"
            }else if(val.type == 'vote_text'){
                html += "<li>" +
                    "<div>"+val.title+"</div>"+
                    "<div>"
                if(val.content){
                    for(var i = 0; i < val.content.length; i++){
                        html += "<div class='clearfix'>" +
                            "<span>"+val.content[i].title+"</span>" +
                            "</div>"
                    }
                    html += "</div></li>"
                }else{
                    html += val.content + "</div></li>"
                }
            }else if(val.type == 'vote_image'){
                html += "<li>" +
                            "<div>"+val.title+"</div>"+
                            "<div>"
                if(val.content){
                    for(var j = 0; j< val.content.length; j++){
                        html +=
                            "<div class='clearfix'>" +
                                "<p class='img_box'><img src='"+ (host + val.content[j].image)+"' alt=''></p>" +
                                "<span class='span_box'>"+val.content[j].title+"</span>" +
                            "</div>"
                    }
                    html += "</div></li>"
                }else{
                    html += val.content + "</div></li>"
                }
            }else if(val.type == 'image'){
                html += "<li>" +
                            "<div>"+val.title+"</div>" +
                            "<div>" +
                                "<div class='clearfix'>" +
                                    "<p class='img_box'>" +
                                        "<img src='"+(host + val.url)+"' alt=''>" +
                                    "</p>" +
                                "</div>" +
                            "</div>" +
                       "</li>"
            }else if(val.type == 'appoint_text'){
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div class='per_text'>"+val.option+"</div>" +
                    "</li>"
            }else if(val.type == 'appoint_image'){
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div>" +
                    "<div class='clearfix'>" +
                    "<p class='img_box'>" +
                    "<img src='" + (host + val.image)+"' alt=''>" +
                    "</p>" +
                    "<span class='span_box'>"+val.option+"</span>" +
                    "</div>" +
                    "</div>" +
                    "</li>"
            }else if(val.type == 'appoint_time'){//何书哲 2018年7月20日 添加预约时间段html
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div class='per_text'>"+val.option+"</div>" +
                    "</li>"
            }else if(val.type == 'address'){
                html += "<li>" +
                    "<div>"+val.title+"</div>" +
                    "<div>"+val.region+"</div>" +
                    "</li>"
            }
        })
        $(".par_ul").html(html)
        $(".particulars").removeClass('hide')
    }
    $(".par_close").on('click',function () {
        $(".particulars").addClass('hide')
    });
    $(".particulars").mouseenter(function () {
        $(".par_close").removeClass('hide')
    })
    $(".particulars").mouseleave(function () {
        $(".par_close").addClass('hide')
    })
    //搜索
    $("#search_txt").on('blur',function () {
        var val = $(this).val();
        search(val)
    })
    $(document).on('keydown',function (e) {
        if(e.keyCode == 13){
            var val = $('#search_txt').val();
            search(val)
        }
    })
    //updata by 邓钊 2018-6-29
    function search(txt) {
        if(txt){
            window.location.href = '/merchants/marketing/researchMembers/'+ activity_id +'?name=' + txt
        }else{
            window.location.href = '/merchants/marketing/researchMembers/'+ activity_id
        }
    }
    //end
    //add by 邓钊 2018-06-27
    $(".type_ul").children('li').on('click',function () {
        $(this).addClass('li_active').siblings('li').removeClass('li_active')
        var id = $(this).attr('data-id')
        if(id == 0){
            $('.main-cont').show()
            $(".text-right").show()
            $('.vote-cont').addClass('hide')
        }else{
            $('.main-cont').hide()
            $('.text-right').hide()
            $('.vote-cont').removeClass('hide')
        }
    })
    //end
});

//add by 邓钊 2018-06-27
var activity_id = $("#activity_id").val()
var type_id = $("#type_id").val()
var app = new Vue({
    el: '#app',
    data: {
        message: '',
        list: null,
        type_id:null
    },
    created:function () {
        var url = '/merchants/marketing/researchResult/' + activity_id
        var _this = this
        this.$http.get(url).then(function (res) {
            if(res.body.status == 1){
                var item = res.body.data
                _this.message = item.activity_title
                _this.type_id = type_id
                var arr = []
                for(var key in item.vote_result){
                    arr.push(item.vote_result[key])
                }
                for(let i = 0; i < arr.length; i++){
                    var options = arr[i].options
                    for(let i = 0; i < options.length; i++ ){
                        options[i]['widths'] = options[i]['vote_ratio'] * 200
                        options[i]['bar'] = parseInt(options[i]['vote_ratio'] * 100)  + '%'
                    }
                }
                _this.list = arr
            }
        })
    }
})
//end

