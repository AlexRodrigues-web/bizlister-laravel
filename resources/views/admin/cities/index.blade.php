@extends('layouts.admin')

@section('title', 'Cidades')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-6xl p-6">

  {{-- Breadcrumbs / Ações rápidas --}}
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500" aria-label="breadcrumb">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Cidades</span>
    </nav>

    @if (Route::has('admin.cities.create'))
      <a href="{{ route('admin.cities.create') }}"
         class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        <span aria-hidden="true">＋</span> Nova Cidade
      </a>
    @endif
  </div>

  {{-- Cabeçalho --}}
  <header class="mb-5">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">Cidades</h1>
    <p class="mt-1 text-sm text-slate-500">Gerencie as cidades cadastradas no diretório.</p>
  </header>

  {{-- Flash messages --}}
  @if (session('success') || session('status'))
    <div class="mb-4 rounded-xl border border-green-200 bg-green-50 p-3 text-green-800" role="alert">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-3 text-red-800" role="alert">
      {{ session('error') }}
    </div>
  @endif

  @php
    // Fonte pode vir como $items ou $cities
    $items = $items ?? ($cities ?? collect());
    if (is_array($items)) { $items = collect($items); }

    // PK padrão
    $pk = $pk ?? 'city_id';

    // Paginação
    $isPager = ($items instanceof \Illuminate\Contracts\Pagination\Paginator)
            || ($items instanceof \Illuminate\Pagination\LengthAwarePaginator);

    // Mostrar UF?
    $hasUf = $hasUf
      ?? ($items && ($items instanceof \Illuminate\Support\Collection ? $items->count() : (is_countable($items) ? count($items) : 0)) > 0
          && ( (is_object($items->first()) && (isset($items->first()->uf) || isset($items->first()->state)))
            || (is_array($items->first()) && (array_key_exists('uf', $items->first()) || array_key_exists('state', $items->first()))) )
      );

    // Contagem segura
    $countItems = function($it){
      try { return method_exists($it,'count') ? $it->count() : (is_countable($it) ? count($it) : 0); }
      catch (\Throwable $e) { return 0; }
    };
  @endphp

  @if ( ($isPager && $items->count()) || (!$isPager && $countItems($items)) )

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-slate-200">
        <thead class="bg-slate-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">ID</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Cidade</th>
            @if($hasUf)
              <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">UF</th>
            @endif
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          @foreach($items as $row)
            @php
              // Acessador seguro
              $get = function($k) use ($row) {
                return is_object($row) ? ($row->{$k} ?? null) : (is_array($row) ? ($row[$k] ?? null) : null);
              };

              $id   = $get($pk) ?? $get('city_id') ?? $get('id');

              // Prioriza 'city'; depois 'name' e 'label' se forem textos válidos
              $candidate = $get('city');
              if (!is_string($candidate) || trim($candidate) === '') {
                $nm = $get('name');
                $lb = $get('label');
                $candidate = (is_string($nm) && !preg_match('/^-?\d+$/', trim($nm))) ? $nm
                           : (is_string($lb) && !preg_match('/^-?\d+$/', trim($lb)) ? $lb : null);
              }
              $name = $candidate ? trim($candidate) : ($id ? "Cidade #{$id}" : 'Cidade');

              $uf = $get('uf') ?? $get('state') ?? '';
            @endphp

            <tr class="hover:bg-slate-50">
              <td class="px-4 py-3 text-sm text-slate-700">{{ $id }}</td>
              <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $name }}</td>
              @if($hasUf)
                <td class="px-4 py-3 text-sm text-slate-700">{{ $uf }}</td>
              @endif
              <td class="px-4 py-3 text-sm">
                <div class="flex flex-wrap gap-2">
                  @if($id)
                    @if (Route::has('admin.cities.edit'))
                      <a href="{{ route('admin.cities.edit', ['city' => $id]) }}"
                         class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">
                        Editar
                      </a>
                    @endif

                    @if (Route::has('admin.cities.destroy'))
                      <form action="{{ route('admin.cities.destroy', ['city' => $id]) }}"
                            method="POST"
                            onsubmit="return confirm('Remover esta cidade?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-white hover:bg-red-700">
                          Excluir
                        </button>
                      </form>
                    @endif
                  @else
                    <span class="text-slate-400">Sem ID</span>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($isPager)
      <div class="mt-4">
        {{ $items->withQueryString()->links() }}
      </div>
    @endif

  @else
    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-6 text-yellow-800">
      Nenhuma cidade encontrada.
    </div>
  @endif
</div>
@endsection
