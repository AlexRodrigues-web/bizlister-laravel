@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="max-w-6xl mx-auto p-6">

  {{-- Cabeçalho / Toolbar --}}
  <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div class="min-w-0">
      <h1 class="text-2xl font-bold tracking-tight text-slate-900">Categorias</h1>
      <p class="mt-0.5 text-sm text-slate-600">Gerencie as categorias do diretório.</p>
    </div>

    <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
      {{-- Busca (GET, mesma rota) --}}
      <form method="GET" action="{{ route('admin.categories.index') }}" class="flex items-stretch gap-2">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          placeholder="Buscar por nome…"
          class="w-full sm:w-60 rounded-lg border border-slate-300 px-3 py-2 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30"
          aria-label="Buscar categorias">
        <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
          Buscar
        </button>
      </form>

      @if(Route::has('admin.categories.create'))
        <a href="{{ route('admin.categories.create') }}"
           class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
          + Nova Categoria
        </a>
      @endif
    </div>
  </div>

  {{-- Flash messages --}}
  @if (session('success') || session('status'))
    <div role="alert" class="mb-4 rounded-lg border border-green-200 bg-green-50 p-3 text-green-800">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div role="alert" class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  @php
    // Fonte de dados: pode vir como $items ou $categories
    $items = $items ?? ($categories ?? collect());

    // Normaliza para Collection se vier array
    if (is_array($items)) { $items = collect($items); }

    // PK padrão (mantém legado)
    $pk = $pk ?? 'cat_id';

    // Detecta paginação
    $isPager = ($items instanceof \Illuminate\Contracts\Pagination\Paginator)
            || ($items instanceof \Illuminate\Pagination\LengthAwarePaginator);

    // Função contagem segura
    $countItems = function($src) use ($isPager) {
      try {
        if ($isPager) return $src->count();
        return $src instanceof \Illuminate\Support\Collection ? $src->count() : (is_countable($src) ? count($src) : 0);
      } catch (\Throwable $e) {
        return 0;
      }
    };

    $totalCount = $isPager
      ? (method_exists($items, 'total') ? (int)$items->total() : $countItems($items))
      : $countItems($items);
  @endphp

  {{-- Resumo (contagem + termo de busca) --}}
  <div class="mb-3 flex flex-wrap items-center gap-2 text-sm text-slate-600">
    <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-800 ring-1 ring-slate-200">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
      </svg>
      {{ number_format($totalCount, 0, ',', '.') }} resultado(s)
    </span>
    @if(filled(request('q')))
      <span>• termo: “{{ request('q') }}”</span>
    @endif
  </div>

  {{-- Tabela / Lista --}}
  @if ($countItems($items) > 0)
    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm">
      <table class="min-w-full divide-y divide-slate-200" role="table" aria-label="Tabela de categorias">
        <thead class="bg-slate-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">ID</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Categoria</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-600">Ações</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100 bg-white">
          @foreach($items as $row)
            @php
              $get = function ($k) use ($row) {
                return is_object($row) ? ($row->{$k} ?? null) : ($row[$k] ?? null);
              };

              $id = $get($pk) ?? $get('id');

              // PRIORIDADE de nome: category -> cat_name -> name (se não-numérico)
              $candidateName = $get('category') ?? $get('cat_name');
              if (is_null($candidateName)) {
                $nm = $get('name');
                if (is_string($nm) && trim($nm) !== '' && !preg_match('/^-?\d+$/', trim($nm))) {
                  $candidateName = $nm;
                }
              }
              $name = $candidateName ? trim($candidateName) : ($id ? 'Categoria #'.$id : 'Categoria');
            @endphp

            <tr class="hover:bg-slate-50" role="row">
              <td class="px-4 py-3 text-sm text-slate-700 align-top" role="cell">{{ $id }}</td>
              <td class="px-4 py-3 text-sm font-medium text-slate-900 align-top" role="cell">{{ $name }}</td>
              <td class="px-4 py-3 text-sm align-top" role="cell">
                <div class="flex flex-wrap gap-2">
                  @if($id && Route::has('admin.categories.edit'))
                    <a href="{{ route('admin.categories.edit', ['category' => $id]) }}"
                       class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">
                      Editar
                    </a>
                  @endif

                  @if($id && Route::has('admin.categories.destroy'))
                    <form action="{{ route('admin.categories.destroy', ['category' => $id]) }}"
                          method="POST"
                          onsubmit="return confirm('Excluir a categoria “{{ $name }}”? Esta ação não pode ser desfeita.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-white hover:bg-red-700">
                        Excluir
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Paginação (se houver) --}}
    @if($isPager && method_exists($items, 'links'))
      <div class="mt-4">
        {{ $items->withQueryString()->links() }}
      </div>
    @endif

  @else
    {{-- Empty state --}}
    <div class="rounded-xl border border-slate-200 bg-white p-8 text-center shadow-sm">
      <div class="mx-auto mb-3 h-10 w-10 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19v2H3v-2h18zM7 4h10a2 2 0 0 1 2 2v9H5V6a2 2 0 0 1 2-2zm0 2v7h10V6H7z"/></svg>
      </div>
      <p class="text-slate-700 font-medium">Nenhuma categoria encontrada.</p>
      <p class="text-slate-500 text-sm mt-1">Tente limpar filtros ou criar uma nova categoria.</p>
      @if(Route::has('admin.categories.create'))
        <div class="mt-4">
          <a href="{{ route('admin.categories.create') }}"
             class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            + Nova Categoria
          </a>
        </div>
      @endif
    </div>
  @endif
</div>
@endsection
