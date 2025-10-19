{{-- PUBLIC_UI_V2_MARK --}}
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
  <header class="mb-6 flex items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">Categorias</h1>
      <p class="text-slate-600 text-sm">Navegue por todas as categorias do BizLister.</p>
    </div>
    <form method="get" action="{{ route('categories.index') }}" class="hidden md:block">
      <input name="q" value="{{ request('q') }}" placeholder="Buscar..."
             class="rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none">
    </form>
  </header>

  @php
    $items = $categories ?? $cats ?? collect();
  @endphp

  @if($items->count() === 0)
    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">Nenhuma categoria encontrada.</div>
  @else
    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
      @foreach($items as $c)
        @php
          $id   = $c->cat_id   ?? $c->id   ?? null;
          $name = $c->category ?? $c->cat_name ?? $c->name ?? $c->label ?? ('Categoria #'.$id);
          $slug = \Illuminate\Support\Str::slug($name ?? 'categoria');
          $url  = $id ? route('categories.show', [$id, $slug]) : '#';
        @endphp
        <a href="{{ $url }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition">
          <div class="flex items-center justify-between">
            <h2 class="font-semibold text-slate-800 group-hover:text-slate-900">{{ $name }}</h2>
            <span class="text-xs text-slate-500">Ver</span>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection