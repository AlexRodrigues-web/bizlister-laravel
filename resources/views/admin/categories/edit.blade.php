@extends('layouts.app')
@section('content')
<div class="max-w-lg mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Editar Categoria #{{ $row->cat_id }}</h1>
  <form method="post" action="{{ route('admin.categories.update', ['category' => resources\views\admin\categories\edit.blade.phpcategory->cat_id]) }}" class="space-y-4">
    @csrf @method('PUT')
    <div>
      <label class="block text-sm mb-1">Nome</label>
      <input name="category" value="{{ old('category',$row->category) }}" class="border rounded px-3 py-2 w-full">
      @error('category')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 border rounded bg-gray-100">Salvar</button>
    </div>
  </form>
</div>
@endsection

