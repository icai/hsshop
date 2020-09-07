@extends('staff.base.head')
@section('head.css')
  <link rel="stylesheet" href="{{ config('app.source_url') }}staff/hsadmin/css/statistic.css" />
@endsection
@section('slidebar')
    @include('staff.base.slidebar');
@endsection
@section('content')
<div class="main" data-date_start="{{ $start }}" data-date_end="{{ $end }}">
  <div class="content">
    <div class="content_top">
      <button type="button" class="btn btn-primary">当前位置</button>
      <span>商家活跃度-微商城</span>
    </div>
    <div class="main_content">
      <div class="main_nav">
        <span class="line"></span>
        数据统计
      </div>
      <!-- 数据统计 -->
      <div class="static-num">
        <div class="static-item">
          <div class="static-item-title">总商家数</div>
          <div class="static-item-num">{{ $incomeData['shopCount'] }}</div>
        </div>
        <div class="static-item">
          <div class="static-item-title">总销售额</div>
          <div class="static-item-num">{{ $incomeData['orderData']['price_total'] }}</div>
        </div>
        <div class="static-item">
          <div class="static-item-title">总订单数</div>
          <div class="static-item-num">{{ $incomeData['orderData']['order_count'] }}</div>
        </div>
        <div class="static-item">
          <div class="static-item-title">总用户数</div>
          <div class="static-item-num">{{ $incomeData['userCount'] }}</div>
        </div>
      </div>
      <!-- 数据统计 -->
      <div class="search-item">
        <span class="search-item-label">筛选日期</span>
        <input type="text" placeholder="开始时间" name="starttime" value="{{$start}}" id="startDate">
        <span class="search-item-label">至</span>
        <input type="text" placeholder="结束时间" name="endtime" value="{{$end}}" id="endDate">
        <span class="search-item-btn active" data-type="0" data-days="1">昨日</span>
        <span class="search-item-btn" data-type="0" data-days="30">近30天</span>
        <span class="search-item-btn" data-type="0" data-days="90">近三个月</span>
        <div class="search-action">
          <button type="button" class="btn btn-primary search-static">查询</button>
        </div>
      </div>
      <!-- 图表区域 -->
      <div class="echats">
        <div class="echats-item">
          <div class="echats-item-title">
            新增商家数
            <span data-toggle="tooltip" data-placement="right" title="新注册的商家店铺数" class="el-tooltip info">?</span>
          </div>
          <p class="echats-item-num add-shop-num">{{ $activeData['newShopCount'] }}</p>
          <div class="img">
            <img src="{{ config('app.source_url') }}/staff/static/images/z_home_1.7c92f39.png" alt="">
          </div>
        </div>

        <div class="echats-item">
          <div class="echats-item-title">
            活跃商家数
            <span data-toggle="tooltip" data-placement="right" title="登陆商家后台的商家数" class="el-tooltip info">?</span>
          </div>
          <p class="echats-item-num active-shop-num">{{ $activeData['activeShopCount'] }}</p>
          <div class="img">
            <img src="{{ config('app.source_url') }}/staff/static/images/z_home_2.e8ece12.png" alt="">
          </div>
        </div>

        <div class="echats-item">
          <div class="echats-item-title">
            新增订单数
            <span data-toggle="tooltip" data-placement="right" title="支付完成的订单" class="el-tooltip info">?</span>
          </div>
          <p class="echats-item-num add-order-num">{{ $activeData['orderPayedCount'] }}</p>
          <div class="img">
            <img src="{{ config('app.source_url') }}/staff/static/images/z_home_3.0cea4f8.png" alt="">
          </div>
        </div>
        <div class="echats-item">
          <div class="echats-item-title">
            活跃客户数
            <span data-toggle="tooltip" data-placement="right" title="访问商家的用户，算为活跃客户数" class="el-tooltip info">?</span>
          </div>
          <p class="echats-item-num active-user-num">{{ $activeData['viewTotalCount'] }}</p>
          <div class="img">
            <img src="{{ config('app.source_url') }}/staff/static/images/z_home_4.bf45df0.png" alt="">
          </div>
        </div>
      </div>
      <!-- 图表区域 -->

      <!-- 商家排行 -->
      <div class="store-rank">
        <div class="main_nav">
          <span class="line"></span>
          商家排行
        </div>
        <!-- 商家排行搜索 -->
        <div class="search-item">
          <span class="search-item-label">店铺名称</span>
          <input type="text" placeholder="请输入标题" name="shopName" id="shopName">
          <span class="search-item-label search-date-label">筛选日期</span>
          <input type="text" placeholder="开始时间" name="starttime" value="{{$start}}" id="startDate1">
          <span class="search-item-label">至</span>
          <input type="text" placeholder="结束时间" name="endtime" value="{{$end}}" id="endDate1">
          <span class="search-item-btn active" data-type="1" data-days="1">昨日</span>
          <span class="search-item-btn" data-type="1" data-days="30">近30天</span>
          <span class="search-item-btn" data-type="1" data-days="90">近三个月</span>
          <div class="search-action">
            <button type="button" class="btn btn-primary search-shop">查询</button>
          </div>
        </div>
        <div class="rank-list">
          <!-- 左侧表格 -->
          <div class="rank-list-item">
            <table class="table table-bordered">
              <colgroup>
                <col name="el-table_1_column_71" width="100">
                <col name="el-table_1_column_72" width="157">
                <col name="el-table_1_column_73" width="155">
                <col name="el-table_1_column_73" width="155">
              </colgroup>
              <thead>
                <tr>
                  <th>排名</th>
                  <th>店铺名称</th>
                  <th class="descending">
                    销售额
                    <span class="caret-wrapper">
                      <i class="sort-caret ascending" data-order="income_asc"></i>
                      <i class="sort-caret descending" data-order="income_desc"></i>
                    </span>
                  </th>
                  <th>
                    订单数
                    <span class="caret-wrapper">
                      <i class="sort-caret ascending" data-order="nums_asc"></i>
                      <i class="sort-caret descending" data-order="nums_desc"></i>
                    </span>
                  </th>
                </tr>
              </thead>
            </table>
            <div class="scroll" id="scroll">
              <table class="table table-scroll table-bordered">
                <colgroup>
                  <col name="el-table_1_column_71" width="100">
                  <col name="el-table_1_column_72" width="157">
                  <col name="el-table_1_column_73" width="155">
                  <col name="el-table_1_column_74" width="155">
                </colgroup>
                <tbody>
                  @if(!$rankData->isEmpty())
                  @foreach($rankData as $item)
                  <tr>
                    <!-- <th scope="row">1</th> -->
                    <td>
                      <span class="table-index @if($item->rank == 1) first-tab @elseif($item->rank == 2) second-tab @elseif($item->rank == 3) third-tab @endif">{{ $item->rank }}</span>
                    </td>
                    <td>
                      <p class="store-title" data-logo="{{ $item->weixin_logo_url }}" data-income="{{ $item->income }}" data-nums="{{ $item->nums }}">{{ $item->shop_name }}</p>
                    </td>
                    <td>{{ $item->income }}</td>
                    <td>{{ $item->nums }}</td>
                  </tr>
                  @endforeach
                  @endif
                  @if($rankData->isEmpty())
                  <tr class="no-data">
                    <td colspan="4">暂无数据!</td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
          </div>
          <!-- 右侧统计数据 -->
          <div class="rank-list-item">
            <div class="store-data">
              <div class="store-data-header">
                <img src="{{ $rankData[0]->weixin_logo_url ?? '' }}" alt="">
                <span>{{ $rankData[0]->shop_name ?? '' }}</span>
              </div>
              <div class="store-data-content">
                <div class="static-num">
                  <div class="static-item">
                    <div class="static-item-title">销售额</div>
                    <div class="static-item-num income">{{ $rankData[0]->income ?? 0 }}</div>
                  </div>
                  <div class="static-item">
                    <div class="static-item-title">订单数</div>
                    <div class="static-item-num nums">{{ $rankData[0]->nums ?? 0 }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('foot.js')
  <script src="{{ config('app.source_url') }}static/js/layer/laydate.js"></script>
  <!-- 图表插件 -->
  <script src="{{ config('app.source_url') }}static/js/echarts/echarts-all.js"></script>
  <script src="{{ config('app.source_url') }}staff/hsadmin/js/statistic.js" type="text/javascript" charset="utf-8"></script>
@endsection
