@extends('layouts.app')

@section('title', $pageTitle ?? ($title ?? (View::shared('title') ?? 'Cidades')))

@section('content')
<div class="container py-4">

  {{-- UI polish (apenas CSS – não quebra nada) --}}
  <style>
    .ui-hero {
      background: linear-gradient(135deg, rgba(99,102,241,.08), rgba(59,130,246,.06));
      border: 1px solid rgba(0,0,0,.04);
      border-radius: 16px;
      padding: 20px;
    }
    .ui-hero h1 { letter-spacing: -.02em; }
    .ui-quiet { color: #64748b; }
    .ui-search { border-radius: 12px; box-shadow: 0 8px 20px rgba(2,6,23,.06); }
    .ui-card {
      border-radius: 14px;
      transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
      border-color: rgba(0,0,0,.06) !important;
    }
    .ui-card:hover { transform: translateY(-3px); box-shadow: 0 18px 32px rgba(2,6,23,.12); border-color: rgba(59,130,246,.25) !important; }
    .ui-ratio { aspect-ratio: 4 / 3; }
    .ui-badge {
      border-radius: 9999px; background: #f8fafc; border: 1px solid rgba(100,116,139,.24);
      padding: .15rem .5rem; max-width: 10rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .ui-line-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .ui-toolbar { border: 1px solid rgba(0,0,0,.06); border-radius: 12px; background: #fff; }
    .ui-empty { border: 1px dashed rgba(100,116,139,.35); border-radius: 14px; background: #fff; }
    .ui-stat { display:inline-flex; align-items:center; gap:.5rem; padding:.25rem .6rem; border-radius:9999px; background:#eef2ff; color:#3730a3; font-weight:600; font-size:.85rem; }
    .ui-link { text-underline-offset: 3px; }
  </style>

  @php
    // --------- Helpers de rótulo seguro (evita "vazar" JSON/objeto) ----------
    $safe = function($v, $keys = []) {
      if (is_string($v)) return trim($v);
      if (is_object($v) || is_array($v)) {
        foreach ($keys as $k) {
          $val = is_object($v) ? ($v->{$k} ?? null) : ($v[$k] ?? null);
          if (is_string($val) && trim($val) !== '') return trim($val);
        }
      }
      return '';
    };

    // Label da cidade (compat com legado)
    $cityLabel = $safe($city->city ?? null, ['city','name','label'])
              ?: ($city->name ?? $city->label ?? 'Cidade');

    // Filtros vindos do controller
    $qValue = $q ?? '';
    $catId  = $catId ?? null;

    // Lista de categorias pode vir com nomes diferentes
    $catText = function($c) use ($safe) {
      $label = $safe($c, ['category','category_name','cat_name','name','label']);
      return $label !== '' ? $label : ('Categoria #'.($c->cat_id ?? ''));
    };
  @endphp

  {{-- Header aprimorado --}}
  <div class="ui-hero mb-4">
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3">
      <div>
        <h1 class="h3 mb-1">Negócios em {{ $cityLabel }}</h1>
        <p class="mb-0 ui-quiet">Procure e filtre os negócios desta cidade.</p>
      </div>

      <form method="GET" class="d-flex align-items-stretch gap-2" role="search" aria-label="Buscar na cidade">
        <input
          type="search"
          name="q"
          value="{{ $qValue }}"
          class="form-control ui-search"
          placeholder="Buscar por nome ou descrição..."
          aria-label="Buscar por nome ou descrição">
        @foreach(request()->except(['q','page']) as $k => $v)
          <input type="hidden" name="{{ $k }}" value="{{ is_array($v) ? implode(',', $v) : $v }}">
        @endforeach
        <button class="btn btn-primary px-4" type="submit">Buscar</button>
      </form>
    </div>
  </div>

  {{-- Filtros detalhados (toolbar compacta) --}}
  <form method="GET" class="card ui-toolbar mb-3">
    <div class="card-body py-3">
      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label for="q" class="form-label mb-1">Buscar</label>
          <input id="q" type="text" name="q" value="{{ $qValue }}" class="form-control" placeholder="Buscar por nome ou descrição...">
        </div>
        <div class="col-md-4">
          <label for="categoria" class="form-label mb-1">Categoria</label>
          <select id="categoria" name="categoria" class="form-select">
            <option value="">Todas</option>
            @foreach($categories as $c)
              @php $id = $c->cat_id ?? null; @endphp
              <option value="{{ $id }}" {{ (string)($catId ?? '') === (string)($id ?? '') ? 'selected' : '' }}>
                {{ $catText($c) }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100" type="submit">Filtrar</button>
        </div>
      </div>
    </div>
  </form>

  {{-- Resumo com chip/estatística --}}
  @php
    $total = method_exists($businesses ?? null, 'total') ? $businesses->total() : (($businesses ?? collect())->count());
  @endphp
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    <span class="ui-stat" title="Total de resultados">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
      {{ $total }} resultado(s)
    </span>
    <span class="ui-quiet">em <strong>{{ $cityLabel }}</strong></span>
    @if(filled($qValue)) <span class="ui-quiet">&bull; termo: &ldquo;{{ $qValue }}&rdquo;</span> @endif
    @if(filled($catId))  <span class="ui-quiet">&bull; categoria: #{{ $catId }}</span> @endif
  </div>

  {{-- Lista / grade --}}
  @if(($businesses ?? collect())->count())
    <div class="row g-3">
      @foreach($businesses as $biz)
        @php
          // ====== IMAGEM (compat com legado) ======
          $img = null;
          $cands = [
            $biz->image_lg ?? null,
            $biz->image ?? null,
            ($biz->biz_id ?? null) ? ('businesses/'.($biz->image ?? ''.($biz->biz_id).'.jpg')) : null,
          ];
          foreach ($cands as $p) {
            if (!$p) continue;
            if (is_string($p) && \Illuminate\Support\Str::startsWith($p, ['http://','https://'])) { $img = $p; break; }
            if (is_string($p) && \Illuminate\Support\Facades\Storage::disk('public')->exists($p)) { $img = \Illuminate\Support\Facades\Storage::url($p); break; }
            if (is_string($p) && file_exists(public_path($p))) { $img = url($p); break; }
          }
          if (!$img) $img = asset('images/placeholder-800x600.png');

          $name = $biz->business_name ?? $biz->name ?? 'Negócio';
          $desc = $biz->short_description ?? $biz->description ?? '';

          // Badges opcionais (como texto, nunca objeto/array)
          $bizCity = $safe(($biz->city ?? null), ['city','name','label'])
                 ?: ($biz->city_name ?? $biz->cidade ?? '');
          $bizCat  = $safe(($biz->category ?? null), ['category','name','label','cat_name','category_name'])
                 ?: ($biz->category_name ?? $biz->cat_name ?? '');
          
          $slug = \Illuminate\Support\Str::slug($name);
          $url  = !empty($biz->biz_id) ? route('business.show', [$biz->biz_id, $slug]) : '#';
        @endphp

        <div class="col-12 col-sm-6 col-lg-4">
          <div class="card h-100 shadow-sm ui-card">
            <a href="{{ $url }}" class="text-decoration-none" aria-label="Ver {{ $name }}">
              <div class="bg-light ui-ratio w-100 rounded-top overflow-hidden">
                <img src="{{ $img }}" alt="Imagem de {{ $name }}" class="w-100 h-100" style="object-fit:cover;" loading="lazy" decoding="async" fetchpriority="low">
              </div>
            </a>

            <div class="card-body d-flex flex-column">
              <div class="d-flex align-items-start gap-2 mb-2">
                <a href="{{ $url }}" class="flex-grow-1 text-decoration-none text-dark">
                  <h3 class="h6 mb-1 text-truncate" title="{{ $name }}">{{ $name }}</h3>
                </a>
                @if(is_string($bizCat) && trim($bizCat)!=='') <span class="ui-badge" title="{{ $bizCat }}">{{ $bizCat }}</span>@endif
                @if(is_string($bizCity) && trim($bizCity)!=='') <span class="ui-badge" title="{{ $bizCity }}">{{ $bizCity }}</span>@endif
              </div>

              @if (is_string($desc) && trim($desc)!=='')
                <p class="card-text small text-muted mb-3 ui-line-2">{{ $desc }}</p>
              @endif

              <div class="mt-auto d-flex justify-content-between align-items-center">
                <a href="{{ $url }}" class="btn btn-primary btn-sm">Ver detalhes</a>
                @auth
                  @if (Route::has('business.bookmark') && !empty($biz->biz_id))
                    <form method="POST" action="{{ route('business.bookmark', $biz->biz_id) }}">
                      @csrf
                      <button class="btn btn-outline-secondary btn-sm" type="submit" title="Salvar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bookmark" viewBox="0 0 16 16" aria-hidden="true">
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

    {{-- Paginação --}}
    @if(method_exists($businesses, 'links'))
      <div class="mt-4 d-flex justify-content-center">
        {{ $businesses->withQueryString()->links() }}
      </div>
    @endif

  @else
    <div class="ui-empty p-5 text-center ui-quiet mb-3">
      <div class="mb-2" aria-hidden="true">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19v2H3v-2h18zM7 4h10a2 2 0 0 1 2 2v9H5V6a2 2 0 0 1 2-2zm0 2v7h10V6H7z"/></svg>
      </div>
      Nenhum negócio encontrado com os filtros aplicados.
    </div>
  @endif

  <div class="mt-4">
    <a href="{{ route('cities.index') }}" class="ui-link">&larr; Voltar para cidades</a>
  </div>
</div>
@endsection
