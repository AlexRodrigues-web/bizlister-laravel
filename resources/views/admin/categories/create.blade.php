@extends('layouts.admin')

@section('title', 'Nova Categoria')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-3xl p-6">

  {{-- Breadcrumbs / Ações rápidas --}}
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500" aria-label="breadcrumb">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <a href="{{ route('admin.categories.index') }}" class="hover:underline">Categorias</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Nova</span>
    </nav>

    <a href="{{ route('admin.categories.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
      <span aria-hidden="true">←</span> Voltar
    </a>
  </div>

  {{-- Cabeçalho --}}
  <header class="mb-5">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
      Nova Categoria
    </h1>
    <p class="mt-1 text-sm text-slate-500">Crie uma nova categoria para organizar os negócios.</p>
  </header>

  {{-- Flash messages --}}
  @if (session('success') || session('status'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  {{-- Resumo de erros --}}
  @if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">
      <div class="font-semibold mb-1">Corrija os erros abaixo:</div>
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Card / Formulário --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-6" novalidate>
      @csrf

      <div>
        <label for="category" class="block text-sm font-medium text-slate-700">Nome</label>
        <input
          id="category"
          name="category"
          type="text"
          value="{{ old('category') }}"
          placeholder="Ex.: Restaurantes"
          class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          required
          autocomplete="off"
        >
        <p class="mt-1 text-xs text-slate-500">Este é o nome que aparecerá no site.</p>
        @error('category')
          <div class="mt-1 text-red-600 text-sm">{{ $message }}</div>
        @enderror
      </div>

      <div class="pt-2 flex items-center gap-3">
        <button
          type="submit"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          Salvar
        </button>

        <a href="{{ route('admin.categories.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
