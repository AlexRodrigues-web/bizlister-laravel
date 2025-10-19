@extends('layouts.admin')
@section('content')
@include('admin._back_public')

<div class="p-6 max-w-6xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">NegÃ³cios</h1>

  @if(session('success'))
    <div class="p-3 bg-green-100 text-green-800 mb-3">{{ session('success') }}</div>
  @endif

  <form method="get" class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-2">
    <input name="q" value="{{ $q }}" class="border rounded px-3 py-2" placeholder="Termo">

    <select name="cid" class="border rounded px-3 py-2">
      <option value="">Todas categorias</option>
      @foreach($cats as $c)
        <option value="{{ $c->cat_id }}" {{ (string)$cid === (string)$c->cat_id ? 'selected' : '' }}>
          {{ $c->category }}
        </option>
      @endforeach
    </select>

    <select name="sid" class="border rounded px-3 py-2">
      <option value="">Todas cidades</option>
      @foreach($cities as $c)
        <option value="{{ $c->city_id }}" {{ (string)$sid === (string)$c->city_id ? 'selected' : '' }}>
          {{ $c->city }}
        </option>
      @endforeach
    </select>

    <button class="px-4 py-2 border rounded">Filtrar</button>
  </form>

  <div class="border rounded overflow-x-auto">
    <table class="w-full">
      <tr class="border-b">
        <th class="p-2 text-left">ID</th>
        <th class="p-2 text-left">Nome</th>
        <th class="p-2 text-left">Categoria</th>
        <th class="p-2 text-left">Cidade</th>
        <th class="p-2 text-right">AÃ§Ãµes</th>
      </tr>
      @forelse($rows as $r)
        <tr class="border-b">
          <td class="p-2">{{ $r->biz_id }}</td>
          <td class="p-2">{{ $r->business_name }}</td>
          <td class="p-2">{{ $r->category_name }}</td>
          <td class="p-2">{{ $r->city_name }}</td>
          <td class="p-2 text-right">
            <a class="px-2 py-1 border rounded" href="{{ route('admin.businesses.edit',$r->biz_id) }}">Editar</a>
            <form action="{{ route('admin.businesses.destroy',$r->biz_id) }}" method="post" class="inline" onsubmit="return confirm('Remover?')">
              @csrf @method('DELETE')
              <button class="px-2 py-1 border rounded">Remover</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="5" class="p-3">Nenhum registro.</td></tr>
      @endforelse
    </table>
  </div>

  <div class="mt-3">{{ $rows->links() }}</div>
</div>
@endsection