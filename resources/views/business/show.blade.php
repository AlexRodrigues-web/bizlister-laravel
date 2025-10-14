@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

  {{-- Flash: success ou status (unificado) --}}
  @if (session('success') || session('status'))
    <div class="mb-4">
      <div class="rounded-lg bg-green-50 border border-green-200 p-3 text-green-800">
        {{ session('success') ?? session('status') }}
      </div>
    </div>
  @endif

  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-4">
    {{ $biz->business_name }}
  </h1>

  {{-- Imagem com fallback (verifica existência no storage) --}}
  @php
      $imgPath = null;
      if (!empty($biz->image_lg) && \Illuminate\Support\Facades\Storage::disk('public')->exists($biz->image_lg)) {
          $imgPath = 'storage/'.$biz->image_lg;
      } elseif (!empty($biz->image) && \Illuminate\Support\Facades\Storage::disk('public')->exists($biz->image)) {
          $imgPath = 'storage/'.$biz->image;
      } else {
          $imgPath = 'images/placeholder-800x600.png';
      }
  @endphp
  <img
    src="{{ asset($imgPath) }}"
    alt="{{ $biz->business_name }}"
    class="mb-4 rounded-lg w-full h-auto object-cover max-h-96">

  @php
    // Normaliza labels vindo de stdClass, string ou ausentes
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

  <dl class="space-y-2">
    <div>
      <dt class="font-semibold">Categoria:</dt>
      <dd>{{ $catLabel }}</dd>
    </div>
    <div>
      <dt class="font-semibold">Cidade:</dt>
      <dd>{{ $cityLabel }}</dd>
    </div>
    <div>
      <dt class="font-semibold">Descrição:</dt>
      <dd>{!! nl2br(e($biz->description)) !!}</dd>
    </div>
  </dl>

  <div class="mt-6">
    <a href="{{ url()->previous() }}"
       class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">
      Voltar
    </a>
  </div>
</div>
@endsection
