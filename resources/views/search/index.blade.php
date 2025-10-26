@extends('layouts.app', ['title' => 'Busca'])

@section('content')
  {{-- Caixa de busca existente (mantida) --}}
  @include('partials.search_box')

  @php
    use Illuminate\Support\Str;

    // $items pode ser paginator ou coleÃ§Ã£o; garantimos objeto iterÃ¡vel
    $results = $items ?? collect();

    // termo da busca (se houver)
    $q = trim((string) request('q', ''));

    // contagem segura
    $total = method_exists($results ?? null, 'total')
      ? (int) $results->total()
      : ((method_exists($results ?? null, 'count') ? $results->count() : 0));

    // helper para destacar o termo pesquisado (sem quebrar HTML)
    $hl = function (?string $text) use ($q) {
      $text = (string) $text;
      if ($text === '' || $q === '') return e($text);
      $escaped = e($text);
      $pattern = '/(' . preg_quote($q, '/') . ')/i';
      return preg_replace($pattern, '<mark>$1</mark>', $escaped);
    };

    // helper para montar imagem (se existir)
    $pickImg = function ($b) {
      $cands = [
        $b->image_lg ?? null,
        $b->image ?? null,
        ($b->biz_id ?? null) ? ('businesses/'.(($b->image ?? '').($b->biz_id ?? '').'.jpg')) : null,
      ];
      foreach ($cands as $p) {
        if (!$p || !is_string($p)) continue;
        if (Str::startsWith($p, ['http://','https://'])) return $p;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($p)) return \Illuminate\Support\Facades\Storage::url($p);
        if (file_exists(public_path($p))) return url($p);
      }
      return asset('images/placeholder-800x600.png');
    };
  @endphp

  {{-- Styles de melhoria (apenas visual, nÃ£o interfere nas rotas/lÃ³gica) --}}
  <style>
    .sr-hero{background:linear-gradient(135deg,rgba(99,102,241,.08),rgba(59,130,246,.06));border:1px solid rgba(0,0,0,.05);border-radius:16px;padding:18px;margin-bottom:14px}
    .sr-quiet{color:#64748b}
    .sr-chip{display:inline-flex;align-items:center;gap:.35rem;border:1px solid rgba(100,116,139,.25);background:#f8fafc;border-radius:9999px;padding:.2rem .55rem;font-size:.8rem}
    .sr-count{display:inline-flex;align-items:center;gap:.5rem;padding:.25rem .6rem;border-radius:9999px;background:#eef2ff;color:#3730a3;font-weight:600}
    .sr-grid{display:grid;grid-template-columns:1fr;gap:12px}
    @media (min-width:576px){.sr-grid{grid-template-columns:1fr}}
    @media (min-width:768px){.sr-grid{grid-template-columns:1fr 1fr}}
    @media (min-width:1200px){.sr-grid{grid-template-columns:1fr 1fr 1fr}}
    .sr-card{border-radius:14px;border-color:rgba(0,0,0,.06)!important;transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease}
    .sr-card:hover{transform:translateY(-3px);box-shadow:0 18px 32px rgba(2,6,23,.12);border-color:rgba(59,130,246,.22)!important}
    .sr-media{width:100%;aspect-ratio:16/9;background:#f1f5f9;border-top-left-radius:14px;border-top-right-radius:14px;overflow:hidden}
    .sr-media>img{width:100%;height:100%;object-fit:cover}
    .sr-desc{display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
    .sr-tags{display:flex;flex-wrap:wrap;gap:.4rem}
    .sr-empty{border:1px dashed rgba(100,116,139,.35);border-radius:14px;background:#fff}
    .sr-link{ text-underline-offset: 3px; }
  </style>

  {{-- Header da busca com contador e contexto --}}
  <div class="container px-0">
    <div class="sr-hero d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
      <div>
        <h1 class="h4 mb-1">Resultados da busca</h1>
        <p class="mb-0 sr-quiet">
          @if($q !== '')
            Mostrando resultados para <strong>â€œ{{ e($q) }}â€</strong>.
          @else
            Refine sua busca para encontrar negÃ³cios.
          @endif
        </p>
      </div>
      <div class="sr-count" title="Total de resultados">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        {{ $total }}
      </div>
    </div>
  </div>

  {{-- Lista de resultados --}}
  @if(($results instanceof \Illuminate\Support\Collection && $results->count()) || (method_exists($results,'count') && $results->count()))
    <div class="sr-grid">
      @foreach($results as $b)
        @php
          $id    = $b->biz_id ?? $b->id ?? null;
          $name  = $b->business_name ?? $b->name ?? 'NegÃ³cio';
          $slug  = Str::slug($name ?: 'negocio');
          $url   = $id ? route('business.show', [$id, $slug]) : 'javascript:void(0)';

          $city  = $b->city_name ?? $b->cidade ?? $b->city ?? null;
          $cat   = $b->category_name ?? $b->category ?? $b->cat_name ?? null;
          $desc  = $b->short_description ?? $b->description ?? '';

          $img   = $pickImg($b);
        @endphp

        <div class="card shadow-sm sr-card">
          <a href="{{ $url }}" class="text-decoration-none" aria-label="Abrir {{ $name }}">
            <div class="sr-media">
              <img src="{{ $img }}" alt="Imagem de {{ $name }}" loading="lazy" decoding="async" fetchpriority="low">
            </div>
          </a>

          <div class="card-body">
            <h5 class="card-title mb-1">
              <a href="{{ $url }}" class="text-decoration-none">{!! $hl($name) !!}</a>
            </h5>

            {{-- chips de contexto --}}
            <div class="sr-tags mb-2">
              @if($city)
                <span class="sr-chip" title="Cidade">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l7 6v12h-5v-7H10v7H5V8l7-6z"/></svg>
                  {{ $city }}
                </span>
              @endif
              @if($cat)
                <span class="sr-chip" title="Categoria">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 5v14h18V5H3zm16 12H5V7h14v10z"/></svg>
                  {{ $cat }}
                </span>
              @endif
            </div>

            @if($desc)
              <p class="card-text mb-0 sr-desc">
                {!! $hl(Str::limit(strip_tags($desc), 220)) !!}
              </p>
            @endif
          </div>
        </div>
      @endforeach
    </div>

    {{-- PaginaÃ§Ã£o (preserva querystring) --}}
    @if(method_exists($results, 'links'))
      <div class="mt-4 d-flex justify-content-center">
        {{ $results->withQueryString()->links() }}
      </div>
    @endif
  @else
    <div class="sr-empty p-5 text-center sr-quiet">
      <div class="mb-2">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M10 18H6a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2h8l4 4v8a2 2 0 0 1-2 2h-4v2H8v-2h2v-2zM6 6v10h12V9h-5V4H6z"/></svg>
      </div>
      Nenhum resultado.
      @if($q !== '')
        <div class="mt-1">Tente palavras-chave diferentes ou mais curtas.</div>
      @endif
      <div class="mt-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm me-2">Voltar</a>
        <a href="{{ url('/') }}" class="btn btn-primary btn-sm">Ir para a pÃ¡gina inicial</a>
      </div>
    </div>
  @endif
@endsection
