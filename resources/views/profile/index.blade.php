@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
  <h1 class="text-2xl font-semibold mb-6">Meu perfil</h1>

  @if(session('status'))
    <div class="mb-4 rounded bg-green-50 text-green-800 px-4 py-2">{{ session('status') }}</div>
  @endif

  <div class="space-y-4">
    <div>
      <span class="block text-sm text-gray-600">Nome</span>
      <span class="text-lg font-medium">{{ $user->name }}</span>
    </div>
    <div>
      <span class="block text-sm text-gray-600">E-mail</span>
      <span class="text-lg font-medium">{{ $user->email }}</span>
    </div>
  </div>

  <div class="mt-8">
    <a href="{{ url()->previous() }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">
      Voltar
    </a>
  </div>
</div>
@endsection