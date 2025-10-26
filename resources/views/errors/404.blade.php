@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12">
  <h1 class="mb-3 text-3xl font-bold text-slate-800">Página não encontrada (404)</h1>
  <p class="mb-6 text-slate-600">
    O endereço pode ter mudado. Use a busca, acesse as páginas principais ou confira alguns negócios recentes.
  </p>

  {{-- Busca rápida --}}
  @if (Route::has('search.index'))
    <form action="{{ route('search.index') }}" method="GET" class="mb-8" role="search" aria-label="Busca no site">
      <label for="q" class="sr-only">Buscar</label>
      <div class="flex gap-2">
        <input
          id="q"
          name="q"
          type="search"
          value="{{ request('q') }}"
          placeholder="Busque por nome, categoria, cidade…"
          class="flex-1 rounded-lg border border-slate-300 bg-white px-4 py-2 text-slate-800 placeholder:text-slate-400 shadow-sm outline-none ring-0 transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
          autocomplete="off"
          spellcheck="false"
        >
        <button
          type="submit"
          class="rounded-lg bg-indigo-600 px-5 py-2 text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
        >
          Buscar
        </button>
      </div>
    </form>
  @endif

  {{-- Links úteis --}}
  <div class="mb-10">
    <h2 class="mb-3 text-xl font-semibold text-slate-800">Acesso rápido</h2>
    <nav aria-label="Links úteis" class="flex flex-wrap gap-3">
      <a href="{{ url('/') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Início
      </a>

      @if (Route::has('categories.index'))
        <a href="{{ route('categories.index') }}"
           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Categorias
        </a>
      @endif

      @if (Route::has('cities.index'))
        <a href="{{ route('cities.index') }}"
           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Cidades
        </a>
      @endif

      @if (Route::has('business.create'))
        <a href="{{ route('business.create') }}"
           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
          Cadastrar um negócio
        </a>
      @endif

      {{-- Páginas estáticas --}}
      <a href="{{ url('/sobre-nos') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Sobre Nós
      </a>
      <a href="{{ url('/termos-de-uso') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" rel="nofollow">
        Termos de Uso
      </a>
      <a href="{{ url('/politica-de-privacidade') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" rel="nofollow">
        Política de Privacidade
      </a>

      {{-- Contato & Sitemap --}}
      <a href="{{ url('/contato') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Contato
      </a>
      <a href="{{ url('/sitemap.xml') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 transition hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" rel="nofollow">
        Sitemap
      </a>

      {{-- Voltar --}}
      <button type="button"
              onclick="history.back()"
              class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        Voltar
      </button>
    </nav>
  </div>

  {{-- Sugerir negócios recentes --}}
  @php
    // $latest vem do fallback; garante boolean coerente sem warnings
    $showLatest = !empty($latest) && is_iterable($latest) && count($latest);
  @endphp

  @if ($showLatest)
    <h2 class="mb-3 text-xl font-semibold text-slate-800">Talvez você esteja procurando:</h2>
    <ul class="mb-8 grid grid-cols-1 gap-2 md:grid-cols-2">
      @foreach ($latest as $b)
        <li>
          <a class="block rounded-lg border border-slate-200 bg-white px-4 py-2 text-slate-800 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
             href="{{ route('business.show', ['id' => $b->biz_id, 'slug' => \Illuminate\Support\Str::slug($b->business_name)]) }}">
            {{ $b->business_name }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>
@endsection
