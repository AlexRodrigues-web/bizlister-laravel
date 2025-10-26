@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-7xl px-4 py-6">
  {{-- Cabeçalho + faixa com destaque --}}
  <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-r from-indigo-600 via-indigo-500 to-blue-500 p-6 shadow-sm">
    <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-white">Admin</h1>
        <p class="mt-1 text-sm text-indigo-100">Painel • Laravel 8.x</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('search.index') }}"
           class="inline-flex items-center rounded-lg bg-white/10 px-3 py-2 text-sm font-medium text-white ring-1 ring-white/30 backdrop-blur hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70">
          Buscar Negócios
        </a>
        <a href="{{ route('business.create') }}"
           class="inline-flex items-center rounded-lg bg-white px-3 py-2 text-sm font-medium text-slate-900 hover:bg-indigo-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70">
          Cadastrar Negócio (público)
        </a>
      </div>
    </div>

    {{-- “onda” decorativa sutil --}}
    <div class="pointer-events-none absolute -right-10 -top-10 h-48 w-48 rounded-full bg-white/10 blur-2xl"></div>
  </div>

  {{-- Flash de sucesso (mantido) --}}
  @if (session('success'))
    <div class="mt-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
      {{ session('success') }}
    </div>
  @endif

  {{-- Resumo / KPI Cards --}}
  @php
    $nf = fn($v) => is_numeric($v ?? null) ? number_format((int)$v, 0, ',', '.') : ($v ?? '-');
    $card = 'group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-slate-900 dark:border-slate-700';
    $chip = 'inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200 group-hover:bg-indigo-50 group-hover:text-indigo-700 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700';
    $h = 'text-[28px] leading-none font-bold tracking-tight text-slate-900 dark:text-white';
    $muted = 'text-sm text-slate-500 dark:text-slate-400';
    $btn = 'inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-medium focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/50';
    $btnPrimary = $btn.' bg-indigo-600 text-white hover:bg-indigo-700';
    $btnGhost   = $btn.' bg-slate-100 text-slate-800 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700';
  @endphp

  <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-4">
    {{-- Cidades --}}
    <div class="{{ $card }}" aria-label="Resumo de Cidades">
      <div class="flex items-start justify-between">
        <div>
          <span class="{{ $chip }}">Cidades</span>
          <div class="mt-2 {{ $h }}">{{ $nf($totCities ?? '-') }}</div>
          <p class="mt-1 {{ $muted }}">Regiões cadastradas</p>
        </div>
        <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V8l7-5 7 5v13H3Z"/></svg>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <a class="{{ $btnPrimary }}" href="{{ route('admin.cities.index') }}">Listar</a>
        <a class="{{ $btnGhost }}" href="{{ route('admin.cities.create') }}">Novo</a>
      </div>
    </div>

    {{-- Categorias --}}
    <div class="{{ $card }}" aria-label="Resumo de Categorias">
      <div class="flex items-start justify-between">
        <div>
          <span class="{{ $chip }}">Categorias</span>
          <div class="mt-2 {{ $h }}">{{ $nf($totCategories ?? '-') }}</div>
          <p class="mt-1 {{ $muted }}">Organização do diretório</p>
        </div>
        <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z"/></svg>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <a class="{{ $btnPrimary }}" href="{{ route('admin.categories.index') }}">Listar</a>
        <a class="{{ $btnGhost }}" href="{{ route('admin.categories.create') }}">Nova</a>
      </div>
    </div>

    {{-- Negócios --}}
    <div class="{{ $card }}" aria-label="Resumo de Negócios">
      <div class="flex items-start justify-between">
        <div>
          <span class="{{ $chip }}">Negócios</span>
          <div class="mt-2 {{ $h }}">{{ $nf($totBusinesses ?? '-') }}</div>
          <p class="mt-1 {{ $muted }}">Cadastros ativos</p>
        </div>
        <div class="rounded-xl bg-amber-50 p-3 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V7h18v14H3Zm2-2h14V9H5v10Zm2-6h5v2H7v-2Z"/></svg>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <a class="{{ $btnPrimary }}" href="{{ route('admin.businesses.index') }}">Listar</a>
        {{-- <a class="btn btn-sm" href="{{ route('admin.businesses.create') }}">Novo</a> --}}
      </div>
    </div>

    {{-- Ações rápidas (mantido) --}}
    <div class="{{ $card }}" aria-label="Ações rápidas">
      <span class="{{ $chip }}">Ações rápidas</span>
      <div class="mt-3 flex flex-col gap-2">
        <a class="{{ $btnGhost }}" href="{{ route('business.create') }}">Cadastrar Negócio (público)</a>
        <a class="{{ $btnGhost }}" href="{{ route('search.index') }}">Buscar Negócios</a>
      </div>
    </div>
  </div>

  {{-- Atalhos de gerenciamento (mantidos) --}}
  <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
    <a href="{{ route('admin.categories.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-transparent transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/50 dark:bg-slate-900 dark:border-slate-700">
      <div class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white">
        <span class="rounded-lg bg-indigo-50 p-2 text-indigo-600 group-hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z"/></svg>
        </span>
        Gerir Categorias
      </div>
      <p class="mt-1 text-sm text-slate-600 group-hover:text-slate-700 dark:text-slate-400">Listar, criar, editar, remover</p>
    </a>

    <a href="{{ route('admin.cities.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-transparent transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/50 dark:bg-slate-900 dark:border-slate-700">
      <div class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white">
        <span class="rounded-lg bg-emerald-50 p-2 text-emerald-600 group-hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V8l7-5 7 5v13H3Z"/></svg>
        </span>
        Gerir Cidades
      </div>
      <p class="mt-1 text-sm text-slate-600 group-hover:text-slate-700 dark:text-slate-400">Listar, criar, editar, remover</p>
    </a>

    <a href="{{ route('admin.businesses.index') }}"
       class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm ring-1 ring-transparent transition hover:-translate-y-0.5 hover:border-indigo-300 hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500/50 dark:bg-slate-900 dark:border-slate-700">
      <div class="flex items-center gap-3 font-semibold text-slate-900 dark:text-white">
        <span class="rounded-lg bg-amber-50 p-2 text-amber-600 group-hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300" aria-hidden="true">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3 21V7h18v14H3Zm2-2h14V9H5v10Z"/></svg>
        </span>
        Gerir Negócios
      </div>
      <p class="mt-1 text-sm text-slate-600 group-hover:text-slate-700 dark:text-slate-400">Listar e editar/remover</p>
    </a>
  </div>

  {{-- Rodapé simples da área (mantido) --}}
  <div class="mt-8 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-xs text-slate-500 shadow-sm dark:bg-slate-900 dark:border-slate-700">
    Área administrativa • Mantendo rotas e dados existentes
  </div>
</div>
@endsection
