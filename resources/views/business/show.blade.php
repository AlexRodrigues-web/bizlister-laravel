{{-- resources/views/business/show.blade.php --}}
@extends('layouts.app')

@section('title', $biz->business_name ?? 'Negócio')

@section('content')
@php
  // Normaliza a instância do negócio
  $biz = $biz ?? ($business ?? ($item ?? ($company ?? null)));

  // Helper para extrair strings de objetos/arrays
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

  // Categoria (apenas string)
  $catLabel = '';
  if (isset($category)) {
      $catLabel = $safe($category, ['category','name','label','cat_name','category_name']);
  }
  if ($catLabel === '' && $biz) {
      $catLabel = $biz->category_name
                 ?? $biz->cat_name
                 ?? $safe(($biz->category ?? null), ['category','name','label','cat_name','category_name'])
                 ?? '';
  }
  if ($catLabel === '' && $biz) { $catLabel = (string)($biz->cid ?? ''); }

  // Cidade (apenas string)
  $cityLabel = '';
  if (isset($city)) {
      $cityLabel = $safe($city, ['city','name','label']);
  }
  if ($cityLabel === '' && $biz) {
      $cityLabel = $biz->city_name
                 ?? $biz->cidade
                 ?? $safe(($biz->city ?? null), ['city','name','label'])
                 ?? '';
  }
  if ($cityLabel === '' && $biz) { $cityLabel = (string)($biz->city ?? ''); }

  // ===== IMAGENS =====
  use Illuminate\Support\Facades\Storage;

  $heroImg = null;
  if (!empty($biz->image_lg) && Storage::disk('public')->exists($biz->image_lg)) {
      $heroImg = asset('storage/'.$biz->image_lg);
  } elseif (!empty($biz->image) && Storage::disk('public')->exists($biz->image)) {
      $heroImg = asset('storage/'.$biz->image);
  } else {
      $heroImg = asset('images/placeholder-1600x600.png');
  }

  $sideImg = null;
  if (!empty($biz->image) && Storage::disk('public')->exists($biz->image)) {
      $sideImg = asset('storage/'.$biz->image);
  } elseif (!empty($biz->image_lg) && Storage::disk('public')->exists($biz->image_lg)) {
      $sideImg = asset('storage/'.$biz->image_lg);
  } else {
      $sideImg = asset('images/placeholder-800x600.png');
  }

  // ===== COMPARTILHAMENTO / BOOKMARK =====
  $shareUrl   = urlencode(url()->current());
  $shareTitle = urlencode((string)($biz->business_name ?? config('app.name')));
  $shareImg   = urlencode($heroImg);

  // Contador de bookmarks (se existir relação/atributo, senão 0)
  $bookmarkCount = 0;
  try {
      if (method_exists($biz, 'bookmarks')) {
          $bookmarkCount = (int) $biz->bookmarks()->count();
      } elseif (isset($biz->bookmarks_count)) {
          $bookmarkCount = (int) $biz->bookmarks_count;
      }
  } catch (\Throwable $e) {
      $bookmarkCount = 0;
  }
@endphp

