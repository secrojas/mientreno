@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" style="display:flex;justify-content:space-between;align-items:center;">
        <div style="flex:1 1 0%;display:flex;justify-content:flex-start;">
            @if ($paginator->onFirstPage())
                <span style="position:relative;display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid var(--border-subtle);background:rgba(15,23,42,.5);border-radius:0.5rem;font-size:0.875rem;font-weight:500;color:var(--text-muted);cursor:not-allowed;">
                    ‹ Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="position:relative;display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid var(--border-subtle);background:rgba(30,41,59,.7);border-radius:0.5rem;font-size:0.875rem;font-weight:500;color:var(--text-main);text-decoration:none;transition:all 0.2s;">
                    ‹ Anterior
                </a>
            @endif
        </div>

        <div style="display:flex;gap:0.25rem;">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span style="display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid transparent;font-size:0.875rem;color:var(--text-muted);">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" style="display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid var(--accent-primary);background:rgba(255,59,92,0.1);border-radius:0.5rem;font-size:0.875rem;font-weight:600;color:var(--accent-primary);">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" style="display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid var(--border-subtle);background:rgba(30,41,59,.7);border-radius:0.5rem;font-size:0.875rem;color:var(--text-main);text-decoration:none;transition:all 0.2s;">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </div>

        <div style="flex:1 1 0%;display:flex;justify-content:flex-end;">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="position:relative;display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid var(--border-subtle);background:rgba(30,41,59,.7);border-radius:0.5rem;font-size:0.875rem;font-weight:500;color:var(--text-main);text-decoration:none;transition:all 0.2s;">
                    Siguiente ›
                </a>
            @else
                <span style="position:relative;display:inline-flex;align-items:center;padding:0.5rem 0.75rem;border:1px solid var(--border-subtle);background:rgba(15,23,42,.5);border-radius:0.5rem;font-size:0.875rem;font-weight:500;color:var(--text-muted);cursor:not-allowed;">
                    Siguiente ›
                </span>
            @endif
        </div>
    </nav>

    <div style="margin-top:0.75rem;text-align:center;">
        <p style="font-size:0.875rem;color:var(--text-muted);">
            Mostrando
            <span style="font-weight:600;color:var(--text-main);">{{ $paginator->firstItem() }}</span>
            a
            <span style="font-weight:600;color:var(--text-main);">{{ $paginator->lastItem() }}</span>
            de
            <span style="font-weight:600;color:var(--text-main);">{{ $paginator->total() }}</span>
            resultados
        </p>
    </div>
@endif
