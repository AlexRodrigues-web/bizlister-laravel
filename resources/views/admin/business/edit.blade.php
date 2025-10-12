@extends("layouts.app")

@section("content")
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Editar Negócio</h1>

  @if(session("success"))
    <p class="mb-3 text-green-700">{{ session("success") }}</p>
  @endif

  <form method="POST" action="{{ route('admin.businesses.update', $item->biz_id) }}" class="space-y-4">
    @csrf
    @method("PUT")

    <div>
      <label class="block text-sm mb-1">Nome</label>
      <input type="text" name="business_name" class="border rounded px-3 py-2 w-full"
             value="{{ old('business_name', $item->business_name) }}" required>
      @error('business_name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
      <label class="block text-sm mb-1">Descrição</label>
      <textarea name="description" class="border rounded px-3 py-2 w-full" rows="4">{{ old('description', $item->description) }}</textarea>
      @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm mb-1">Categoria</label>
        <select name="cid" class="border rounded px-3 py-2 w-full">
          <option value="">-- Selecione --</option>
          @foreach($categories as $c)
            @php
              $catLabel = $c->category ?? $c->title ?? $c->name ?? $c->cat_name ?? ('#'.$c->cat_id);
              $selCat   = old('cid', $item->cid ?? null) == $c->cat_id ? 'selected' : '';
            @endphp
            <option value="{{ $c->cat_id }}" {{ $selCat }}>{{ $catLabel }}</option>
          @endforeach
        </select>
        @error('cid') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm mb-1">Cidade</label>
        <select name="city_id" class="border rounded px-3 py-2 w-full">
          <option value="">-- Selecione --</option>
          @foreach($cities as $c)
            @php
              $selCity = old('city_id', $item->city_id ?? $item->sid ?? null) == $c->city_id ? 'selected' : '';
            @endphp
            <option value="{{ $c->city_id }}" {{ $selCity }}>{{ $c->city }}</option>
          @endforeach
        </select>
        @error('city_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>
    </div>

    <div class="flex gap-2">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">Salvar</button>
      <a href="{{ route('admin.businesses.index') }}" class="px-4 py-2 border rounded">Cancelar</a>
    </div>
  </form>
</div>
@endsection
