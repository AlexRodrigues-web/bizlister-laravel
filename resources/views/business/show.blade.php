@extends('layouts.app')

@section('content')
<div class="mx-auto w-full max-w-5xl px-4 py-8">

  {{-- STATUS / FLASH --}}
  @if (session('success') || session('status'))
    <div class="mb-6">
      <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800">
        {{ session('success') ?? session('status') }}
      </div>
    </div>
  @endif

  {{-- CABEÇALHO --}}
  <header class="mb-6">
    <h1 class="text-3xl font-bold tracking-tight text-slate-900">
      {{ $biz->business_name }}
    </h1>
  </header>

  {{-- HERO / INTRO: ABOUT + GALLERY (SEUS PARCIAIS) --}}
  <section class="mb-6 grid gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @include('business._about',   ['business' => ($business ?? ($biz ?? ($item ?? ($company ?? null))))])
      </div>
      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @include('business._gallery', ['business' => ($business ?? ($biz ?? ($item ?? ($company ?? null))))])
      </div>
    </div>

    {{-- CARD LATERAL: IMAGEM DESTACADA + CONTATOS (sem remover nada seu) --}}
    <aside class="space-y-6">
      {{-- IMAGEM DESTACADA (SEU BLOCO) --}}
      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        @php
            use Illuminate\Support\Facades\Storage;
            $imgPath = null;
            if (!empty($biz->image_lg) && Storage::disk('public')->exists($biz->image_lg)) {
                $imgPath = 'storage/'.$biz->image_lg;
            } elseif (!empty($biz->image) && Storage::disk('public')->exists($biz->image)) {
                $imgPath = 'storage/'.$biz->image;
            } else {
                $imgPath = 'images/placeholder-800x600.png';
            }
        @endphp

        <div class="aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-100">
          <img src="{{ asset($imgPath) }}" alt="{{ $biz->business_name }}" class="h-full w-full object-cover">
        </div>
      </div>

      {{-- CONTATOS (SEU BLOCO) --}}
      @php
          $addr1   = trim((string)($biz->address_1 ?? ''));
          $addr2   = trim((string)($biz->address_2 ?? ''));
          $phone   = trim((string)($biz->phone ?? ''));
          $website = trim((string)($biz->website ?? ''));
          $email   = trim((string)($biz->email ?? ''));

          $hasContacts = $addr1 || $addr2 || $phone || $website || $email;

          $telHref = preg_replace('/\D+/', '', $phone);
          $websiteHref = $website;
          if ($website && !preg_match('/^https?:\/\//i', $websiteHref)) {
              $websiteHref = 'http://' . $websiteHref;
          }
      @endphp

      @if ($hasContacts)
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
          <h2 class="mb-3 text-lg font-semibold text-slate-900">Contatos</h2>
          <div class="space-y-3 text-sm leading-6 text-slate-700">
            @if ($addr1 || $addr2)
              <div>
                <div class="font-semibold text-slate-800">Endereço</div>
                <div>{{ $addr1 }}@if($addr1 && $addr2), @endif{{ $addr2 }}</div>
              </div>
            @endif
            @if ($phone)
              <div>
                <div class="font-semibold text-slate-800">Telefone</div>
                <a href="tel:{{ $telHref }}" class="underline">{{ $phone }}</a>
              </div>
            @endif
            @if ($website)
              <div>
                <div class="font-semibold text-slate-800">Website</div>
                <a href="{{ $websiteHref }}" target="_blank" rel="nofollow noopener" class="underline break-all">
                  {{ $website }}
                </a>
              </div>
            @endif
            @if ($email)
              <div>
                <div class="font-semibold text-slate-800">E-mail</div>
                <a href="mailto:{{ e($email) }}" class="underline break-all">{{ $email }}</a>
              </div>
            @endif
          </div>
        </div>
      @endif
    </aside>
  </section>

  {{-- DETALHES (Categoria/Cidade/Descrição) — SEU BLOCO EM CARD PADRÃO --}}
  @php
    $catLabel = isset($category)
      ? (is_object($category)
          ? ($category->category ?? $category->cat_name ?? $category->label ?? (string)($biz->cid))
          : (string)$category)
      : (string)($biz->cid);

    $cityLabel = isset($city)
      ? (is_object($city)
          ? ($city->city ?? $city->label ?? (string)($biz->city))
          : (string)$city)
      : (string)($biz->city);
  @endphp

  <section class="mb-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <h2 class="mb-3 text-lg font-semibold text-slate-900">Detalhes</h2>
    <dl class="grid gap-4 sm:grid-cols-3 text-slate-800">
      <div>
        <dt class="text-sm text-slate-500">Categoria</dt>
        <dd class="font-medium">{{ $catLabel }}</dd>
      </div>
      <div>
        <dt class="text-sm text-slate-500">Cidade</dt>
        <dd class="font-medium">{{ $cityLabel }}</dd>
      </div>
      <div class="sm:col-span-3">
        <dt class="text-sm text-slate-500">Descrição</dt>
        <dd class="mt-1 whitespace-pre-line leading-relaxed">{!! nl2br(e($biz->description)) !!}</dd>
      </div>
    </dl>
  </section>

  {{-- CTA VOLTAR --}}
  <div class="mb-8">
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-slate-800 shadow-sm hover:bg-slate-50">
      Voltar
    </a>
  </div>

  {{-- REVIEWS + FORM + MAPA (SEU BLOCO NOVO) --}}
  @php
    $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));
  @endphp

  @if($__biz)
    @php
      $avg = round($__biz->reviews()->where('is_approved', true)->avg('rating') ?: 0, 1);
      $cnt = $__biz->reviews()->where('is_approved', true)->count();
    @endphp

    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
      <header class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="text-xl font-semibold text-slate-900">Avaliações</h2>
          <p class="text-sm text-slate-500">Nota média: {{ $avg }} / 5 ({{ $cnt }} {{ $cnt == 1 ? 'avaliação' : 'avaliações' }})</p>
        </div>
        <div class="shrink-0"><x-stars :value="$avg" size="lg" /></div>
      </header>

      @include('reviews._list', ['business' => $__biz])

      <div class="my-6 border-t border-slate-200"></div>

      @include('reviews._form', ['business' => $__biz])

      <div class="my-6 border-t border-slate-200"></div>

      @include('business._map', ['business' => $__biz])
    </section>
  @endif
</div>
@endsection
