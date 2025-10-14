@extends('layouts.app')
@section('content')
<div class="max-w-lg mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Nova Cidade</h1>
  <form method="post" action="{{ route('admin.cities.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm mb-1">Nome</label>
      <input name="city" value="{{ old('city') }}" class="border rounded px-3 py-2 w-full">
      @error('city')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.cities.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 border rounded bg-gray-100">Salvar</button>
    </div>
  </form>
</div>
@endsection