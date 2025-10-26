@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-3xl p-6">
  {{-- Cabeçalho / breadcrumbs --}}
  <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <a href="{{ route('admin.cities.index') }}" class="hover:underline">Cidades</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Nova</span>
    </nav>

    <a href="{{ route('admin.cities.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
      ← Voltar
    </a>
  </div>

  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-4">Nova Cidade</h1>

  {{-- Flash / erros de topo --}}
  @if (session('success') || session('status'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">
      <div class="font-semibold mb-1">Corrija os campos abaixo:</div>
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Card do formulário --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.cities.store') }}" class="space-y-6">
      @csrf

      {{-- Nome da cidade (salvo em "city") --}}
      <div>
        <label for="fld-city" class="block text-sm font-medium text-slate-700">Nome</label>
        <input
          id="fld-city"
          name="city"
          type="text"
          value="{{ old('city') }}"
          placeholder="Ex.: São Paulo"
          class="mt-1 block w-full rounded-xl border @error('city') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          required
        >
        @error('city')
          <div class="mt-1 text-red-600 text-sm">{{ $message }}</div>
        @enderror
      </div>

      {{-- UF opcional --}}
      <div class="max-w-xs">
        <label for="fld-uf" class="block text-sm font-medium text-slate-700">UF</label>
        <input
          id="fld-uf"
          name="uf"
          value="{{ old('uf') }}"
          maxlength="2"
          class="mt-1 block w-24 text-center uppercase tracking-widest rounded-xl border @error('uf') border-red-500 @else border-slate-300 @enderror px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          oninput="this.value=this.value.toUpperCase()"
          placeholder="SP"
        >
        @error('uf')
          <div class="mt-1 text-red-600 text-sm">{{ $message }}</div>
        @enderror
      </div>

      {{-- Ativa? (opcional, compatível com controller) --}}
      <div class="flex items-center gap-2">
        <input
          id="fld-active"
          name="is_active"
          type="checkbox"
          value="1"
          class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
          {{ old('is_active', true) ? 'checked' : '' }}
        >
        <label for="fld-active" class="text-sm text-slate-700">Ativa</label>
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button
          type="submit"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          Salvar
        </button>

        <a href="{{ route('admin.cities.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
