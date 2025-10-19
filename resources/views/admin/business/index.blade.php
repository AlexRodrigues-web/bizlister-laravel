@extends('layouts.admin')
@section("content")
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">NegÃ³cios</h1>
  @if(session("success")) <p class="mt-2 text-green-700">{{ session("success") }}</p> @endif

  <table class="w-full mt-4 border">
    <thead>
      <tr class="bg-gray-100">
        <th class="p-2 text-left">ID</th>
        <th class="p-2 text-left">Nome</th>
        <th class="p-2 text-left">Cidade</th>
        <th class="p-2 text-left">Categoria</th>
        <th class="p-2">AÃ§Ãµes</th>
      </tr>
    </thead>
    <tbody>
      @forelse($items as $it)
        <tr class="border-t">
          <td class="p-2">{{ $it->biz_id }}</td>
          <td class="p-2">{{ $it->business_name }}</td>
          <td class="p-2">{{ $it->city }}</td>
          <td class="p-2">{{ $it->cid }}</td>
          <td class="p-2 text-right">
            <a class="px-2 py-1 border rounded" href="{{ route('admin.businesses.edit',$it->biz_id) }}">Editar</a>
            <form action="{{ route('admin.businesses.destroy',$it->biz_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Remover?')">
              @csrf @method('DELETE')
              <button class="px-2 py-1 border rounded text-red-700">Excluir</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td class="p-2" colspan="5">Nenhum negÃ³cio.</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
