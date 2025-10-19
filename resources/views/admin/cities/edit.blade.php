@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Editar Cidade #{{ $city->city_id ?? $city->id }}</h1>

  @if (session('error'))
    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  <form method="post" action="{{ route('admin.cities.update', ['city' => $city->city_id ?? $city->id]) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
      <label class="block text-sm mb-1 font-medium">Nome</label>
      <input name="city"
             value="{{ old('city', $city->city ?? $city->name ?? '') }}"
             class="border rounded px-3 py-2 w-full @error('city') border-red-500 @enderror">
      @error('city')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm mb-1 font-medium">UF</label>
      <input name="uf"
             value="{{ old('uf', $city->uf ?? '') }}"
             maxlength="2"
             class="border rounded px-3 py-2 w-24 text-center uppercase tracking-widest @error('uf') border-red-500 @enderror"
             oninput="this.value=this.value.toUpperCase()">
      @error('uf')<div class="text-red-600 text-sm mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.cities.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">Salvar</button>
    </div>
  </form>
</div>
@endsection
