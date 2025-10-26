{{-- PUBLIC_UI_V2_MARK --}}
@extends('layouts.app', ['title' => 'Categorias'])

@section('content')
<div class="container py-4">

  {{-- CabeÃƒÂ§alho + busca --}}
  <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 fw-bold mb-1">Categorias</h1>
      <p class="text-muted mb-0">Navegue por todas as categorias do BizLister.</p>
    </div>

    <form method="GET" action="{{ route('categories.index') }}" class="w-100 w-md-auto" role="search">
      <div class="input-group">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          class="form-control"
          placeholder="Buscar..."
          aria-label="Buscar categorias"
        >
        <button class="btn btn-primary" type="submit">Buscar</button>
      </div>
    </form>
  </div>

  @php
    // Aceita $categories ou $cats Ã¢â‚¬â€ e tanto Collection quanto Paginator
    $items = $categories ?? $cats ?? collect();

    // Helper para extrair campos de esquemas diferentes
    $getId = function($c) { return $c->cat_id ?? $c->id ?? null; };
    $getName = function($c) {
        return $c->category_name
            ?? $c->category
            ?? $c->cat_name
            ?? $c->name
            ?? $c->label
            ?? ($c->cat_id ?? $c->id ? 'Categoria #'.($c->cat_id ?? $c->id) : 'Categoria');
    };
    $getCount = function($c) { return $c->business_count ?? $c->count ?? null; };
  @endphp

  @if(($items instanceof \Illuminate\Support\Collection ? $items->count() : $items->total() ?? $items->count()) === 0)
    <div class="alert alert-secondary mb-0">
      Nenhuma categoria encontrada.
    </div>
  @else
    <div class="row g-3">
      @foreach($items as $c)
        @php
          $id   = $getId($c);
          $name = $getName($c);
          $slug = \Illuminate\Support\Str::slug($name ?? 'categoria');
          $url  = $id ? route('categories.show', ['id' => $id, 'slug' => $slug]) : '#';
          $qty  = $getCount($c);
        @endphp

        <div class="col-12 col-sm-6 col-md-4">
          <a href="{{ $url }}" class="text-decoration-none">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-2">
                  <h2 class="h6 fw-semibold text-dark mb-1">{{ $name }}</h2>
                  @if(!is_null($qty))
                    <span class="badge text-bg-light">{{ $qty }}</span>
                  @endif
                </div>
                <span class="text-muted small">Ver</span>
              </div>
            </div>
          </a>
        </div>
      @endforeach
    </div>

    {{-- PaginaÃƒÂ§ÃƒÂ£o (se for paginator) --}}
    @if(method_exists($items, 'links'))
      <div class="mt-3">
        {{ $items->withQueryString()->links() }}
      </div>
    @endif
  @endif
</div>
@endsection
