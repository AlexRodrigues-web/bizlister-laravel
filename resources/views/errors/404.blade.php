@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12">
  <h1 class="text-3xl font-bold text-slate-800 mb-3">Página não encontrada (404)</h1>
  <p class="text-slate-600 mb-6">
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
          class="flex-1 rounded-lg border border-slate-300 px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
          autocomplete="off"
          spellcheck="false"
        >
        <button type="submit"
                class="rounded-lg bg-indigo-600 px-5 py-2 text-white hover:bg-indigo-700">
          Buscar
        </button>
      </div>
    </form>
  @endif

  {{-- Links úteis --}}
  <div class="mb-10">
    <h2 class="text-xl font-semibold text-slate-800 mb-3">Acesso rápido</h2>
    <nav aria-label="Links úteis" class="flex flex-wrap gap-3">
      <a href="{{ url('/') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
        Início
      </a>

      @if (Route::has('categories.index'))
        <a href="{{ route('categories.index') }}"
           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
          Categorias
        </a>
      @endif

      @if (Route::has('cities.index'))
        <a href="{{ route('cities.index') }}"
           class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
          Cidades
        </a>
      @endif

      @if (Route::has('business.create'))
        <a href="{{ route('business.create') }}"
           class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
          Cadastrar um negócio
        </a>
      @endif

      {{-- Páginas estáticas --}}
      <a href="{{ url('/sobre-nos') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
        Sobre Nós
      </a>
      <a href="{{ url('/termos-de-uso') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
        Termos de Uso
      </a>
      <a href="{{ url('/politica-de-privacidade') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
        Política de Privacidade
      </a>

      {{-- Contato & Sitemap --}}
      <a href="{{ url('/contato') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200">
        Contato
      </a>
      <a href="{{ url('/sitemap.xml') }}"
         class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-slate-700 hover:bg-slate-200" rel="nofollow">
        Sitemap
      </a>

      {{-- Voltar --}}
      <button type="button"
              onclick="history.back()"
              class="inline-flex items-center rounded-lg bg-white border border-slate-200 px-4 py-2 text-slate-700 hover:bg-slate-50">
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
    <h2 class="text-xl font-semibold text-slate-800 mb-3">Talvez você esteja procurando:</h2>
    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-8">
      @foreach ($latest as $b)
        <li>
          <a class="block rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50"
             href="{{ route('business.show', ['id' => $b->biz_id, 'slug' => \Illuminate\Support\Str::slug($b->business_name)]) }}">
            {{ $b->business_name }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif
</div>
@endsection
