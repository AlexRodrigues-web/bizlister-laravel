@extends("layouts.app")

@section("content")
<div class="max-w-6xl mx-auto px-4 py-8">
  @php
    $catName = ($category->category ?? $category->cat_name ?? "Categoria");
  @endphp
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">
    {{ $catName }}
  </h1>

  @if(isset($businesses) && $businesses->count())
    <p class="text-sm text-slate-600 mb-4">
      Resultados: <span class="font-semibold">{{ $businesses->total() }}</span>
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($businesses as $biz)
        <x-biz-card :biz="$biz" />
      @endforeach
    </div>

    <div class="mt-6">
      {{ $businesses->withQueryString()->links() }}
    </div>
  @else
    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">
      Nenhum NegóciosÂ³cio encontrado para esta categoria.
    </div>
  @endif

  <div class="mt-8">
    <a href="{{ route('categories.index') }}" class="text-indigo-600 hover:text-indigo-700">&larr; Voltar para categorias</a>
  </div>
</div>
@endsection