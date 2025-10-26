{{-- resources/views/bookmarks/index.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  use Illuminate\Support\Str;

  $items = $bookmarks ?? collect();

  $isPaginator = $items instanceof \Illuminate\Pagination\Paginator
              || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
  $hasItems = $isPaginator ? $items->count() > 0 : (count($items) > 0);
@endphp

{{-- TOPO estilo legado (faixa vermelha) --}}
<section class="bl-hero">
  <div class="bl-hero__inner">
    <h1>Meus favoritos</h1>
    <div class="bl-hero__actions">
      <a href="{{ route('search.index') }}" class="bl-btn bl-btn--light">＋ Buscar negócios</a>
    </div>
  </div>
</section>

<div class="mx-auto max-w-6xl px-4 md:px-6 py-6 md:py-8">
  {{-- flashes --}}
  @foreach (['success' => 'green', 'error' => 'red', 'status' => 'blue'] as $key => $color)
    @if (session($key))
      <div class="mb-4 rounded-lg border border-{{ $color }}-200 bg-{{ $color }}-50 px-4 py-3 text-{{ $color }}-800">
        {{ session($key) }}
      </div>
    @endif
  @endforeach

  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
    {{-- COLUNA ESQUERDA (lista) --}}
    <div class="md:col-span-8 space-y-4">
      @if (!$hasItems)
        <div class="bl-panel">
          <div class="bl-panel__heading">Você ainda não tem favoritos</div>
          <div class="bl-panel__body">
            <p class="text-slate-600">Encontre empresas por cidade ou categoria e salve as que gostar.</p>
            <a href="{{ route('search.index') }}" class="bl-btn bl-btn--primary mt-3">Explorar negócios</a>
          </div>
        </div>
      @else
        @foreach ($items as $row)
          @php
            // Normalização de campos (funciona com Eloquent ou DB::table)
            $pk     = $row->bookmark_pk ?? $row->id ?? $row->bookmark_id ?? $row->bm_id ?? null;
            $bizId  = $row->biz_id ?? $row->bm_biz_id ?? $row->business_id ?? $row->bizid ?? null;
            $name   = trim($row->business_name ?? $row->name ?? 'Negócio');
            $city   = $row->city ?? null;

            $imgRaw = $row->image_lg ?? $row->image ?? null;
            $imgUrl = $imgRaw
              ? (Str::startsWith($imgRaw, ['http://','https://','/']) ? $imgRaw : asset($imgRaw))
              : null;

            $slug   = Str::slug($name);
            $bizUrl = $bizId ? route('business.show', ['id' => $bizId, 'slug' => $slug]) : '#';
          @endphp

          <article class="bl-panel hover:shadow-sm transition">
            <header class="bl-panel__heading flex items-center gap-3">
              <a href="{{ $bizUrl }}" class="truncate hover:underline">{{ $name }}</a>
              {{-- nota/estrelas “placeholder” para lembrar o legado --}}
              <div class="ml-auto flex items-center gap-1 text-amber-400" aria-label="avaliação (placeholder)">
                <span>☆</span><span>☆</span><span>☆</span><span>☆</span><span>☆</span>
              </div>
            </header>

            <div class="bl-panel__body">
              <div class="flex items-start gap-4">
                <a href="{{ $bizUrl }}" class="block shrink-0 w-28 h-20 rounded border border-slate-200 bg-slate-50 overflow-hidden">
                  @if ($imgUrl)
                    <img src="{{ $imgUrl }}" alt="" class="w-full h-full object-cover">
                  @else
                    <div class="grid w-full h-full place-items-center text-slate-400">🏷️</div>
                  @endif
                </a>

                <div class="min-w-0 flex-1">
                  @if ($city)
                    <div class="text-sm text-slate-600 flex items-center gap-1">
                      <svg class="w-4 h-4 opacity-70" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5Z"/></svg>
                      {{ $city }}
                    </div>
                  @endif

                  <div class="mt-2 flex flex-wrap items-center gap-2">
                    <a href="{{ $bizUrl }}" class="bl-btn bl-btn--ghost">Ver detalhes</a>

                    @if ($bizId)
                      <a href="{{ route('search.index') }}" class="bl-btn bl-btn--ghost">Buscar similares</a>
                    @endif
                  </div>
                </div>

                {{-- coluna de ações (remover) --}}
                @if($pk !== null)
                  <form method="POST" action="{{ route('bookmarks.destroy', $pk) }}"
                        onsubmit="return confirm('Remover dos favoritos?')"
                        class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button class="bl-btn bl-btn--danger">Remover</button>
                  </form>
                @endif
              </div>
            </div>
          </article>
        @endforeach

        {{-- paginação --}}
        @if (method_exists($items, 'links'))
          <div class="mt-6 bl-pager flex justify-center">
            {{ $items->onEachSide(1)->links() }}
          </div>
        @endif
      @endif
    </div>

    {{-- COLUNA DIREITA (sidebar) --}}
    <aside class="md:col-span-4 space-y-4">
      <div class="bl-aside">
        <div class="bl-aside__heading">Resumo</div>
        <div class="bl-aside__body">
          <div class="flex items-center justify-between">
            <span>Favoritos salvos</span>
            <strong>
              {{ $isPaginator ? ($items->total() ?? $items->count()) : (is_countable($items) ? count($items) : 0) }}
            </strong>
          </div>
        </div>
      </div>

      <div class="bl-aside">
        <div class="bl-aside__heading">Acesso rápido</div>
        <div class="bl-aside__body">
          <ul class="space-y-2 text-sm">
            <li><a class="bl-link" href="{{ url('/') }}">Início</a></li>
            <li><a class="bl-link" href="{{ route('categories.index') }}">Categorias</a></li>
            <li><a class="bl-link" href="{{ route('cities.index') }}">Cidades</a></li>
            <li><a class="bl-link" href="{{ route('business.create') }}">Cadastrar negócio</a></li>
            <li><a class="bl-link" href="{{ route('search.index') }}">Buscar</a></li>
            <li><a class="bl-link" href="{{ route('contact.show') }}">Contato</a></li>
          </ul>
        </div>
      </div>

      <div class="bl-aside">
        <div class="bl-aside__heading">Dica</div>
        <div class="bl-aside__body text-sm text-slate-600">
          Clique em <strong>Ver detalhes</strong> para abrir a página do negócio e conferir endereço, descrição,
          mapa e avaliações — igual ao sistema antigo, mas com visual atualizado.
        </div>
      </div>
    </aside>
  </div>
</div>

{{-- CSS de acabamento para “legado moderno” --}}
<style>
  /* Faixa vermelha do topo */
  .bl-hero{background:#c41210;color:#fff}
  .bl-hero__inner{max-width:72rem;margin:0 auto;padding:14px 1rem;display:flex;align-items:center;gap:12px}
  .bl-hero h1{font-size:1.25rem;font-weight:800;letter-spacing:.2px}
  @media(min-width:768px){.bl-hero__inner{padding:18px 1.5rem}.bl-hero h1{font-size:1.5rem}}
  .bl-hero__actions{margin-left:auto}

  /* Botões */
  .bl-btn{display:inline-flex;align-items:center;gap:.5rem;border-radius:.5rem;font-weight:600;line-height:1;padding:.625rem .9rem}
  .bl-btn--primary{background:#c41210;color:#fff}
  .bl-btn--primary:hover{filter:brightness(1.08)}
  .bl-btn--light{background:#fff;color:#c41210}
  .bl-btn--light:hover{background:#fff;filter:brightness(0.98)}
  .bl-btn--ghost{background:#fff;border:1px solid #e5e7eb;color:#374151}
  .bl-btn--ghost:hover{background:#f8fafc}
  .bl-btn--danger{background:#fee2e2;color:#b91c1c;border:1px solid #fecaca}
  .bl-btn--danger:hover{background:#fecaca}

  /* Painéis (lista) */
  .bl-panel{border:1px solid #e5e7eb;border-radius:.75rem;background:#fff;overflow:hidden}
  .bl-panel__heading{background:#f5f5f5;border-bottom:1px solid #e5e7eb;padding:.75rem 1rem;font-weight:700;color:#1f2937}
  .bl-panel__body{padding:1rem}

  /* Aside (sidebar) */
  .bl-aside{border:1px solid #e5e7eb;border-radius:.75rem;background:#fff;overflow:hidden}
  .bl-aside__heading{background:#f5f5f5;border-bottom:1px solid #e5e7eb;padding:.6rem .9rem;font-weight:700;color:#1f2937}
  .bl-aside__body{padding:.9rem}

  .bl-link{color:#c41210;text-decoration:none}
  .bl-link:hover{text-decoration:underline}

  /* Paginação “escura”, sem template externo */
  .bl-pager nav[role="navigation"] a,
  .bl-pager nav[role="navigation"] span{color:#1f2937 !important}
  .bl-pager nav[role="navigation"] a:hover{background:#f1f5f9 !important;color:#0f172a !important}
  .bl-pager .relative.inline-flex.items-center{color:#1f2937 !important}
  .bl-pager .opacity-50,.bl-pager .cursor-default{color:#94a3b8 !important}
</style>
@endsection
