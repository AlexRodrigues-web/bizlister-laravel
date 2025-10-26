@extends('layouts.admin')

@section('content')
@include('admin._back_public')

@php
  /**
   * View única para CRIAR e EDITAR.
   * Aceita $row, $business OU $item como registro do negócio.
   * Recebe $cats (categories.cat_id, categories.category) e $cities (city.city_id, city.city).
   */

  // Registro do negócio (pode não existir no create)
  $row    = $row ?? ($business ?? ($item ?? null));
  $isEdit = isset($row) && isset($row->biz_id);
  $id     = $row->biz_id ?? null;

  // Valores com old()
  $business_name = old('business_name', $row->business_name ?? '');
  $description   = old('description',   $row->description   ?? '');
  $cid           = old('cid',           $row->cid           ?? ''); // categoria (FK)
  $sid           = old('sid',           $row->sid           ?? ''); // cidade    (FK)
  $menu          = old('menu',          $row->menu          ?? 0);
  $phone         = old('phone',         $row->phone         ?? '');
  $address       = old('address',       $row->address       ?? '');
  $status        = old('status',        $row->status        ?? '');

  // Listas vindas do controller
  $cats   = $cats   ?? ($categories ?? collect());
  $cities = $cities ?? collect();
  if (is_array($cats))   { $cats   = collect($cats); }
  if (is_array($cities)) { $cities = collect($cities); }

  // Helpers locais (objeto/array-safe)
  $getProp = function($obj, $k) {
      return is_object($obj) ? ($obj->{$k} ?? null)
           : (is_array($obj) ? ($obj[$k] ?? null) : null);
  };

  // --- Categoria ---
  $catIdOf = function($c) use ($getProp) {
      return $getProp($c, 'cat_id') ?? $getProp($c, 'id');
  };
  $catNameOf = function($c) use ($getProp, $catIdOf) {
      // Prioridade: category (tabela atual) -> category_name -> cat_name -> name -> label
      $nm = $getProp($c, 'category');
      if (!is_string($nm) || trim($nm) === '') { $nm = $getProp($c, 'category_name'); }
      if (!is_string($nm) || trim($nm) === '') { $nm = $getProp($c, 'cat_name'); }
      if (!is_string($nm) || trim($nm) === '') { $nm = $getProp($c, 'name'); }
      if (!is_string($nm) || trim($nm) === '') { $nm = $getProp($c, 'label'); }
      $id = $catIdOf($c);
      return (is_string($nm) && trim($nm) !== '') ? trim($nm) : ($id ? "Categoria #{$id}" : 'Categoria');
  };

  // --- Cidade ---
  $cityIdOf = function($ci) use ($getProp) {
      // alguns bancos tinham 'sid' associado a city_id
      return $getProp($ci, 'city_id') ?? $getProp($ci, 'sid') ?? $getProp($ci, 'id');
  };
  $cityNameOf = function($ci) use ($getProp, $cityIdOf) {
      // Prioridade: city (tabela atual) -> name -> label
      $nm = $getProp($ci, 'city');
      if (!is_string($nm) || trim($nm) === '') { $nm = $getProp($ci, 'name'); }
      if (!is_string($nm) || trim($nm) === '') { $nm = $getProp($ci, 'label'); }
      $nm = (is_string($nm) && trim($nm) !== '') ? trim($nm) : null;

      // Mostra UF/STATE se existir
      $uf = $getProp($ci, 'uf') ?? $getProp($ci, 'state');
      $uf = is_string($uf) ? strtoupper(trim($uf)) : '';

      if ($nm && $uf) return "{$nm} ({$uf})";
      if ($nm)        return $nm;

      $id = $cityIdOf($ci);
      return $id ? "Cidade #{$id}" : 'Cidade';
  };
@endphp

