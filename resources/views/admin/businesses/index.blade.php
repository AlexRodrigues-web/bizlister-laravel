@extends('layouts.admin')
@section('content')
@include('admin._back_public')

@php
  // Helpers de rótulo (compatíveis com colunas legadas)
  $catLabel = function ($c) {
    $id = $c->cat_id ?? $c->id ?? null;
    $nm = $c->category ?? $c->cat_name ?? $c->name ?? $c->title ?? null;
    return (is_string($nm) && trim($nm) !== '') ? trim($nm) : ($id ? 'Categoria #'.$id : 'Categoria');
  };

  $cityLabel = function ($ci) {
    $id = $ci->city_id ?? $ci->sid ?? $ci->id ?? null;
    // Prioriza coluna legada 'city'; depois name/label/title
    $nm = $ci->city ?? $ci->name ?? $ci->city_name ?? $ci->label ?? $ci->title ?? null;
    return (is_string($nm) && trim($nm) !== '') ? trim($nm) : ($id ? 'Cidade #'.$id : 'Cidade');
  };
@endphp

<div class="mx-auto max-w-6xl p-6">

  {{-- Cabeçalho + Ações rápidas --}}
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Negócios</span>
    </nav>

    <a href="{{ route('admin.businesses.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-700">
      + Novo Negócio
    </a>
  </div>

  {{-- Flash --}}
  @if(session('success'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-green-800" role="alert">
      {{ session('success') }}
    </div>
  @endif
  @if(session('status'))
    <div class="mb-4 rounded-xl border border-blue-200 bg-blue-50 p-3 text-blue-800" role="alert">
      {{ session('status') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800" role="alert">
      {{ session('error') }}
    </div>
  @endif

  {{-- Filtros --}}
  <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <form method="get" class="grid grid-cols-1 gap-3 md:grid-cols-4">
      <div>
        <label for="q" class="block text-xs font-medium text-slate-600 mb-1">Termo</label>
        <input id="q" name="q" value="{{ $q }}" placeholder="Buscar por nome, descrição…"
               class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
      </div>

      <div>
        <label for="cid" class="block text-xs font-medium text-slate-600 mb-1">Categoria</label>
        <select id="cid" name="cid"
                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
          <option value="">Todas categorias</option>
          @foreach($cats as $c)
            <option value="{{ $c->cat_id }}" {{ (string)$cid === (string)$c->cat_id ? 'selected' : '' }}>
              {{ $catLabel($c) }}
            </option>
          @endforeach
        </select>
      </div>

      <div>
        <label for="sid" class="block text-xs font-medium text-slate-600 mb-1">Cidade</label>
        <select id="sid" name="sid"
                class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
          <option value="">Todas cidades</option>
          @foreach($cities as $c)
            <option value="{{ $c->city_id }}" {{ (string)$sid === (string)$c->city_id ? 'selected' : '' }}>
              {{ $cityLabel($c) }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="flex items-end">
        <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
          Filtrar
        </button>
      </div>
    </form>
  </div>

  {{-- Tabela --}}
  <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full">
      <thead class="bg-slate-50">
        <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
          <th class="px-4 py-3">ID</th>
          <th class="px-4 py-3">Nome</th>
          <th class="px-4 py-3">Categoria</th>
          <th class="px-4 py-3">Cidade</th>
          <th class="px-4 py-3 text-right">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 text-sm">
        @forelse($rows as $r)
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 text-slate-700">{{ $r->biz_id }}</td>
            <td class="px-4 py-3 font-medium text-slate-900">{{ $r->business_name }}</td>
            <td class="px-4 py-3 text-slate-700">{{ $r->category_name }}</td>
            <td class="px-4 py-3 text-slate-700">{{ $r->city_name }}</td>
            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                <a class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-700"
                   href="{{ route('admin.businesses.edit', $r->biz_id) }}">
                  Editar
                </a>
                <form action="{{ route('admin.businesses.destroy', $r->biz_id) }}" method="POST"
                      onsubmit="return confirm('Remover este negócio?')">
                  @csrf @method('DELETE')
                  <button class="inline-flex items-center rounded-lg border border-red-300 bg-white px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">
                    Remover
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6 text-center text-slate-500">Nenhum registro.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginação --}}
  <div class="mt-4">
    {{ $rows->withQueryString()->links() }}
  </div>
</div>
@endsection
