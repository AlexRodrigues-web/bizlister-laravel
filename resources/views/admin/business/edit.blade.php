@extends('layouts.admin')

@section('content')
@include('admin._back_public')

@php
  // Normalizações leves para título/breadcrumb
  $bizId   = $item->biz_id ?? $item->id ?? null;
  $bizName = trim((string)($item->business_name ?? ''));
@endphp

<div class="mx-auto max-w-3xl p-6">

  {{-- Breadcrumbs / Ações rápidas --}}
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <a href="{{ route('admin.businesses.index') }}" class="hover:underline">Negócios</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Editar</span>
    </nav>

    <a href="{{ route('admin.businesses.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
      ← Voltar
    </a>
  </div>

  {{-- Cabeçalho --}}
  <div class="mb-5">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
      Editar Negócio {{ $bizName ? '— '.$bizName : '' }}
    </h1>
    @if($bizId)
      <div class="mt-1 text-xs text-slate-500">
        ID interno: <span class="inline-flex items-center rounded-full border border-slate-200 px-2 py-0.5">#{{ $bizId }}</span>
      </div>
    @endif
  </div>

  {{-- Feedback (flash) --}}
  @if(session('success') || session('status'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  {{-- Sumário de erros --}}
  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">
      <div class="font-semibold mb-1">Corrija os erros abaixo:</div>
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Card do formulário --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST"
          action="{{ route('admin.businesses.update', $bizId) }}"
          class="space-y-6">
      @csrf
      @method('PUT')

      {{-- Nome --}}
      <div>
        <label for="business_name" class="block text-sm font-medium text-slate-700">Nome</label>
        <input
          id="business_name"
          type="text"
          name="business_name"
          class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          value="{{ old('business_name', $item->business_name) }}"
          required
        >
        @error('business_name')
          <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
        @enderror
      </div>

      {{-- Descrição --}}
      <div>
        <label for="description" class="block text-sm font-medium text-slate-700">Descrição</label>
        <textarea
          id="description"
          name="description"
          rows="4"
          class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
        >{{ old('description', $item->description) }}</textarea>
        @error('description')
          <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
        @enderror
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Categoria (cid) --}}
        <div>
          <label for="cid" class="block text-sm font-medium text-slate-700">Categoria</label>
          <select
            id="cid"
            name="cid"
            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          >
            <option value="">-- Selecione --</option>
            @foreach($categories as $c)
              @php
                $catId = $c->cat_id ?? $c->id ?? null;
                $catLabel =
                    ($c->category ?? null)
                    ?? ($c->cat_name ?? null)
                    ?? ($c->name ?? null)
                    ?? ($catId ? 'Categoria #'.$catId : 'Categoria');
                $selected = (string) old('cid', $item->cid ?? '') === (string) $catId ? 'selected' : '';
              @endphp
              <option value="{{ $catId }}" {{ $selected }}>{{ $catLabel }}</option>
            @endforeach
          </select>
          @error('cid')
            <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
          @enderror
        </div>

        {{-- Cidade (city_id) --}}
        <div>
          <label for="city_id" class="block text-sm font-medium text-slate-700">Cidade</label>
          <select
            id="city_id"
            name="city_id"
            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          >
            <option value="">-- Selecione --</option>
            @foreach($cities as $c)
              @php
                $cid = $c->city_id ?? $c->id ?? null;
                // Prioriza coluna legada 'city'; fallback para 'name'/'label'
                $cname = (is_string($c->city ?? null) && trim($c->city) !== '')
                          ? $c->city
                          : ((is_string($c->name ?? null) && trim($c->name) !== '')
                              ? $c->name
                              : ((is_string($c->label ?? null) && trim($c->label) !== '') ? $c->label : 'Cidade #'.$cid));
                $sel = (string) old('city_id', $item->city_id ?? $item->sid ?? '') === (string) $cid ? 'selected' : '';
              @endphp
              <option value="{{ $cid }}" {{ $sel }}>{{ $cname }}</option>
            @endforeach
          </select>
          @error('city_id')
            <p class="mt-1 text-red-600 text-sm">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          Salvar
        </button>
        <a href="{{ route('admin.businesses.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
