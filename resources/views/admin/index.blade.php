@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="max-w-5xl mx-auto p-6">
  @if (session('success'))
    <div class="p-3 mb-4 rounded bg-green-100 text-green-800">
      {{ session('success') }}
    </div>
  @endif

  <h1 class="text-2xl font-bold mb-6">Painel Administrativo</h1>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <a href="{{ route('admin.categories.index') }}" class="block p-6 rounded-lg shadow hover:shadow-md border">
      <div class="text-sm text-gray-500">Gerenciar</div>
      <div class="text-xl font-semibold">Categorias</div>
      <div class="text-gray-600 mt-2">Total: {{ $totals['categories'] ?? 0 }}</div>
    </a>
    <a href="{{ route('admin.cities.index') }}" class="block p-6 rounded-lg shadow hover:shadow-md border">
      <div class="text-sm text-gray-500">Gerenciar</div>
      <div class="text-xl font-semibold">Cidades</div>
      <div class="text-gray-600 mt-2">Total: {{ $totals['cities'] ?? 0 }}</div>
    </a>
    <a href="{{ route('admin.businesses.index') }}" class="block p-6 rounded-lg shadow hover:shadow-md border">
      <div class="text-sm text-gray-500">Gerenciar</div>
      <div class="text-xl font-semibold">NegÃƒÂ³cios</div>
      <div class="text-gray-600 mt-2">Total: {{ $totals['business'] ?? 0 }}</div>
    </a>
  </div>

  <div class="p-4 rounded border">
    <div class="text-sm text-gray-500">UsuÃƒÂ¡rios cadastrados</div>
    <div class="text-xl font-semibold">{{ $totals['users'] ?? 0 }}</div>
  </div>
</div>
@endsection