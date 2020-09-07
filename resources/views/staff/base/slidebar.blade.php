<div class="left">
    <!-- 一级导航 开始 -->
    <div class="first_items">
        <!-- 一级导航列表 开始 -->`
        <ul class="first_nav">
            @forelse($__menu__[$title]['grandson']??[] as $key=>$item)
                <li @if($item['url'] == $_SERVER['REQUEST_URI']) class="hover"@endif>
                    <a href="{{$item['url']}}">{{$item['name']}}</a>
                </li>
                @endforeach
        </ul>
        <!-- 一级导航级导航列表 结束 -->
    </div>
    <!-- 一级导航 结束 -->
</div>