<div class="p-6 max-w-3xl mx-auto">
  {{-- Cabeçalho --}}
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">
      {{ $isEdit ? "Editar Negócio #{$id}" : "Novo Negócio" }}
    </h1>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.businesses.index') }}"
         class="px-3 py-2 rounded border border-slate-300 text-slate-700 hover:bg-slate-50">← Voltar</a>
      <a href="{{ route('admin.dashboard') }}"
         class="px-3 py-2 rounded border border-slate-300 text-slate-700 hover:bg-slate-50">Painel admin</a>
    </div>
  </div>

  {{-- Flash --}}
  @if (session('success'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  {{-- Erros --}}
  @if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-red-800">
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="post"
        action="{{ $isEdit ? route('admin.businesses.update', $id) : route('admin.businesses.store') }}"
        enctype="multipart/form-data"
        class="space-y-5">
    @csrf
    @if($isEdit) @method('PUT') @endif

    {{-- Nome --}}
    <div>
      <label class="block text-sm font-medium">Nome</label>
      <input
        name="business_name"
        value="{{ $business_name }}"
        required
        class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
      @error('business_name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    {{-- Descrição --}}
    <div>
      <label class="block text-sm font-medium">Descrição</label>
      <textarea
        name="description"
        rows="5"
        class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">{{ $description }}</textarea>
      @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      {{-- Categoria --}}
      <div>
        <label class="block text-sm font-medium">Categoria</label>
        <select
          name="cid"
          class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
          <option value="">Selecione…</option>
          @forelse($cats as $c)
            @php
              $catId = $catIdOf($c);
              $catNm = $catNameOf($c);
            @endphp
            <option value="{{ $catId }}" {{ (string)$cid === (string)$catId ? 'selected' : '' }}>
              {{ $catNm }}
            </option>
          @empty
            <option value="" disabled>(sem categorias)</option>
          @endforelse
        </select>
        @if(method_exists($cats, 'count') && $cats->count() === 0)
          <div class="mt-2 text-xs text-slate-500">
            Nenhuma categoria encontrada.
            <a class="text-indigo-600 hover:underline" href="{{ route('admin.categories.create') }}">+ Nova Categoria</a>
          </div>
        @endif
        @error('cid')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      {{-- Cidade --}}
      <div>
        <label class="block text-sm font-medium">Cidade</label>
        <select
          name="sid"
          class="mt-1 w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
          <option value="">Selecione…</option>
          @forelse($cities as $ci)
            @php
              $cityId = $cityIdOf($ci);
              $cityNm = $cityNameOf($ci);
            @endphp
            <option value="{{ $cityId }}" {{ (string)$sid === (string)$cityId ? 'selected' : '' }}>
              {{ $cityNm }}
            </option>
          @empty
            <option value="" disabled>(sem cidades)</option>
          @endforelse
        </select>
        @if(method_exists($cities, 'count') && $cities->count() === 0)
          <div class="mt-2 text-xs text-slate-500">
            Nenhuma cidade encontrada.
            <a class="text-indigo-600 hover:underline" href="{{ route('admin.cities.create') }}">+ Nova Cidade</a>
          </div>
        @endif
        @error('sid')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Menu (número)</label>
        <input type="number" name="menu" value="{{ $menu }}" class="mt-1 w-40 rounded border px-3 py-2">
        @error('menu')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>
      <div>
        <label class="block text-sm font-medium">Telefone</label>
        <input name="phone" value="{{ $phone }}" class="mt-1 w-full rounded border px-3 py-2">
        @error('phone')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium">Endereço</label>
      <input name="address" value="{{ $address }}" class="mt-1 w-full rounded border px-3 py-2">
      @error('address')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Status</label>
      <input name="status" value="{{ $status }}" class="mt-1 w-full rounded border px-3 py-2">
      @error('status')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Imagem (opcional)</label>
      <input type="file" name="image" accept="image/*" class="mt-1 block">
      @error('image')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror

      @if($isEdit && !empty($row->image))
        <div class="mt-2 text-sm text-slate-600">Atual: {{ $row->image }}</div>
      @endif
    </div>

    <div class="flex gap-2">
      <a href="{{ route('admin.businesses.index') }}" class="px-4 py-2 rounded border">Cancelar</a>
      <button class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
        {{ $isEdit ? 'Salvar alterações' : 'Cadastrar Negócio' }}
      </button>
    </div>
  </form>
</div>
@endsection
