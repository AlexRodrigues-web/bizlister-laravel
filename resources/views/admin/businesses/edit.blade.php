@extends('layouts.admin')
@section('content')
@include('admin._back_public')

<div class="p-6 max-w-2xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Editar NegÃƒÂ³cio #{{ $row->biz_id }}</h1>

  @if ($errors->any())
    <div class="p-3 mb-4 rounded bg-red-100 text-red-800">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="post" action="{{ route('admin.businesses.update',$row->biz_id) }}" class="space-y-4">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm font-medium">Nome</label>
      <input name="business_name" value="{{ old('business_name',$row->business_name) }}" class="border rounded px-3 py-2 w-full">
      @error('business_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium">DescriÃƒÂ§ÃƒÂ£o</label>
      <textarea name="description" rows="5" class="border rounded px-3 py-2 w-full">{{ old('description',$row->description) }}</textarea>
      @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Categoria</label>
        <select name="cid" class="border rounded px-3 py-2 w-full">
          @foreach($cats as $c)
            <option value="{{ $c->cat_id }}" {{ old('cid',$row->cid)==$c->cat_id ? 'selected' : '' }}>
              {{ $c->category }}
            </option>
          @endforeach
        </select>
        @error('cid')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Cidade</label>
        <select name="sid" class="border rounded px-3 py-2 w-full">
          @foreach($cities as $c)
            <option value="{{ $c->city_id }}" {{ old('sid',$row->sid)==$c->city_id ? 'selected' : '' }}>
              {{ $c->city }}
            </option>
          @endforeach
        </select>
        @error('sid')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Menu (legado, default 0)</label>
      <input type="number" name="menu" value="{{ old('menu', $row->menu ?? 0) }}" class="border rounded px-3 py-2 w-40">
      @error('menu')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.businesses.index') }}" class="px-3 py-2 border rounded">Cancelar</a>
      <button class="px-3 py-2 border rounded bg-gray-100">Salvar</button>
    </div>
  </form>
</div>
@endsection