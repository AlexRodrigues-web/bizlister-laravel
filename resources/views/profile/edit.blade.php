{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar perfil')

@section('content')
@php
  /** @var \App\Models\User $user */
  $user = $user ?? auth()->user();
@endphp

<div class="max-w-3xl mx-auto px-4 py-8">
  <div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-900">Editar perfil</h1>
    <p class="text-sm text-slate-600 mt-1">Atualize seus dados de conta. A senha é opcional.</p>
  </div>

  @if (session('status'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
      {{ session('status') }}
    </div>
  @endif

  <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method('PATCH')

    {{-- Nome --}}
    <div>
      <label for="name" class="block text-sm font-medium text-slate-700">Nome</label>
      <input id="name" name="name" type="text" required
             value="{{ old('name', $user->name) }}"
             class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('name') border-red-500 @enderror">
      @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- E-mail --}}
    <div>
      <label for="email" class="block text-sm font-medium text-slate-700">E-mail</label>
      <input id="email" name="email" type="email" required
             value="{{ old('email', $user->email) }}"
             class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('email') border-red-500 @enderror">
      @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Alterar senha (opcional) --}}
    <fieldset class="rounded-2xl border border-slate-200 p-4">
      <legend class="px-2 text-sm text-slate-600">Alterar senha (opcional)</legend>

      <div class="mt-2">
        <label for="current_password" class="block text-sm font-medium text-slate-700">Senha atual</label>
        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
               class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('current_password') border-red-500 @enderror">
        @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="mt-2">
        <label for="password" class="block text-sm font-medium text-slate-700">Nova senha</label>
        <input id="password" name="password" type="password" autocomplete="new-password"
               class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('password') border-red-500 @enderror">
        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
      </div>

      <div class="mt-2">
        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirmar nova senha</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
               class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
      </div>
    </fieldset>

    <div class="pt-2 flex items-center gap-3">
      <button type="submit"
              class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
        Salvar alterações
      </button>
      <a href="{{ route('profile.show') }}"
         class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 text-sm text-slate-700 hover:bg-slate-50">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
