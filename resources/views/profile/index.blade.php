@extends('layouts.app')

@section('content')
@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Support\Str;

  /** @var \App\Models\User $user */
  $user = $user ?? Auth::user();

  // Avatar (inicial do nome)
  $initials = collect(explode(' ', trim((string)$user->name)))
      ->filter()->take(2)->map(fn($p)=>Str::upper(Str::substr($p,0,1)))->implode('');

  // Contadores (com fallback para bancos diferentes)
  $counts = ['bookmarks' => 0, 'reviews' => 0];
  try {
      if (Schema::hasTable('bookmarks')) {
          $counts['bookmarks'] = (int) DB::table('bookmarks')->where('user_id', $user->id)->count();
      } elseif (Schema::hasTable('bookmark')) {
          $counts['bookmarks'] = (int) DB::table('bookmark')->where('user_id', $user->id)->count();
      }
      if (Schema::hasTable('reviews')) {
          $counts['reviews'] = (int) DB::table('reviews')->where('user_id', $user->id)->count();
      } elseif (Schema::hasTable('review')) {
          $counts['reviews'] = (int) DB::table('review')->where('user_id', $user->id)->count();
      }
  } catch (\Throwable $e) { /* silencioso */ }

  $joined = optional($user->created_at)->translatedFormat('d MMM yyyy');
@endphp

<div class="mx-auto max-w-5xl px-4 py-8">
  {{-- Header / capa --}}
  <div class="relative overflow-hidden rounded-2xl border border-slate-200 bg-gradient-to-br from-indigo-50 via-white to-pink-50">
    <div class="absolute inset-0 opacity-10" aria-hidden="true"></div>

    <div class="relative flex flex-col gap-6 p-6 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-4">
        {{-- Avatar com iniciais --}}
        <div class="grid h-16 w-16 place-items-center rounded-2xl bg-indigo-600 text-white text-2xl font-bold shadow-sm">
          {{ $initials ?: 'U' }}
        </div>

        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Meu perfil</h1>
          <p class="mt-1 text-sm text-slate-600">
            Bem-vindo, {{ $user->name }}. @if($joined) Conta criada em <span class="font-medium text-slate-700">{{ $joined }}</span>. @endif
          </p>
        </div>
      </div>

      {{-- Ações rápidas --}}
      <div class="flex flex-wrap items-center gap-2">
        @if (Route::has('profile.edit'))
          <a href="{{ route('profile.edit') }}"
             class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25Zm14.71-9.04a1 1 0 0 0 0-1.41l-2.5-2.5a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.99-1.67Z"/></svg>
            Editar perfil
          </a>
        @endif

        @if (Route::has('bookmarks.index'))
          <a href="{{ route('bookmarks.index') }}"
             class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            ★ Meus favoritos
          </a>
        @endif

        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button type="submit"
                  class="inline-flex items-center gap-2 rounded-lg border border-transparent px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">
            Sair
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Alerts --}}
  @if (session('status'))
    <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-2 text-green-800">
      {{ session('status') }}
    </div>
  @endif

  {{-- Cards de estatísticas --}}
  <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
    <div class="rounded-xl border border-slate-200 bg-white p-4">
      <div class="text-sm text-slate-500">Favoritos</div>
      <div class="mt-1 flex items-end gap-2">
        <div class="text-3xl font-semibold text-slate-900">{{ $counts['bookmarks'] }}</div>
        <a href="{{ Route::has('bookmarks.index') ? route('bookmarks.index') : '#' }}"
           class="ml-auto text-sm text-indigo-600 hover:underline">ver todos</a>
      </div>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-4">
      <div class="text-sm text-slate-500">Avaliações</div>
      <div class="mt-1 flex items-end gap-2">
        <div class="text-3xl font-semibold text-slate-900">{{ $counts['reviews'] }}</div>
        @if (Route::has('search.index'))
          <a href="{{ route('search.index', ['sort' => 'recent']) }}" class="ml-auto text-sm text-indigo-600 hover:underline">explorar negócios</a>
        @endif
      </div>
    </div>
  </div>

  {{-- Detalhes da conta --}}
  <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-6">
    <h2 class="text-lg font-semibold text-slate-900">Dados da conta</h2>

    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
      <div class="rounded-xl border border-slate-100 p-4">
        <dt class="text-xs uppercase tracking-wide text-slate-500">Nome</dt>
        <dd class="mt-1 text-slate-900 font-medium">{{ $user->name }}</dd>
      </div>
      <div class="rounded-xl border border-slate-100 p-4">
        <dt class="text-xs uppercase tracking-wide text-slate-500">E-mail</dt>
        <dd class="mt-1 text-slate-900 font-medium">{{ $user->email }}</dd>
      </div>

      @if (!empty($user->email_verified_at))
        <div class="rounded-xl border border-slate-100 p-4">
          <dt class="text-xs uppercase tracking-wide text-slate-500">Verificado em</dt>
          <dd class="mt-1 text-slate-900 font-medium">
            {{ optional($user->email_verified_at)->translatedFormat('d MMM yyyy HH:mm') }}
          </dd>
        </div>
      @endif

      <div class="rounded-xl border border-slate-100 p-4">
        <dt class="text-xs uppercase tracking-wide text-slate-500">ID do usuário</dt>
        <dd class="mt-1 text-slate-900 font-mono text-sm">{{ $user->id }}</dd>
      </div>
    </dl>

    @if (Route::has('profile.edit'))
      <div class="mt-4">
        <a href="{{ route('profile.edit') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-800">
          Ajustar dados
        </a>
      </div>
    @endif
  </div>

  {{-- Voltar --}}
  <div class="mt-6">
    <a href="{{ url()->previous() }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
      ← Voltar
    </a>
  </div>
</div>
@endsection
