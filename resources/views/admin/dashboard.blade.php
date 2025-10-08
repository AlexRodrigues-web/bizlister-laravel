@extends("layouts.app")

@section("content")
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">Admin</h1>

  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <a href="{{ route('cities.index') }}" class="block rounded-xl border border-slate-200 bg-white p-5 hover:shadow">
      <div class="text-sm text-slate-500">Cidades</div>
      <div class="mt-1 text-3xl font-semibold">{{ $totCities }}</div>
    </a>

    <a href="{{ route('categories.index') }}" class="block rounded-xl border border-slate-200 bg-white p-5 hover:shadow">
      <div class="text-sm text-slate-500">Categorias</div>
      <div class="mt-1 text-3xl font-semibold">{{ $totCategories }}</div>
    </a>

    <a href="{{ route('search.index') }}" class="block rounded-xl border border-slate-200 bg-white p-5 hover:shadow">
      <div class="text-sm text-slate-500">Negócios</div>
      <div class="mt-1 text-3xl font-semibold">{{ $totBusinesses }}</div>
    </a>

    <div class="rounded-xl border border-slate-200 bg-white p-5">
      <div class="text-sm text-slate-500">Usuários</div>
      <div class="mt-1 text-3xl font-semibold">{{ $totUsers }}</div>
    </div>
  </div>

  <div class="mt-8 flex gap-3">
    <a href="{{ route('business.create') }}" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">
      Cadastrar Negócio</a>
    <a href="{{ route('search.index') }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">
      Buscar Negócios
    </a>
  </div>
</div>
@endsection