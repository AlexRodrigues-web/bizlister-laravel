@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="max-w-6xl mx-auto p-6">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold">Cidades</h1>
    <a href="{{ route('admin.cities.create') }}"
       class="inline-flex items-center px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700">
      + Nova Cidade
    </a>
  </div>

  {{-- Flash messages --}}
  @if (session('success') || session('status'))
    <div class="mb-4 p-3 rounded border border-green-200 bg-green-50 text-green-800" role="alert">
      {{ session('success') ?? session('status') }}
    </div>
  @endif

  @if (session('error'))
    <div class="mb-4 p-3 rounded border border-red-200 bg-red-50 text-red-800" role="alert">
      {{ session('error') }}
    </div>
  @endif

  @php
    // Esperamos $items (Paginator ou Collection) e $pk com nome da PK (ex.: city_id)
    $items    = $items ?? ($cities ?? collect());
    $pk       = $pk ?? 'city_id';
    $isPager  = ($items instanceof \Illuminate\Contracts\Pagination\Paginator)
             || ($items instanceof \Illuminate\Pagination\LengthAwarePaginator);
    $hasUf    = $hasUf ?? false; // vem do controller
  @endphp

  @if (($isPager && $items->count()) || (!$isPager && count($items)))
  <div class="overflow-x-auto rounded-lg shadow bg-white">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cidade</th>
          @if($hasUf)
            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">UF</th>
          @endif
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">AÃ§Ãµes</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
        @foreach($items as $row)
          @php
            $id   = $row->{$pk} ?? $row->id ?? null;
            $name = $row->city ?? $row->name ?? ('#'.$id);
            $uf   = $row->uf ?? '';
            $rid  = $row->{$pk} ?? $row->city_id ?? $row->id;
          @endphp
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 text-sm text-gray-700">{{ $id }}</td>
            <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $name }}</td>
            @if($hasUf)
              <td class="px-4 py-3 text-sm text-gray-700">{{ $uf }}</td>
            @endif
            <td class="px-4 py-3 text-sm">
              <div class="flex gap-2">
                @if($rid)
                  <a href="{{ route('admin.cities.edit', ['city' => $rid]) }}"
                     class="px-3 py-1 rounded bg-indigo-600 text-white hover:bg-indigo-700">
                    Editar
                  </a>

                  <form action="{{ route('admin.cities.destroy', ['city' => $rid]) }}"
                        method="POST"
                        onsubmit="return confirm('Remover esta cidade?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-700">
                      Excluir
                    </button>
                  </form>
                @else
                  <span class="text-gray-400">Sem ID</span>
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
    <div class="p-6 rounded bg-yellow-50 text-yellow-800">Nenhuma cidade encontrada.</div>
  @endif
</div>
@endsection
