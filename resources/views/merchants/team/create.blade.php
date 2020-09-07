<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $title }}</title>
    <link rel="icon" type="text/css" href="{{ config('app.source_url') }}home/image/icon_logo.png"/>
    <!-- 核心Bootstrap.css文件（每个页面引入） -->
    <link rel="stylesheet" href="{{ config('app.source_url') }}static/css/bootstrap.min.css">
    <!-- 核心base.css文件（每个页面引入） -->
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/static/css/base.css">
    <link rel="stylesheet" type="text/css" href="{{ config('app.source_url') }}mctsource/css/team_dlrqic96.css">
</head>
<body>
    <div class="contenter">
        <div class="wrapper-app">
            <div id="header">
                <div class="header-title-wrap clearfix">
                    <div class="account">
                        <span style="color: #000">{{ session('userInfo')['mphone'] }}</span>-
                        <span class="js-select-store" style="display: none;">
                            <a href="javascript:void(0);">选择店铺</a>-
                        </span>
                        <a href="">帮助</a>-
                        <a href="/auth/loginout">退出</a>
                    </div>
                    <a href="/">
                        <div class="header-logo">
                        	<img src="{{ config('app.source_url') }}home/image/gupiaodaima.png" width="105" height="40" />
                        	</div>
                    </a>
                </div>
                <div class="addition">
                    <ul class="progress-nav progress-nav-3 clearfix">
                        <li class="progress-nav-item active current-active">1.{{ $title }}</li>
                        {{--<li class="progress-nav-item">2.选择推荐模版</li>--}}
                        <li class="progress-nav-item">2.完成！</li>
                    </ul>
                </div>
            </div>
            <div id="content" class="team-select">
                <div>
                    <form class="form-horizontal" id="shopForm" method="post" action="{{ URL('/merchants/team/create') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ $detail['id'] or '' }}" />
                        <input type="hidden" name="uid" value="{{ session('userInfo')['id'] }}" />
                        <fieldset>
                            <div class="control-group">
                                <label class="control-label">店铺名称：</label>
                                <div class="controls">
                                    <input type="text" name="shop_name" value="{{ $detail['shop_name'] or '' }}" maxlength="30" class="form-control">
                                </div>
                            </div>
                            {{--<div class="control-group">
                                <label class="control-label">主营商品：</label>
                                <div class="controls">
                                    <div class="js-business">
                                        <div>
                                            <div class="widget-selectbox">
                                                <span href="javascript:;" class="widget-selectbox-handle classfiy_1">
                                                    <span class="c-gray">{{ $detail['business_name'] or '选择类目' }}</span>
                                                </span>
                                                <div class="widget-selectbox-content top_radio">
                                                    <ul class="clearfix">
                                                        @foreach ($businessList as $bv)
                                                        <li data-id="{{ $bv['id'] }}">
                                                            <label class="radio">
                                                                @if (isset($detail['business_id']) && $detail['business_id'] == $bv['id'])
                                                                <input type="radio" name="business" value="{{ $bv['id'] }}" checked />
                                                                @else
                                                                <input type="radio" name="business" value="{{ $bv['id'] }}" />
                                                                @endif
                                                                <span>{{ $bv['title'] }}</span>
                                                            </label>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                    <p>店铺主营类目及类目细项，
                                                        <a class="new-window" href="javascript:void(0);" target="_blank">请点此查看详情</a>
                                                    </p>
                                                </div>
                                            </div>
                                            <!-- 二级主营商品 -->
                                            <div class="widget-selectbox choose_classfiy_2">
                                                <span href="javascript:;" class="widget-selectbox-handle classfiy_2">
                                                    <span class="c-gray">请选择类目</span>
                                                </span>
                                                <div class="widget-selectbox-content sub_radio">
                                                    <ul class="clearfix">
                                                        <li>
                                                            <label class="radio">
                                                                <input type="radio" name="shop_category_2">
                                                                <span>食品</span>
                                                            </label>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="classfiy" value="" id="classfiy_input">
                                </div>
                            </div>--}}
                            <div class="control-group">
                                <label class="control-label">联系地址：</label>
                                <div class="controls">
                                    <div class="space-fix" id="distpicker">
                                        <select id="province" name="province_id" class="form-control">
                                            <option value="">选择省份</option>
                                            @foreach ($provinceList as $v)
                                                @if ( isset($detail['province_id']) && $detail['province_id'] == $v['id'] )
                                                <option value="{{ $v['id'] }}" selected>{{ $v['title'] }}</option>
                                                @else
                                                <option value="{{ $v['id'] }}">{{ $v['title'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <select id="city" name="city_id" class="form-control">
                                            <option value="">选择城市</option>
                                            @if ( isset($detail['province_id']) && !empty($detail['province_id']) )
                                                @foreach ( $regionList[$detail['province_id']] as $rcv )
                                                    @if ( isset($detail['city_id']) && $detail['city_id'] == $rcv['id'] )
                                                    <option value="{{ $rcv['id'] }}" selected>{{ $rcv['title'] }}</option>
                                                    @else
                                                    <option value="{{ $rcv['id'] }}">{{ $rcv['title'] }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <select id="district" name="area_id" class="form-control">
                                            <option value="">选择地区</option>
                                            @if ( isset($detail['city_id']) && !empty($detail['city_id']) )
                                                @foreach ( $regionList[$detail['city_id']] as $rcv )
                                                    @if ( isset($detail['area_id']) && $detail['area_id'] == $rcv['id'] )
                                                    <option value="{{ $rcv['id'] }}" selected>{{ $rcv['title'] }}</option>
                                                    @else
                                                    <option value="{{ $rcv['id'] }}">{{ $rcv['title'] }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label"></label>
                                <div class="controls">
                                    <input class="input-xxlarge form-control" type="text" placeholder="请填写具体地址" name="address" maxlength="50" value="{{ isset($detail['address']) ? $detail['address'] : '' }}">
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">公司名称：</label>
                                <div class="controls">
                                    <input type="text" name="company_name" value="{{ $detail['company_name'] or '' }}" placeholder="请输入营业执照上的公司全名" maxlength="30" class="form-control">
                                </div>
                            </div>
                            <!--<div class="control-group">
                                <div class="controls">
                                    <label class="checkbox readme" style="width: auto;">
                                        @if ( isset($detail['id']) && !empty($detail['id']) )
                                        <input type="checkbox" class="js-readme" name="agreement" id="agreement" value="1" checked />
                                        @else
                                        <input type="checkbox" class="js-readme" name="agreement" id="agreement" value="1" />
                                        @endif
                                        我已阅读并同意
                                        <a href="#readme" target="_blank">微商城代理销售服务和结算协议</a>和
                                        <a href="#secured_transaction_readme" target="_blank">担保交易服务协议</a>
                                    </label>
                                </div>
                            </div>-->
                            <div class="controls">
                                @if ( isset($detail['id']) && !empty($detail['id']) )
                                <input type="submit" name="submit" value="保存" class="btn btn-large btn-primary submit-btn" type="button" data-loading-text="正在提交..." >
                                @else
                                <input type="submit" name="submit" value="创建店铺" class="btn btn-large btn-primary submit-btn" type="button" data-loading-text="正在提交..." >
                                @endif
                                <input type="hidden" name="business_id" value="">
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @if(config('app.env') == 'prod')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum.js"></script>
    @endif
    @if(config('app.env') == 'dev')
    <script type="text/javascript" src="{{ config('app.source_url') }}static/js/tingyun-rum-dev.js"></script>
    @endif
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="{{ config('app.source_url') }}static/js/jquery-1.11.2.min.js"></script>
    <!-- 表单验证插件 -->
    <script src="{{ config('app.source_url') }}static/js/jquery.validate.min.js"></script>
    <script src="{{ config('app.source_url') }}static/js/messages_zh.js"></script>
    <!-- 当前页面js -->
    <script type="text/javascript">
        var regions_datas = {!! $regions_datas !!};
    </script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/static/js/base.js"></script>
    <script type="text/javascript" src="{{ config('app.source_url') }}mctsource/js/team_dlrqic96.js"></script>
</body>
</html>