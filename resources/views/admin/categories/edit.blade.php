@extends('layouts.admin')

@section('content')
@include('admin._back_public')

@php
  // Normaliza o registro vindo do controller: $row OU $category OU $cat
  $row = $row ?? $category ?? $cat ?? null;

  // Tenta resolver as colunas de nome comuns: category / cat_name / name
  $nameValue = old('category',
                old('cat_name',
                  old('name',
                    $row->category
                      ?? ($row->cat_name ?? ($row->name ?? ''))
                  )));
@endphp

<div class="max-w-lg mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">
    Editar Categoria @if($row && isset($row->cat_id)) #{{ $row->cat_id }} @endif
  </h1>

  <form method="post"
        action="{{ route('admin.categories.update', ['category' => $row->cat_id ?? ($row->id ?? request()->route('category'))]) }}"
        class="space-y-4">
    @csrf
    @method('PUT')

    <div>
      <label class="block text-sm mb-1">Nome</label>
      <input name="category"
             value="{{ $nameValue }}"
             class="border rounded px-3 py-2 w-full">
      @error('category')
        <div class="text-red-600 text-sm">{{ $message }}</div>
      @enderror
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 border rounded bg-gray-100">Salvar</button>
    </div>
  </form>
</div>
@endsection
