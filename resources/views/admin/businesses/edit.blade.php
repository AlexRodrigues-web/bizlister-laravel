@extends('layouts.admin')

@section('content')

@include('admin._back_public')

@php
  // Modo: criar ou editar
  $isEdit = isset($row) && isset($row->biz_id);
  $id     = $row->biz_id ?? null;

  // Fallbacks seguros
  $business_name = old('business_name', $row->business_name ?? '');
  $description   = old('description',   $row->description   ?? '');
  $cid           = old('cid',           $row->cid           ?? '');
  $sid           = old('sid',           $row->sid           ?? '');
  $menu          = old('menu',          $row->menu          ?? 0);
  $phone         = old('phone',         $row->phone         ?? '');
  $address       = old('address',       $row->address       ?? '');
  $status        = old('status',        $row->status        ?? '');
@endphp

<div class="p-6 max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">
    {{ $isEdit ? "Editar Negócio #{$id}" : "Novo Negócio" }}
  </h1>

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

    <div>
      <label class="block text-sm font-medium">Nome</label>
      <input name="business_name" value="{{ $business_name }}" class="mt-1 w-full rounded border px-3 py-2">
      @error('business_name')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium">Descrição</label>
      <textarea name="description" rows="5" class="mt-1 w-full rounded border px-3 py-2">{{ $description }}</textarea>
      @error('description')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Categoria</label>
        <select name="cid" class="mt-1 w-full rounded border px-3 py-2">
          <option value="">-- selecione --</option>
          @foreach(($cats ?? []) as $c)
            @php
              $catId = $c->cat_id ?? $c->id ?? null;
              $catNm = $c->category ?? $c->cat_name ?? $c->name ?? $c->label ?? ('Categoria #'.$catId);
            @endphp
            <option value="{{ $catId }}" {{ (string)$cid === (string)$catId ? 'selected' : '' }}>{{ $catNm }}</option>
          @endforeach
        </select>
        @error('cid')<div class="text-sm text-red-600 mt-1">{{ $message }}</div>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium">Cidade</label>
        <select name="sid" class="mt-1 w-full rounded border px-3 py-2">
          <option value="">-- selecione --</option>
          @foreach(($cities ?? []) as $ci)
            @php
              $cityId = $ci->city_id ?? $ci->sid ?? $ci->id ?? null;
              $cityNm = $ci->city ?? $ci->name ?? $ci->label ?? ('Cidade #'.$cityId);
            @endphp
            <option value="{{ $cityId }}" {{ (string)$sid === (string)$cityId ? 'selected' : '' }}>{{ $cityNm }}</option>
          @endforeach
        </select>
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
