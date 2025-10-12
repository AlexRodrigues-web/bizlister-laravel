<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl leading-tight">Admin — Cidades</h2>
  </x-slot>

  <div class="py-6 max-w-6xl mx-auto">
    <div class="mb-4">
      <a href="{{ route('admin.cities.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">Nova Cidade</a>
      <a href="{{ route('admin.dashboard') }}" class="ml-2 px-3 py-2 bg-gray-200 rounded">Voltar ao Admin</a>
    </div>

    @php
      $items = $items ?? collect();
      $list  = ($items instanceof \Illuminate\Contracts\Pagination\Paginator) ? $items->getCollection() : collect($items);
      $first = $list->first();

      $allCols  = $cols ?? [];
      $hasCol = function(string $c) use ($allCols,$first){
        return in_array($c,$allCols,true) || (is_object($first) && property_exists($first,$c));
      };
      $pick = function(array $cands) use ($hasCol){
        foreach($cands as $c){ if($hasCol($c)) return $c; }
        return null;
      };

      $pkCol    = $pk ?? 'id';
      $nameCol  = $pick(['city','name','title']);
      $ufCol    = $pick(['uf','state']);
      $slugCol  = $pick(['slug']);
    @endphp>

    <div class="bg-white shadow rounded overflow-x-auto">
      <table class="min-w-full">
        <thead>
          <tr class="border-b bg-gray-50">
            <th class="p-3 text-left">ID</th>
            <th class="p-3 text-left">Cidade</th>
            <th class="p-3 text-left">UF/Estado</th>
            <th class="p-3 text-left">Slug</th>
            <th class="p-3 text-left">Ações</th>
          </tr>
        </thead>
        <tbody>
          @forelse($list as $row)
            @php
              $id   = $row->{$pkCol}   ?? null;
              $nome = $nameCol ? ($row->{$nameCol} ?? null) : null;
              $uf   = $ufCol ? ($row->{$ufCol} ?? null) : null;
              $slug = $slugCol ? ($row->{$slugCol} ?? null) : null;
            @endphp
            <tr class="border-b">
              <td class="p-3">{{ $id }}</td>
              <td class="p-3">{{ $nome ?? '(sem nome)' }}</td>
              <td class="p-3">{{ $uf ?? '-' }}</td>
              <td class="p-3">{{ $slug ?? '-' }}</td>
              <td class="p-3 space-x-2">
                <a href="{{ route('admin.cities.edit', $id) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Editar</a>
                <form action="{{ route('admin.cities.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Remover cidade?');">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 bg-red-600 text-white rounded">Remover</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td class="p-3" colspan="5">Nenhuma cidade encontrada.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
      <div class="mt-4">{{ $items->links() }}</div>
    @endif
  </div>
</x-app-layout>