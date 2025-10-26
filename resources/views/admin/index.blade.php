@extends('layouts.admin')

@section('content')
@include('admin._back_public')

{{-- ===== Toggle (checkbox hack, sem JS) ===== --}}
<input id="adminSidebar" type="checkbox" class="sr-only peer" />

{{-- Backdrop (clica e fecha) --}}
<label for="adminSidebar"
       class="fixed inset-0 z-30 hidden bg-black/30 backdrop-blur-sm peer-checked:block"></label>

{{-- Sidebar OFF-CANVAS (oculto por padrão) --}}
<aside class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full peer-checked:translate-x-0
              transition-transform duration-200 ease-out bg-white border-r border-slate-200 shadow-xl">
  <div class="flex items-center justify-between px-4 py-3 border-b border-slate-200">
    <span class="text-sm font-semibold text-slate-700">Menu</span>
    <label for="adminSidebar"
           class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                  text-slate-600 hover:bg-slate-100 cursor-pointer">
      Fechar
    </label>
  </div>
  <div class="p-3 overflow-y-auto h-[calc(100vh-48px)]">
    {{-- usa seu menu existente, sem alterar rotas --}}
    @include('admin._sidebar')
  </div>
</aside>

<div class="mx-auto max-w-6xl p-6">

  {{-- Alertas --}}
  @if (session('success'))
    <div role="alert" class="mb-6 flex items-start gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <div class="grow">{{ session('success') }}</div>
    </div>
  @endif

  {{-- Cabeçalho / Hero --}}
  <div class="mb-6 rounded-xl border border-slate-200 bg-gradient-to-r from-indigo-50 to-blue-50 px-5 py-4">
    <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-800">
          Painel Administrativo
        </h1>
        <p class="mt-0.5 text-sm text-slate-600">
          Visão geral e atalhos rápidos de gestão.
        </p>
      </div>

      <div class="flex items-center gap-2">
        {{-- Botão que abre o sidebar --}}
        <label for="adminSidebar"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2
                      text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 cursor-pointer">
          <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z"/>
          </svg>
          Menu
        </label>

        <div class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-600">
          <span class="rounded-full bg-white/70 px-2 py-1">Laravel 8.x</span>
          <span class="rounded-full bg-white/70 px-2 py-1">Tailwind</span>
          <span class="rounded-full bg-white/70 px-2 py-1">PHP {{ PHP_VERSION }}</span>
        </div>
      </div>
    </div>
  </div>

  @php
    // Totais vindos do controller (mantido)
    $totals = $totals ?? [];
    $fmt = fn($v) => number_format((int)($v ?? 0), 0, ',', '.');
  @endphp

  {{-- Visão geral (cards) --}}
  <section aria-labelledby="overview-title" class="mb-8 rounded-xl border border-slate-200 bg-white/80 backdrop-blur-sm">
    <div class="border-b border-slate-200 px-5 py-3">
      <h2 id="overview-title" class="text-sm font-semibold text-slate-700">Visão geral</h2>
    </div>

    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
      {{-- Usuários --}}
      <div class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-500">Usuários cadastrados</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">
              {{ $fmt($totals['users'] ?? 0) }}
            </div>
          </div>
          <div class="rounded-lg bg-slate-50 p-2 text-slate-400 group-hover:text-slate-600" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4Zm-8 0c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4Zm0 2c-2.673 0-8 1.337-8 4v2h16v-2c0-2.663-5.327-4-8-4Zm8 0c-.29 0-.616.02-.97.055 1.16.832 1.97 1.93 1.97 3.445V19h7v-2c0-2.663-5.327-4-8-4Z"/>
            </svg>
          </div>
        </div>
      </div>

      {{-- Cidades --}}
      <div class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-500">Cidades</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">
              {{ $fmt($totals['cities'] ?? 0) }}
            </div>
          </div>
          <div class="rounded-lg bg-slate-50 p-2 text-slate-400 group-hover:text-slate-600" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 21V8l7-5 7 5v13h-5v-6H8v6H3Z"/>
            </svg>
          </div>
        </div>
      </div>

      {{-- Negócios --}}
      <div class="group rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-500">Negócios</div>
            <div class="mt-1 text-2xl font-semibold text-slate-900">
              {{ $fmt($totals['business'] ?? 0) }}
            </div>
          </div>
          <div class="rounded-lg bg-slate-50 p-2 text-slate-400 group-hover:text-slate-600" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
              <path d="M3 21V7h18v14H3Zm2-2h14V9H5v10Zm2-6h5v2H7v-2Zm0 3h10v2H7v-2Z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Atalhos de gerenciamento --}}
  <div class="mb-3 flex items-center justify-between">
    <h2 class="text-sm font-semibold text-slate-700">Gerenciamento rápido</h2>
  </div>

  <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
    {{-- Categorias --}}
    <a
      href="{{ route('admin.categories.index') }}"
      class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
      aria-label="Gerenciar categorias"
    >
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-slate-500">Gerenciar</div>
          <div class="mt-1 text-lg font-semibold text-slate-900">Categorias</div>
          <div class="mt-2 text-sm text-slate-600">
            Total: <span class="font-medium text-slate-800">{{ $fmt($totals['categories'] ?? 0) }}</span>
          </div>
        </div>
        <div class="rounded-lg bg-indigo-50 p-2 text-indigo-500 transition group-hover:bg-indigo-100" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z"/></svg>
        </div>
      </div>
      <div class="pointer-events-none mt-3 text-slate-400 transition group-hover:text-indigo-600" aria-hidden="true">
        &rarr;
      </div>
    </a>

    {{-- Cidades --}}
    <a
      href="{{ route('admin.cities.index') }}"
      class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
      aria-label="Gerenciar cidades"
    >
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-slate-500">Gerenciar</div>
          <div class="mt-1 text-lg font-semibold text-slate-900">Cidades</div>
          <div class="mt-2 text-sm text-slate-600">
            Total: <span class="font-medium text-slate-800">{{ $fmt($totals['cities'] ?? 0) }}</span>
          </div>
        </div>
        <div class="rounded-lg bg-indigo-50 p-2 text-indigo-500 transition group-hover:bg-indigo-100" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V8l7-5 7 5v13H3Zm10-2h3v-9l-3-2v11Z"/></svg>
        </div>
      </div>
      <div class="pointer-events-none mt-3 text-slate-400 transition group-hover:text-indigo-600" aria-hidden="true">
        &rarr;
      </div>
    </a>

    {{-- Negócios --}}
    <a
      href="{{ route('admin.businesses.index') }}"
      class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
      aria-label="Gerenciar negócios"
    >
      <div class="flex items-start justify-between">
        <div>
          <div class="text-xs uppercase tracking-wide text-slate-500">Gerenciar</div>
          <div class="mt-1 text-lg font-semibold text-slate-900">Negócios</div>
          <div class="mt-2 text-sm text-slate-600">
            Total: <span class="font-medium text-slate-800">{{ $fmt($totals['business'] ?? 0) }}</span>
          </div>
        </div>
        <div class="rounded-lg bg-indigo-50 p-2 text-indigo-500 transition group-hover:bg-indigo-100" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V7h18v14H3Zm2-2h14V9H5v10Z"/></svg>
        </div>
      </div>
      <div class="pointer-events-none mt-3 text-slate-400 transition group-hover:text-indigo-600" aria-hidden="true">
        &rarr;
      </div>
    </a>
  </div>

  {{-- Rodapé da área --}}
  <div class="mt-8 border-t border-slate-200 pt-4 text-xs text-slate-500">
    Área administrativa • Mantendo rotas e dados existentes
  </div>
</div>
@endsection
