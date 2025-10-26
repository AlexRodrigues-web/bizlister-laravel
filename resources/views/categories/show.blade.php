{{-- resources/views/categories/show.blade.php --}}
{{-- PUBLIC_UI_V2_MARK --}}
@extends('layouts.app')

@section('title', (optional($category)->category
    ?? optional($category)->name
    ?? optional($category)->label
    ?? 'Categoria').' &mdash; '.config('app.name'))

@section('content')
<div class="container py-4">

  {{-- micro-up de UI sem quebrar nada --}}
  <style>
    .card.hoverable{transition:transform .15s ease, box-shadow .15s ease}
    .card.hoverable:hover{transform:translateY(-2px); box-shadow:0 .5rem 1rem rgba(0,0,0,.08)}
    .badge-truncate{max-width:10rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis}
    .line-2{display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden}
    .chip{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .6rem;border:1px solid rgba(0,0,0,.08);border-radius:999px;background:#fff}
    .chip:hover{text-decoration:none;box-shadow:0 .25rem .5rem rgba(0,0,0,.06)}
  </style>

  @php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    // === Nomes/coleções (compat com legado) ===
    $catName = $category->name
      ?? $category->cat_name
      ?? $category->category_name
      ?? $category->label
      ?? $category->category
      ?? 'Categoria';

    $catId = $category->id
      ?? $category->cat_id
      ?? $category->cid
      ?? null;

    // lista principal (negócios)
    $list = $businesses ?? $items ?? collect();
    if (is_array($list)) $list = collect($list);
    $isPaginator = $list instanceof \Illuminate\Contracts\Pagination\Paginator
                || $list instanceof \Illuminate\Pagination\LengthAwarePaginator;
    $totalCount = method_exists($list, 'total') ? (int)$list->total() : (int)$list->count();

    // filtros simples (se o controller suportar)
    $q   = trim((string) request('q', ''));
    $ord = trim((string) request('ord', ''));

    // subcategorias (pode vir via with('subcategories') ou variável $subcategories)
    $subcats = isset($subcategories) ? $subcategories : ($category->subcategories ?? collect());
    if (is_array($subcats)) $subcats = collect($subcats);
  @endphp

  {{-- Breadcrumb + título --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
      <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorias</a></li>
      <li class="breadcrumb-item active" aria-current="page">{{ $catName }}</li>
    </ol>
  </nav>

  <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-2">
    <div>
      <h1 class="h3 mb-1">{{ $catName }}</h1>
      <p class="text-muted small mb-0">
        @if($q !== '')
          Exibindo <strong>{{ $totalCount }}</strong> resultado(s) para &ldquo;{{ $q }}&rdquo;.
        @else
          {{ $totalCount }} negócio(s) nesta categoria.
        @endif
      </p>
    </div>

    {{-- Busca local + ordenação (preserva query-string) --}}
    <form method="get" class="d-flex align-items-stretch gap-2" role="search" aria-label="Buscar na categoria">
      <input
        type="search"
        name="q"
        value="{{ $q }}"
        class="form-control form-control-sm"
        placeholder="Buscar nesta categoria..."
        aria-label="Buscar nesta categoria"
      >
      <select name="ord" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="">Ordenar por</option>
        <option value="recentes" {{ $ord==='recentes' ? 'selected' : '' }}>Mais recentes</option>
        <option value="nome_az"  {{ $ord==='nome_az'  ? 'selected' : '' }}>Nome (A&ndash;Z)</option>
        <option value="nome_za"  {{ $ord==='nome_za'  ? 'selected' : '' }}>Nome (Z&ndash;A)</option>
      </select>

      {{-- preserva outros filtros sem duplicar --}}
      @foreach(request()->except(['q','ord','page']) as $k => $v)
        <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? implode(',', $v) : $v }}">
      @endforeach

      <button class="btn btn-outline-secondary btn-sm" type="submit">Aplicar</button>
    </form>
  </div>

  {{-- Subcategorias (se houver) --}}
  @if($subcats && $subcats->count())
    <div class="mb-3">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="text-muted small me-1">Subcategorias:</span>
        @foreach($subcats->sortBy('name') as $sub)
          @php
            $sName = $sub->name ?? $sub->subcategory ?? null;
            $sSlug = $sub->slug ?? ($sName ? Str::slug($sName) : null);
          @endphp
          @if($sSlug && $sName)
            <a class="chip text-decoration-none" href="{{ route('subcategories.show', $sSlug) }}" title="{{ $sName }}">
              <span class="small text-truncate" style="max-width:14rem">{{ $sName }}</span>
            </a>
          @endif
        @endforeach
      </div>
    </div>
  @endif

  @if($totalCount === 0)
    <div class="alert alert-info" role="alert">
      Nenhum negócio nesta categoria.
      @if($q !== '')
        <div class="mt-1 small">Tente remover o filtro ou usar outros termos.</div>
      @endif
      @auth
        <div class="mt-2">
          <a class="btn btn-sm btn-primary" href="{{ route('business.create') }}">Cadastrar um negócio</a>
        </div>
      @endauth
    </div>
  @else
    {{-- Grade de cards --}}
    <div class="row g-3">
      @foreach($list as $biz)
        @php
          // ==== Escolha da imagem (sem vazar erro/JSON) ====
          $img = null;
          $cands = [
            $biz->image_lg ?? null,
            $biz->image ?? null,
            ($biz->biz_id ?? null) ? ('businesses/'.($biz->image ?? ''.($biz->biz_id).'.jpg')) : null,
          ];
          foreach ($cands as $p) {
            if (!$p) continue;
            if (is_string($p) && Str::startsWith($p, ['http://','https://'])) { $img = $p; break; }
            if (is_string($p) && Storage::disk('public')->exists($p)) { $img = Storage::url($p); break; }
            if (is_string($p) && file_exists(public_path($p))) { $img = url($p); break; }
          }
          if (!$img) $img = asset('images/placeholder-800x600.png');

          // Campos exibidos (somente strings/escapados)
          $name = $biz->business_name ?? $biz->name ?? 'Negócio';
          $desc = $biz->short_description ?? $biz->description ?? '';
          $city = $biz->city_name ?? $biz->city ?? $biz->cidade ?? '';
          $cat  = $biz->category_name ?? $biz->category ?? $biz->cat_name ?? '';
          $slug = Str::slug($name);
          $url  = !empty($biz->biz_id) ? route('business.show', [$biz->biz_id, $slug]) : '#';
        @endphp

        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 shadow-sm hoverable">
            <a href="{{ $url }}" class="text-decoration-none" aria-label="Abrir {{ $name }}">
              <div class="ratio ratio-4x3 bg-light">
                <img
                  src="{{ $img }}"
                  alt="Imagem de {{ $name }}"
                  class="w-100 h-100"
                  style="object-fit: cover;"
                  loading="lazy"
                  decoding="async"
                  fetchpriority="low"
                >
              </div>
            </a>

            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-start gap-2 mb-2">
                <a href="{{ $url }}" class="flex-grow-1 text-decoration-none text-dark">
                  <h3 class="h6 mb-1 text-truncate" title="{{ $name }}">{{ $name }}</h3>
                </a>
                {{-- Badges opcionais (strings apenas) --}}
                @if(is_string($cat) && $cat !== '')
                  <span class="badge text-bg-light border badge-truncate" title="{{ $cat }}">{{ $cat }}</span>
                @endif
                @if(is_string($city) && $city !== '')
                  <span class="badge text-bg-light border badge-truncate" title="{{ $city }}">{{ $city }}</span>
                @endif
              </div>

              @if (is_string($desc) && trim($desc) !== '')
                <p class="card-text small text-muted mb-3 line-2">{{ $desc }}</p>
              @endif

              <div class="mt-auto d-flex align-items-center justify-content-between">
                <a href="{{ $url }}" class="btn btn-primary btn-sm">Ver detalhes</a>

                {{-- Bookmark opcional, só se rota existir e usuário logado --}}
                @auth
                  @if (Route::has('business.bookmark') && !empty($biz->biz_id))
                    <form method="POST" action="{{ route('business.bookmark', $biz->biz_id) }}">
                      @csrf
                      <button class="btn btn-outline-secondary btn-sm" type="submit" title="Salvar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                             fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16" aria-hidden="true">
                          <path d="M2 2v13.5l6-3 6 3V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>
                        </svg>
                      </button>
                    </form>
                  @endif
                @endauth
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Paginação (se for paginator) --}}
    @if($isPaginator && method_exists($list, 'links'))
      <div class="mt-4">
        {{ $list->withQueryString()->links() }}
      </div>
    @endif
  @endif
</div>
@endsection
