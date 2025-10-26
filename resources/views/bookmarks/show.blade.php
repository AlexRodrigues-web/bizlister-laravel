{{-- resources/views/bookmarks/show.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  use Illuminate\Support\Str;

  $row   = $bookmark ?? null;
  $pk    = $row->bookmark_pk ?? $row->id ?? $row->bookmark_id ?? $row->bm_id ?? null;
  $bizId = $row->biz_id ?? $row->bm_biz_id ?? $row->business_id ?? $row->bizid ?? null;
  $name  = trim($row->business_name ?? $row->name ?? 'Negócio');
  $city  = $row->city ?? null;

  $imgRaw = $row->image_lg ?? $row->image ?? null;
  $imgUrl = $imgRaw ? (Str::startsWith($imgRaw, ['http://','https://','/']) ? $imgRaw : asset($imgRaw)) : null;

  $slug  = Str::slug($name);
  $bizUrl = $bizId ? route('business.show', ['id' => $bizId, 'slug' => $slug]) : '#';
@endphp

{{-- HERO --}}
<section class="relative overflow-hidden border-b border-slate-200">
  <div class="absolute inset-0 -z-10 bg-gradient-to-r from-[#c41210] via-[#c41210]/90 to-[#c41210]/80 opacity-95"></div>
  <div class="mx-auto max-w-5xl px-4 py-8 text-white">
    <h1 class="text-2xl md:text-3xl font-bold leading-tight">Favorito</h1>
    <p class="mt-1 text-white/80 text-sm">Detalhes do negócio salvo.</p>
  </div>
</section>

<div class="mx-auto max-w-5xl px-4 py-8">
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="flex flex-col gap-4 p-6 md:flex-row md:items-start">
      <div class="h-28 w-28 overflow-hidden rounded-xl bg-slate-100 shrink-0">
        @if ($imgUrl)
          <img src="{{ $imgUrl }}" alt="" class="h-full w-full object-cover">
        @else
          <div class="grid h-full w-full place-items-center text-slate-400 text-2xl">🏷️</div>
        @endif
      </div>

      <div class="min-w-0">
        <h2 class="text-xl font-semibold text-slate-900">{{ $name }}</h2>
        @if($city)
          <div class="mt-0.5 text-sm text-slate-500">{{ $city }}</div>
        @endif

        <div class="mt-4 flex flex-wrap items-center gap-2">
          @if($bizId)
            <a href="{{ $bizUrl }}"
               class="inline-flex items-center gap-2 rounded-lg bg-[#c41210] px-4 py-2 text-sm font-medium text-white hover:brightness-110">
              Ver negócio
            </a>
          @endif

          @if($pk !== null)
            <form method="POST" action="{{ route('bookmarks.destroy', $pk) }}"
                  onsubmit="return confirm('Remover dos favoritos?')">
              @csrf
              @method('DELETE')
              <button class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-100">
                Remover favorito
              </button>
            </form>
          @endif

          <a href="{{ route('bookmarks.index') }}"
             class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            ← Voltar à lista
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
