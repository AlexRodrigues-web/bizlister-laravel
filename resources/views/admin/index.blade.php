@extends('layouts.app')

@section('title','Admin')

@section('content')
<div class="max-w-5xl mx-auto">
  <h1 class="text-2xl font-semibold mb-6">Admin</h1>

  @php
    $cityCount = \App\Models\City::count();
    $catCount  = \Illuminate\Support\Facades\DB::table('category')->count();
    $bizCount  = \App\Models\Business::count();
    $userCount = \App\Models\User::count();
  @endphp

  <div class="grid md:grid-cols-2 gap-4">
    <a href="{{ url('/cidades') }}" class="block border rounded p-4 hover:bg-gray-50">
      <div class="text-sm text-gray-500">Cidades</div>
      <div class="text-2xl font-semibold">{{ $cityCount }}</div>
    </a>
    <a href="{{ url('/categorias') }}" class="block border rounded p-4 hover:bg-gray-50">
      <div class="text-sm text-gray-500">Categorias</div>
      <div class="text-2xl font-semibold">{{ $catCount }}</div>
    </a>
    <div class="block border rounded p-4">
      <div class="text-sm text-gray-500">NegÃƒÂ³cios</div>
      <div class="text-2xl font-semibold">{{ $bizCount }}</div>
    </div>
    <div class="block border rounded p-4">
      <div class="text-sm text-gray-500">UsuÃƒÂ¡rios</div>
      <div class="text-2xl font-semibold">{{ $userCount }}</div>
    </div>
  </div>

  <div class="mt-6 grid md:grid-cols-2 gap-4">
    <a href="{{ route('business.create') }}" class="block border rounded p-4 text-center bg-blue-600 text-white hover:bg-blue-700">
      Cadastrar Negócio</a>
    <a href="{{ route('search.index') }}" class="block border rounded p-4 text-center hover:bg-gray-50">
      Buscar NegÃƒÂ³cios
    </a>
  </div>
</div>
@endsection