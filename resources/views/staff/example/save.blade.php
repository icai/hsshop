@extends('staff.base.head')
@section('head.css')
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/3.1 addNews.css" />    
    <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/8.2 examplesave.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
    <div class="main">
        <div class="content">
            <input id="source" type="hidden" value="{{ config('app.source_url') }}staff/hsadmin" />
            <div class="content_top">
                <button type="button" class="btn btn-primary">当前位置</button>
                <span>案例管理-{{ $title }}</span>
            </div>
            <div class="main_content">
                <div class="sorts">
                    <a href="##" style="color: #333;">{{ $title }}</a>
                </div>
                <div class="addNews_list">
                    <form id="myForm" method="post">
                        <input id="edit_id" type="hidden" name="id" value="{{ $data['id'] or 0 }}">
                    <hr />
                    <!--标题照片详情-->
                    <div class="news_detail">
                    	<div class="exm-top">
                    		<div class="exm-lef">
		                        <div class="inpGroup">
		                            <label for="title" class="inpName">案例标题：</label>
		                            <input type="text" name="title" id="title" value="{{ $data['name'] or '' }}" />
		                        </div>
		                        <div class="inpGroup">
		                            <label for="subtitle" class="inpName">案例类型：</label>
		                            <select class="form-control pushsel" name="subtitle">
		                            	@foreach($caseArr as $v)
		                            	<option value="{{ $v }}" @if(isset($data['type']) && $v == $data['type']) selected='selected' @endif>{{ $v }}</option>
										@endforeach
		                            </select>
		                        </div>
		                        <div class="inpGroup">
		                            <label for="subtitle" class="inpName">关联案例：</label>
		                            <select class="form-control pushsel" name="">
		                            	<option value="">案例1</option>
		                            	<option value="">案例2</option>
		                            	<option value="">案例3</option>
		                            	<option value="">案例4</option>
		                            </select>
		                        </div>
		                        <div class="inpGroup">
	                            <label for="subtitle" class="inpName">产品介绍：</label>
	                            <textarea name="meta" rows="7" cols="50">{{ $data['intruduce'] or '' }}</textarea>
		                        </div>
		                        <div class="inpGroup">
		                        	<label for="subtitle" class="inpName">推送设置：</label>
		                        	<select class="form-control pushsel" name="">
		                        		<option value="">全站新闻</option>
		                        		<option value="">推荐内容</option>
		                        		<option value="">广告展示位</option>
		                        	</select>
		                        </div>
		                        <div class="inpGroup">
		                            <label for="sort" class="inpName">排序：</label>
		                            <input type="text" name="sort" id="sort" value="{{ $data['sort'] or 0 }}" />
		                        </div>		                        
                    		</div>	                                          			
                    		<div class="exm-lef">
		                        <div class="inpGroup">
		                            <label for="subtitle" class="inpName">作者：</label>
		                            <input type="text" name="auth" id="auth" value="{{ $data['author'] or '' }}" />
		                        </div>
		                        <div class="inpGroup">
		                            <label for="subtitle" class="inpName">行业分类：</label>
		                            @foreach($industryList as $item)
		                            <label>
		                            <input type="checkbox" name="keywords[]" id="keywords" value="{{ $item['id'] }}" {{ $item['check'] or ''}}/>{{ $item['name'] or ''}}
		                            </label>
		                            @endforeach
		                        </div>      
		                        <div class="inpGroup">
		                            <label for="subtitle" class=" ">上传logo：</label>
			                        <div class="imgDiv flex_star">
			                            <div class="relative upImg">
			                            	<div class="imgGroupa">
			                                    <div class="img_itema">
			                                    	@if(isset($data['logo']))
			                                        <img class="littleImg del-con" src="{{ imgUrl() }}{{ $data['logo'] or '' }}" width="100" height="100"/>
			                                        <img class="delImg del-img delImg1" data-id="" src="{{ config('app.source_url') }}staff/hsadmin/images/guanbi@2x.png"/>
			                                        @endif			                                        
			                                    </div>
			                            	</div>
			                                <img src="{{ config('app.source_url') }}staff/hsadmin/images/tjzp@2x.png" id="btnUpa" type="button"  width="100" height="100"/>
			                                <input id="attachment" type="hidden" name="logo" class="filepath absolutea" value="{{ $data['logo'] or '' }}" />
			                            </div>
			                        </div>
		                        </div>  
		                        <div class="inpGroup">
		                            <label for="subtitle" class="inpName">上传商品展示图：</label>
			                        <div class="imgDiv flex_star">
			                            <div class="relative upImg">
			                            	<div class="imgGroupb">
			                            		@if($imgArr)
			                            		@forelse($imgArr as $img)
			                                    <div class="img_itemb del-div">
			                                        <img style="display: inline-block;" class="delImg del-imga" data-id="" src="{{ config('app.source_url') }}staff/hsadmin/images/guanbi@2x.png"/>
			                                        <img style="display: inline-block;" class="littleImg del-cona" src="{{ imgUrl() }}{{ $img }}" width="100" height="100"/>
			                                    </div>
			                                    @empty
			                                    @endforelse
			                                    @endif
			                            	</div>
			                                <img src="{{ config('app.source_url') }}staff/hsadmin/images/tjzp@2x.png" id="btnUpb" type="button"  width="100" height="100"/>
			                                <input id="attachmenta" type="hidden" name="show_img" class="filepath absoluteb" value="{{ $data['show_img'] or '' }}" />
			                            </div>
			                        </div>
		                        </div>
								<div class="inpGroup">
									<label for="subtitle" class=" ">上传案例二维码：</label>
									<div class="imgDiv flex_star">
										<div class="relative upImg">
											<div class="imgGroupc">
												<div class="img_itemc">
													@if(isset($data['code']))
														<img class="littleImg del-conc" src="{{ imgUrl() }}{{ $data['code'] or '' }}" width="100" height="100"/>
														<img class="delImg delImgc" data-id="" src="{{ config('app.source_url') }}staff/hsadmin/images/guanbi@2x.png"/>
													@endif
												</div>
											</div>
											<img src="{{ config('app.source_url') }}staff/hsadmin/images/tjzp@2x.png" id="btnUpc" type="button"  width="100" height="100"/>
											<input id="attachmentc" type="hidden" name="code" class="filepath absolutec" value="{{ $data['code'] or '' }}" />
										</div>
									</div>
								</div>
							</div>
                    	</div>                        
                		<div class="inpGroup">
                            <label for="detail" class="inpName">产品描述：</label>
                            <div id="editor" name="content" type="text/plain" style="width:calc(100% - 60px);height:300px;margin-left: 60px;"></div>
                        </div>
                        <input id="status" type="hidden" name="status" value="1" />  
                    </div>
                        <div class="btn_group">
                            <button id="sub" type="button" class="btn btn-primary sure">确认提交</button>
                            <button id="sub1" type="button" class="btn btn-primary sure">重置表单</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
@endsection
@section('foot.js')
<script type="text/javascript">
	var attachment = [];
	var data = "{{ $data['desc'] or ''}}";
</script>
	<script src="{{ config('app.source_url') }}staff/hsadmin/js/8.5 examupimg.js" type="text/javascript" charset="utf-8"></script>
	<script src="{{ config('app.source_url') }}staff/hsadmin/js/8.2 examplesave.js" type="text/javascript" charset="utf-8"></script>   
    <script src="{{ config('app.source_url') }}static/js/ajaxupload.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/ueditor.all.js"> </script>
    <!--建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败-->
    <!--这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文-->
    <script type="text/javascript" charset="utf-8" src="{{ config('app.source_url') }}static/js/UE/UEditor/lang/zh-cn/zh-cn.js"></script>  
@endsection