<div class="container py-4">

  {{-- Flash --}}
  @if (session('success') || session('status'))
    <div class="alert alert-success">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
      <li class="breadcrumb-item"><a href="{{ route('cities.index') }}">Cidades</a></li>
      @if($cityLabel)<li class="breadcrumb-item active" aria-current="page">{{ $cityLabel }}</li>@endif
    </ol>
  </nav>

  {{-- Título + badges --}}
  <header class="mb-3">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
      <h1 class="h3 mb-0 text-truncate">{{ $biz->business_name }}</h1>
      <div class="d-flex flex-wrap gap-2">
        @if($catLabel)
          <span class="badge text-bg-light border">{{ $catLabel }}</span>
        @endif
        @if($cityLabel)
          <span class="badge text-bg-light border">{{ $cityLabel }}</span>
        @endif
      </div>
    </div>
  </header>

  {{-- ===== Imagem HERO (imagem #1) ===== --}}
  <div class="card overflow-hidden mb-3">
    <div class="ratio ratio-21x9 bg-light">
      <img src="{{ $heroImg }}" alt="Imagem principal de {{ $biz->business_name }}" class="img-fluid w-100 h-100" style="object-fit:cover;">
    </div>
  </div>

  {{-- Conteúdo: coluna principal + sidebar --}}
  <div class="row g-3 mb-3">
    <div class="col-lg-8 d-flex flex-column gap-3">

      {{-- Sobre --}}
      <div class="card">
        <div class="card-header bg-white"><strong>Sobre</strong></div>
        <div class="card-body">
          @include('business._about', ['business' => ($business ?? ($biz ?? ($item ?? ($company ?? null))))])
        </div>
      </div>

      {{-- (OPCIONAL) Mini formulário de upload — visível somente para logados --}}
      @auth
      <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
          <strong>Enviar fotos para a galeria</strong>
          <small class="text-muted">JPG/PNG/WEBP • até 2MB cada</small>
        </div>
        <div class="card-body">
          <form method="POST"
                action="{{ route('business.gallery.store', $biz->biz_id) }}"
                enctype="multipart/form-data"
                class="row g-2">
            @csrf

            <div class="col-12">
              <input type="file"
                     name="images[]"
                     class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                     accept=".jpg,.jpeg,.png,.webp"
                     multiple
                     required>
              @error('images')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              @error('images.*')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
              <div class="form-text">
                Selecione uma ou mais imagens. Elas aparecerão na galeria após o envio.
              </div>
            </div>

            <div class="col-12 d-flex gap-2">
              <button type="submit" class="btn btn-primary">Enviar</button>
              <a href="#galeria" class="btn btn-outline-secondary">Pular</a>
            </div>
          </form>
        </div>
      </div>
      @endauth

      {{-- Galeria --}}
      <div id="galeria" class="card">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
          <strong>Galeria</strong>

          @auth
            @if (Route::has('business.gallery.index') && !empty($biz->biz_id))
              <a href="{{ route('business.gallery.index', $biz->biz_id) }}"
                 class="btn btn-sm btn-outline-primary">
                Gerenciar galeria
              </a>
            @endif
          @endauth
        </div>
        <div class="card-body">
          @include('business._gallery', ['business' => ($business ?? ($biz ?? ($item ?? ($company ?? null))))])
        </div>
      </div>

      {{-- Detalhes --}}
      <div class="card">
        <div class="card-header bg-white"><strong>Detalhes</strong></div>
        <div class="card-body">
          <div class="row g-3 small">
            <div class="col-sm-4">
              <div class="text-muted">Categoria</div>
              <div class="fw-medium">{{ $catLabel }}</div>
            </div>
            <div class="col-sm-4">
              <div class="text-muted">Cidade</div>
              <div class="fw-medium">{{ $cityLabel }}</div>
            </div>
            <div class="col-12">
              <div class="text-muted">Descrição</div>
              <div class="mt-1">{!! nl2br(e($biz->description)) !!}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Voltar --}}
      <div class="d-flex gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Voltar</a>
      </div>
    </div>

    {{-- ===== Sidebar (imagem #2 + contatos + compartilhar) ===== --}}
    <aside class="col-lg-4">
      <div class="sticky-top" style="top: 76px;">
        <div class="d-flex flex-column gap-3">

          {{-- Imagem destacada (secundária) --}}
          <div class="card overflow-hidden">
            <div class="ratio ratio-4x3 bg-light">
              <img src="{{ $sideImg }}" alt="{{ $biz->business_name }}" class="img-fluid w-100 h-100" style="object-fit:cover;">
            </div>
          </div>

          @include('partials.ads', ['slot' => 'ad2'])

          {{-- Contatos --}}
          @php
            $addr1   = trim((string)($biz->address_1 ?? ''));
            $addr2   = trim((string)($biz->address_2 ?? ''));
            $phone   = trim((string)($biz->phone ?? ''));
            $website = trim((string)($biz->website ?? ''));
            $email   = trim((string)($biz->email ?? ''));

            $hasContacts = $addr1 || $addr2 || $phone || $website || $email;

            $telHref = preg_replace('/\D+/', '', $phone);
            $websiteHref = $website && !preg_match('/^https?:\/\//i', $website) ? ('http://'.$website) : $website;
          @endphp

          @if ($hasContacts)
            <div class="card">
              <div class="card-header bg-white"><strong>Contatos</strong></div>
              <div class="card-body small">
                @if ($addr1 || $addr2)
                  <div class="mb-2">
                    <div class="fw-semibold text-muted">Endereço</div>
                    <div>{{ $addr1 }}@if($addr1 && $addr2), @endif{{ $addr2 }}</div>
                  </div>
                @endif

                @if ($phone)
                  <div class="mb-2">
                    <div class="fw-semibold text-muted">Telefone</div>
                    <a href="tel:{{ $telHref }}" class="link-underline">{{ $phone }}</a>
                  </div>
                @endif

                @if ($website)
                  <div class="mb-2">
                    <div class="fw-semibold text-muted">Website</div>
                    <a href="{{ $websiteHref }}" target="_blank" rel="nofollow noopener" class="text-break">
                      {{ $website }}
                    </a>
                  </div>
                @endif

                @if ($email)
                  <div class="mb-2">
                    <div class="fw-semibold text-muted">E-mail</div>
                    <a href="mailto:{{ e($email) }}" class="text-break">{{ $email }}</a>
                  </div>
                @endif
              </div>
            </div>
          @endif

          {{-- Compartilhar / Bookmark --}}
          <div class="card">
            <div class="card-header bg-white"><strong>Compartilhar</strong></div>
            <div class="card-body small d-flex flex-wrap gap-2">
              {{-- Facebook --}}
              <a class="btn btn-sm btn-outline-primary"
                 href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                 onclick="window.open(this.href,'fbshare','width=640,height=480'); return false;"
                 aria-label="Compartilhar no Facebook">
                Facebook
              </a>

              {{-- Twitter / X --}}
              <a class="btn btn-sm btn-outline-secondary"
                 href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                 onclick="window.open(this.href,'twshare','width=640,height=480'); return false;"
                 aria-label="Compartilhar no Twitter">
                Twitter
              </a>

              {{-- Pinterest --}}
              <a class="btn btn-sm btn-outline-danger"
                 href="https://pinterest.com/pin/create/button/?url={{ $shareUrl }}&media={{ $shareImg }}&description={{ $shareTitle }}"
                 onclick="window.open(this.href,'pinshare','width=740,height=640'); return false;"
                 aria-label="Compartilhar no Pinterest">
                Pinterest
              </a>

              {{-- Bookmark (usa rota existente se houver) --}}
              @auth
                @if (Route::has('business.bookmark') && !empty($biz->biz_id))
                  <form method="POST" action="{{ route('business.bookmark', $biz->biz_id) }}" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-outline-dark" type="submit" title="Salvar este negócio">
                      Bookmark ({{ $bookmarkCount }})
                    </button>
                  </form>
                @else
                  <span class="btn btn-sm btn-outline-dark disabled" title="Indisponível">
                    Bookmark ({{ $bookmarkCount }})
                  </span>
                @endif
              @else
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-dark" title="Entrar para salvar">
                  Bookmark ({{ $bookmarkCount }})
                </a>
              @endauth
            </div>
          </div>

        </div>
      </div>
    </aside>
  </div>

  {{-- Avaliações + Form + Mapa --}}
  @php
    $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));
    $reviews = collect();
    $avg = 0.0;
    $cnt = 0;
    if ($__biz && method_exists($__biz, 'reviews')) {
      $reviews = $__biz->reviews()->where('is_approved', true)->get();
      $avg = round((float) $reviews->avg('rating'), 1);
      $cnt = $reviews->count();
    }
  @endphp

  @if ($__biz)
    <div class="card">
      <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
        <div>
          <strong>Avaliações</strong>
          <div class="text-muted small">
            Nota média: {{ number_format($avg,1) }} / 5 ({{ $cnt }} {{ $cnt == 1 ? 'avaliação' : 'avaliações' }})
          </div>
        </div>
        <div class="text-nowrap small">
          @for ($i = 1; $i <= 5; $i++)
            @php $full = $i <= floor($avg); @endphp
            <span class="{{ $full ? 'text-warning' : 'text-secondary' }}">★</span>
          @endfor
        </div>
      </div>
      <div class="card-body">

        {{-- Lista de reviews --}}
        @if (View::exists('reviews._list'))
          @include('reviews._list', ['business' => $__biz, 'reviews' => $reviews])
        @else
          @if ($reviews->isEmpty())
            <div class="alert alert-light border small">Ainda não há avaliações aprovadas.</div>
          @else
            <div class="list-group mb-3">
              @foreach ($reviews as $rv)
                @php
                  $body  = trim((string)($rv->body ?? $rv->comment ?? ''));
                  $title = trim((string)($rv->title ?? ''));
                @endphp
                <div class="list-group-item">
                  <div class="d-flex justify-content-between">
                    <strong class="small">Nota: {{ $rv->rating }}/5</strong>
                    <span class="text-muted small">{{ optional($rv->created_at)->format('d/m/Y H:i') ?? '' }}</span>
                  </div>
                  @if($title)<div class="mt-1 fw-semibold small">{{ $title }}</div>@endif
                  @if($body)<div class="small mt-1">{{ $body }}</div>@endif
                </div>
              @endforeach
            </div>
          @endif
        @endif

        <hr class="my-4">

        {{-- Form de review --}}
        @if (View::exists('reviews._form'))
          @include('reviews._form', ['business' => $__biz])
        @else
          <div class="alert alert-info small mb-0">Formulário de avaliação indisponível.</div>
        @endif

        <hr class="my-4">

        {{-- Mapa --}}
        @if (View::exists('business._map'))
          @include('business._map', ['business' => $__biz])
        @else
          <div class="alert alert-light border small mb-0">Mapa indisponível.</div>
        @endif
      </div>
    </div>
  @endif
</div>

@include('partials.ads', ['slot' => 'ad3'])

@endsection
