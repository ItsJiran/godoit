@if ($paginator->hasPages())
    <div class="pagination">
        {{-- Tombol sebelumnya --}}
        @if ($paginator->onFirstPage())
            <span class="disabled">&laquo; Prev</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">&laquo; Prev</a>
        @endif
        {{-- Halaman --}}
        @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
            @if ($page == $paginator->currentPage())
                <span class="active">{{ $page }}</span>
            @else
                <a href="{{ $url }}{{ request('search') ? '&search=' . request('search') : '' }}">{{ $page }}</a>
            @endif
        @endforeach
        {{-- Tombol berikutnya --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}{{ request('search') ? '&search=' . request('search') : '' }}">Next &raquo;</a>
        @else
            <span class="disabled">Next &raquo;</span>
        @endif
    </div>
@endif
