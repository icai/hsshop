@if ($paginator->hasPages())
    <ul class="pagination">
        <li style="margin-left: 20px;line-height: 34px;">总共 {{ $paginator->lastPage() }} 页，当前是第 {{ $paginator->currentPage() }} 页</li>
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>上一页</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">上一页</a></li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">下一页</a></li>
        @else
            <li class="disabled"><span>下一页</span></li>
        @endif
    </ul>
@endif
