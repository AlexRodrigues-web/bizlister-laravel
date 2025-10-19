@extends('layouts.app')

@section('content')
{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">
    {{ 'Negócios em ' . ($city->city ?? '') }}
  </h1>

  @php
    $filters    = $filters ?? [];
    $categories = $categories ?? [];
    $qValue     = $q ?? ($filters['q'] ?? '');
    $catId      = $catId ?? ($filters['categoria'] ?? null);
  @endphp

  {{-- Filtros --}}
  <form method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-3 mb-6">
    <div class="md:col-span-6">
      <label class="block text-sm font-medium text-slate-700 mb-1" for="q">Buscar</label>
      <input
        id="q"
        type="text"
        name="q"
        value="{{ $qValue }}"
        class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500"
        placeholder="Buscar por nome ou descrição...">
    </div>

    <div class="md:col-span-4">
      <label class="block text-sm font-medium text-slate-700 mb-1" for="categoria">Categoria</label>
      <select id="categoria" name="categoria"
              class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 focus:border-indigo-500">
        <option value="">Todas</option>
        @foreach($categories as $c)
          <option value="{{ $c->cat_id }}" @selected($catId == $c->cat_id)>
            {{ $c->category ?? $c->cat_name ?? $c->label ?? '' }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2 flex items-end">
      <button class="inline-flex justify-center w-full md:w-auto rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">
        Filtrar
      </button>
    </div>
  </form>

  {{-- Resumo --}}
  <p class="text-sm text-slate-600 mb-4">
    Resultados:
    <span class="font-semibold">
      {{ method_exists($businesses ?? null, 'total') ? $businesses->total() : (($businesses ?? collect())->count()) }}
    </span>
    em <span class="font-semibold">{{ $city->city ?? '' }}</span>
    @if(filled($qValue)) • termo: “{{ $qValue }}” @endif
    @if(filled($catId))  • categoria: #{{ $catId }} @endif
  </p>

  {{-- Lista --}}
  @if(($businesses ?? collect())->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($businesses as $biz)
        <x-biz-card :biz="$biz" />
      @endforeach
    </div>

    @if(method_exists($businesses, 'links'))
      <div class="mt-6">
        {{ $businesses->withQueryString()->links() }}
      </div>
    @endif
  @else
    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">
      Nenhum negócio encontrado com os filtros aplicados.
    </div>
  @endif

  <div class="mt-8">
    <a href="{{ route('cities.index') }}" class="text-indigo-600 hover:text-indigo-700">&larr; Voltar para cidades</a>
  </div>
</div>
@endsection
