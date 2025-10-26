@extends('layouts.app', ['title' => $title ?? 'Início'])

@section('content')
  {{-- Banner de busca (hero) --}}
  @includeIf('partials.hero_search')

  {{-- Caixa de busca menor (legado) --}}
  @includeIf('partials.search_box')

  {{-- Conteúdo introdutório + links úteis --}}
  <div class="row g-3 mb-4">
    <div class="col-lg-8">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
          <h1 class="h4 card-title mb-2">Bem-vindo(a)!</h1>
          <p class="mb-0 text-secondary">
            Use a busca acima para encontrar negócios por <strong>cidade</strong>, <strong>nome</strong> ou <strong>tags</strong>.
          </p>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h6 card-title text-uppercase text-muted mb-3">Links úteis</h2>
          <ul class="list-unstyled mb-0">
            <li class="mb-2">
              <a class="link-body-emphasis text-decoration-none" href="{{ url('/sobre') }}">
                <i class="bi bi-info-circle me-2" aria-hidden="true"></i>Sobre
              </a>
            </li>
            <li class="mb-2">
              <a class="link-body-emphasis text-decoration-none" href="{{ url('/termos') }}">
                <i class="bi bi-file-text me-2" aria-hidden="true"></i>Termos
              </a>
            </li>
            <li>
              <a class="link-body-emphasis text-decoration-none" href="{{ url('/politica-de-privacidade') }}">
                <i class="bi bi-shield-lock me-2" aria-hidden="true"></i>Privacidade
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  {{-- Título da listagem --}}
  <div class="d-flex align-items-center justify-content-between mb-2">
    <h2 class="h5 mb-0">Negócios recentes</h2>

    {{-- Ordenação opcional (mantém compatibilidade; remova se não usar) --}}
    <div class="dropdown">
      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Ordenar
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        @php $ord = request('ord'); @endphp
        <li><a class="dropdown-item {{ $ord==='recentes'||!$ord ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['ord'=>'recentes']) }}">Mais recentes</a></li>
        <li><a class="dropdown-item {{ $ord==='nome' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['ord'=>'nome']) }}">Nome (A–Z)</a></li>
        <li><a class="dropdown-item {{ $ord==='cidade' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['ord'=>'cidade']) }}">Cidade</a></li>
      </ul>
    </div>
  </div>

  @php use Illuminate\Support\Str; @endphp

  @if(isset($items) && $items->count())
    <div class="row g-3">
      @foreach($items as $b)
        <div class="col-12 col-md-6 col-lg-4">
          <article class="card h-100 border-0 shadow-sm hover-card" itemscope itemtype="https://schema.org/LocalBusiness">
            {{-- Imagem de capa opcional --}}
            @if(!empty($b->cover_url))
              <a class="ratio ratio-16x9" href="{{ url('/negocio/'.$b->biz_id) }}" aria-label="Abrir {{ $b->business_name }}">
                <img
                  src="{{ $b->cover_url }}"
                  class="card-img-top object-fit-cover rounded-top"
                  alt="Foto de {{ $b->business_name }}"
                  loading="lazy"
                  itemprop="image">
              </a>
            @endif

            <div class="card-body">
              <h3 class="h6 card-title mb-1" itemprop="name">
                <a class="stretched-link text-decoration-none link-dark" href="{{ url('/negocio/'.$b->biz_id) }}">
                  {{ $b->business_name }}
                </a>
              </h3>

              <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                @if(!empty($b->city))
                  <span class="d-inline-flex align-items-center">
                    <svg width="14" height="14" viewBox="0 0 16 16" class="me-1" aria-hidden="true">
                      <path fill="currentColor" d="M8 0a5.5 5.5 0 0 0-5.5 5.5C2.5 9.1 8 16 8 16s5.5-6.9 5.5-10.5A5.5 5.5 0 0 0 8 0m0 8a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5"/>
                    </svg>
                    <span itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                      <span itemprop="addressLocality">{{ $b->city }}</span>
                    </span>
                  </span>
                @endif

                @if(!empty($b->created_at))
                  <span aria-label="Publicado {{ \Carbon\Carbon::parse($b->created_at)->diffForHumans() }}">
                    • {{ \Carbon\Carbon::parse($b->created_at)->diffForHumans() }}
                  </span>
                @endif
              </div>

              <p class="card-text text-secondary mb-3" itemprop="description">
                {{ Str::limit($b->description ?? '', 140) ?: 'Sem descrição informada.' }}
              </p>

              {{-- Tags (se existir $b->tags como array/collection de strings) --}}
              @if(!empty($b->tags) && count((array)$b->tags))
                <div class="d-flex flex-wrap gap-1" aria-label="Tags">
                  @foreach((array)$b->tags as $tag)
                    <span class="badge text-bg-light border">{{ $tag }}</span>
                  @endforeach
                </div>
              @endif
            </div>
          </article>
        </div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $items->withQueryString()->links() }}
    </div>
  @else
    <div class="alert alert-secondary d-flex align-items-center" role="status">
      <i class="bi bi-search me-2" aria-hidden="true"></i>
      <div>Nenhum negócio encontrado.</div>
    </div>
  @endif
@endsection

@push('styles')
<style>
  .hover-card { transition: transform .15s ease, box-shadow .15s ease; }
  .hover-card:hover { transform: translateY(-2px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08); }
  .object-fit-cover { object-fit: cover; }
</style>
@endpush
