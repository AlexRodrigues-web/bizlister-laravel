@props(['paginator'])

@php($p = $paginator)

@if ($p instanceof \Illuminate\Contracts\Pagination\Paginator || $p instanceof \Illuminate\Pagination\LengthAwarePaginator)
  @if ($p->hasPages())
    <nav class="mt-6 flex items-center justify-between" role="navigation">
      {{-- Previous --}}
      @if ($p->onFirstPage())
        <span class="px-3 py-2 text-sm text-slate-400 border rounded-lg">Anterior</span>
      @else
        <a href="{{ $p->previousPageUrl() }}" class="px-3 py-2 text-sm border rounded-lg hover:bg-slate-50">Anterior</a>
      @endif

      <span class="text-sm text-slate-600">Página {{ $p->currentPage() }} de {{ method_exists($p, 'lastPage') ? $p->lastPage() : '' }}</span>

      {{-- Next --}}
      @if ($p->hasMorePages())
        <a href="{{ $p->nextPageUrl() }}" class="px-3 py-2 text-sm border rounded-lg hover:bg-slate-50">Próxima</a>
      @else
        <span class="px-3 py-2 text-sm text-slate-400 border rounded-lg">Próxima</span>
      @endif
    </nav>
  @endif
@endif