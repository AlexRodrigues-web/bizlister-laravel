@extends('layouts.admin')

@section('content')
@include('admin._back_public')

@php
  // Helpers de rótulo compatíveis com colunas legadas
  $catLabel = function ($c) {
    $id = $c->cat_id ?? $c->id ?? null;
    $nm = $c->category ?? $c->cat_name ?? $c->name ?? null;
    return (is_string($nm) && trim($nm) !== '') ? trim($nm) : ($id ? 'Categoria #'.$id : 'Categoria');
  };

  $cityLabel = function ($ci) {
    $id = $ci->city_id ?? $ci->sid ?? $ci->id ?? null;
    // Prioriza coluna legada 'city'; depois name/label
    $nm = $ci->city ?? $ci->name ?? $ci->label ?? null;
    return (is_string($nm) && trim($nm) !== '') ? trim($nm) : ($id ? 'Cidade #'.$id : 'Cidade');
  };
@endphp

<div class="mx-auto max-w-3xl p-6">

  {{-- Breadcrumbs / Voltar --}}
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <a href="{{ route('admin.businesses.index') }}" class="hover:underline">Negócios</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Cadastrar</span>
    </nav>

    <a href="{{ route('admin.businesses.index') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-50">
      <span aria-hidden="true">←</span> Voltar
    </a>
  </div>

  {{-- Cabeçalho --}}
  <div class="mb-5">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">Cadastrar Negócio</h1>
    <p class="mt-1 text-sm text-slate-600">Preencha os campos abaixo para adicionar um novo negócio.</p>
  </div>

  {{-- Erros de validação --}}
  @if ($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800">
      <div class="font-semibold mb-1">Corrija os erros abaixo:</div>
      <ul class="list-disc pl-5">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Card do formulário --}}
  <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form method="POST"
          action="{{ route('admin.businesses.store') }}"
          enctype="multipart/form-data"
          class="space-y-6">
      @csrf

      {{-- Nome do Negócio --}}
      <div>
        <label for="business_name" class="block text-sm font-medium text-slate-700">Nome do Negócio</label>
        <input
          id="business_name"
          name="business_name"
          value="{{ old('business_name') }}"
          required
          placeholder="Ex.: Pizzaria Central"
          class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('business_name') border-red-500 @enderror"
        >
        @error('business_name')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      {{-- Descrição --}}
      <div>
        <label for="description" class="block text-sm font-medium text-slate-700">Descrição</label>
        <textarea
          id="description"
          name="description"
          rows="5"
          placeholder="Conte um pouco sobre o negócio (serviços, diferenciais, horário, contato)..."
          class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('description') border-red-500 @enderror"
        >{{ old('description') }}</textarea>
        @error('description')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Categoria (cid) --}}
        <div>
          <label for="cid" class="block text-sm font-medium text-slate-700">Categoria</label>
          <select
            id="cid"
            name="cid"
            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('cid') border-red-500 @enderror"
          >
            <option value="">Selecione…</option>
            @foreach(($cats ?? []) as $c)
              @php $id = $c->cat_id ?? $c->id ?? null; @endphp
              <option value="{{ $id }}" @selected(old('cid')==$id)>{{ $catLabel($c) }}</option>
            @endforeach
          </select>
          @error('cid')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        {{-- Cidade (sid) --}}
        <div>
          <label for="sid" class="block text-sm font-medium text-slate-700">Cidade</label>
          <select
            id="sid"
            name="sid"
            class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30 @error('sid') border-red-500 @enderror"
          >
            <option value="">Selecione…</option>
            @foreach(($cities ?? []) as $ci)
              @php $id = $ci->city_id ?? $ci->sid ?? $ci->id ?? null; @endphp
              <option value="{{ $id }}" @selected(old('sid')==$id)>{{ $cityLabel($ci) }}</option>
            @endforeach
          </select>
          @error('sid')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
      </div>

      {{-- Foto (upload + preview) --}}
      <div>
        <label for="image" class="block text-sm font-medium text-slate-700">Imagem (opcional)</label>

        <div class="mt-1 rounded-xl border-2 border-dashed border-slate-300 p-4">
          <input
            id="image"
            name="image"
            type="file"
            accept="image/png,image/jpeg,image/webp"
            class="block w-full text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-indigo-700"
          >
          <p class="mt-2 text-xs text-slate-500">Formatos aceitos: JPG, PNG, WEBP — até 2 MB.</p>

          {{-- Preview --}}
          <div id="image-preview-wrap" class="mt-3 hidden">
            <img id="image-preview" alt="Pré-visualização" class="h-40 rounded-lg object-cover border border-slate-200">
            <button type="button" id="image-clear"
                    class="ml-3 inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs hover:bg-slate-50">
              Remover
            </button>
          </div>
        </div>

        @error('image')<div class="mt-1 text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      {{-- Destacar no menu --}}
      <div class="flex items-center gap-2">
        <input id="menu" type="checkbox" name="menu" value="1"
               class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
               @checked(old('menu'))>
        <label for="menu" class="text-sm text-slate-700">Destacar no menu</label>
        @error('menu')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
      </div>

      {{-- Ações --}}
      <div class="pt-2 flex items-center gap-3">
        <button
          type="submit"
          class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
        >
          Salvar
        </button>

        <a href="{{ route('admin.businesses.index') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
          Cancelar
        </a>
      </div>
    </form>
  </div>
</div>

{{-- Preview JS (leve, sem libs) --}}
<script>
(function () {
  const input   = document.getElementById('image');
  const wrap    = document.getElementById('image-preview-wrap');
  const preview = document.getElementById('image-preview');
  const clear   = document.getElementById('image-clear');

  if (!input || !wrap || !preview || !clear) return;

  input.addEventListener('change', function () {
    const file = this.files && this.files[0] ? this.files[0] : null;
    if (!file) { wrap.classList.add('hidden'); preview.src = ''; return; }

    // validação rápida no front (opcional)
    if (!/image\/(png|jpeg|webp)/i.test(file.type)) {
      alert('Formato inválido. Use JPG, PNG ou WEBP.');
      this.value = '';
      wrap.classList.add('hidden');
      preview.src = '';
      return;
    }
    if (file.size > 2 * 1024 * 1024) { // 2MB
      alert('Arquivo muito grande. Máximo 2 MB.');
      this.value = '';
      wrap.classList.add('hidden');
      preview.src = '';
      return;
    }

    const url = URL.createObjectURL(file);
    preview.src = url;
    wrap.classList.remove('hidden');
  });

  clear.addEventListener('click', function () {
    input.value = '';
    preview.src = '';
    wrap.classList.add('hidden');
  });
})();
</script>
@endsection
