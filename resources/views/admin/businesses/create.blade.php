@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-2xl p-6">
  <h1 class="text-2xl font-bold mb-4">Cadastrar Negócio</h1>

  @if ($errors->any())
    <div class="p-3 mb-4 rounded bg-red-100 text-red-800">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.businesses.store') }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium">Nome do Negócio</label>
      <input name="business_name" value="{{ old('business_name') }}" class="border rounded px-3 py-2 w-full">
      @error('business_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Descrição</label>
      <textarea name="description" rows="5" class="border rounded px-3 py-2 w-full">{{ old('description') }}</textarea>
      @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Categoria</label>
        <select name="cid" class="border rounded px-3 py-2 w-full">
          <option value="">Selecione…</option>
          @foreach($cats ?? [] as $c)
            @php
              $id = $c->cat_id ?? $c->id ?? null;
              $nm = $c->category ?? $c->cat_name ?? $c->name ?? ('Categoria #'.$id);
            @endphp
            <option value="{{ $id }}" @selected(old('cid')==$id)>{{ $nm }}</option>
          @endforeach
        </select>
        @error('cid')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Cidade</label>
        <select name="sid" class="border rounded px-3 py-2 w-full">
          <option value="">Selecione…</option>
          @foreach($cities ?? [] as $ci)
            @php
              $id = $ci->city_id ?? $ci->sid ?? $ci->id ?? null;
              $nm = $ci->city ?? $ci->name ?? $ci->label ?? ('Cidade #'.$id);
            @endphp
            <option value="{{ $id }}" @selected(old('sid')==$id)>{{ $nm }}</option>
          @endforeach
        </select>
        @error('sid')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="flex items-center gap-2">
      <input id="menu" type="checkbox" name="menu" value="1" @checked(old('menu'))>
      <label for="menu" class="text-sm">Destacar no menu</label>
      @error('menu')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.businesses.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
      <button class="px-4 py-2 border rounded bg-gray-100">Salvar</button>
    </div>
  </form>
</div>
@endsection