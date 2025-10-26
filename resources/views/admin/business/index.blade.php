@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-6xl p-6">
  {{-- Cabeçalho + ação principal --}}
  <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-bold tracking-tight text-slate-900">Negócios</h1>

    @if (Route::has('business.create'))
      <a href="{{ route('business.create') }}"
         class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
        + Novo Negócio (público)
      </a>
    @endif
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

  {{-- Tabela --}}
  <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white shadow-sm">
    <table class="min-w-full divide-y divide-slate-200">
      <thead class="bg-slate-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">ID</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Negócio</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Cidade</th>
          <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Categoria</th>
          <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-600">Ações</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @forelse($items as $it)
          @php
            // Normalizações leves (sem criar nada novo)
            $id    = $it->biz_id ?? $it->id ?? null;
            $name  = trim((string)($it->business_name ?? ''));
            $city  = trim((string)($it->city_name ?? $it->cidade ?? $it->city ?? ''));
            $cat   = trim((string)($it->category_name ?? $it->cat_name ?? $it->category ?? $it->cid ?? ''));
          @endphp

          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 text-sm text-slate-700">{{ $id }}</td>

            <td class="px-4 py-3">
              <div class="text-sm font-medium text-slate-900">{{ $name ?: '—' }}</div>
              @if(!empty($it->created_at))
                <div class="text-xs text-slate-500">Criado em {{ \Carbon\Carbon::parse($it->created_at)->format('d/m/Y H:i') }}</div>
              @endif
            </td>

            <td class="px-4 py-3 text-sm text-slate-700">
              {{ $city ?: '—' }}
            </td>

            <td class="px-4 py-3 text-sm">
              <span class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2 py-0.5 text-slate-700">
                {{ $cat ?: '—' }}
              </span>
            </td>

            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                @if($id)
                  <a href="{{ route('admin.businesses.edit', $id) }}"
                     class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-indigo-700">
                    Editar
                  </a>

                  <form action="{{ route('admin.businesses.destroy', $id) }}"
                        method="POST"
                        onsubmit="return confirm('Remover este negócio?');"
                        class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center rounded-lg bg-red-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700">
                      Excluir
                    </button>
                  </form>
                @else
                  <span class="text-xs text-slate-400">Sem ID</span>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-4 py-6">
              <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
                Nenhum negócio encontrado.
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginação --}}
  <div class="mt-4">
    {{ $items->withQueryString()->links() }}
  </div>
</div>
@endsection
