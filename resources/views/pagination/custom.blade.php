<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- Enlace a la página anterior -->
        <li class="page-item{{ $paginator->onFirstPage() ? ' disabled' : '' }}">
            @if ($paginator->onFirstPage())
                <span class="page-link">Anterior</span>
            @else
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous">
                    <span aria-hidden="true">Anterior</span>
                </a>
            @endif
        </li>

        <!-- Enlaces de las páginas -->
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled">
                    <span class="page-link">{{ $element }}</span>
                </li>
            @elseif (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="page-item{{ $page == $paginator->currentPage() ? ' active' : '' }}">
                        @if ($page == $paginator->currentPage())
                            <span class="page-link">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    </li>
                @endforeach
            @endif
        @endforeach

        <!-- Enlace a la página siguiente -->
        <li class="page-item{{ $paginator->hasMorePages() ? '' : ' disabled' }}">
            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next">
                    <span aria-hidden="true">Siguiente</span>
                </a>
            @else
                <span class="page-link">Siguiente</span>
            @endif
        </li>
    </ul>
</nav>
