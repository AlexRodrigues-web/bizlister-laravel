@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-8">
  <h1 class="text-2xl font-semibold mb-6">Cadastrar Negócio</h1>

  <form method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data" class="space-y-6">
    @csrf

    <div>
      <label class="block text-sm font-medium text-gray-700" for="business_name">Nome do Negócio</label>
      <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}" required
             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
      @error('business_name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700" for="cid">Categoria</label>
      <select id="cid" name="cid" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">Selecione...</option>
        @foreach($categories as $c)
          <option value="{{ $c->cat_id }}" {{ old('cid') == $c->cat_id ? 'selected' : '' }}>
            {{ $c->label }}
          </option>
        @endforeach
      </select>
      @error('cid')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700" for="sid">Cidade</label>
      <select id="sid" name="sid" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        <option value="">Selecione...</option>
        @foreach($cities as $s)
          <option value="{{ $s->city_id }}" {{ old('sid') == $s->city_id ? 'selected' : '' }}>
            {{ $s->label }}
          </option>
        @endforeach
      </select>
      @error('sid')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700" for="description">Descrição</label>
      <textarea id="description" name="description" rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                placeholder="Conte um pouco sobre o negócio...">{{ old('description') }}</textarea>
      @error('description')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700" for="image">Imagem (opcional)</label>
      <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full" />
      <p class="text-xs text-gray-500 mt-1">Arquivos até 2MB. Formatos: JPG, PNG, WEBP.</p>
      @error('image')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex gap-3">
      <button type="submit" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">
        Salvar
      </button>
      <a href="{{ url()->previous() }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">
        Cancelar
      </a>
    </div>
  </form>
</div>
@endsection
