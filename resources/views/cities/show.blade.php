@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">
        {{ $city->city ?? 'Cidade' }}
    </h1>
{{-- Filtros --}}
<form method="GET" action="{{ route('cities.show', ['id' => $city->city_id, 'slug' => \Illuminate\Support\Str::slug($city->city)]) }}" class="mb-6">
    <div class="flex flex-wrap items-end gap-3">
        {{-- Termo --}}
        <div>
            <label class="block text-sm font-medium mb-1" for="q">Buscar</label>
            <input
                id="q"
                type="text"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                class="border rounded px-3 py-2"
                placeholder="Digite um termo (ex: hotel, padaria)…">
        </div>

        {{-- Categoria --}}
        <div>
            <label class="block text-sm font-medium mb-1" for="categoria">Categoria</label>
            <select id="categoria" name="categoria" class="border rounded px-3 py-2">
                <option value="">Todas</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->cat_id }}" {{ ($filters['categoria'] ?? null) == $cat->cat_id ? 'selected' : '' }}>
                        {{ $cat->category }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Filtrar
            </button>
            @if(($filters['q'] ?? null) || ($filters['categoria'] ?? null))
                <a href="{{ route('cities.show', ['id' => $city->city_id, 'slug' => \Illuminate\Support\Str::slug($city->city)]) }}"
                   class="ml-2 px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Limpar
                </a>
            @endif
        </div>
    </div>
</form>

<p class="text-sm text-gray-600 mb-4">
    Resultados: <strong>{{ $businesses->total() }}</strong>
</p>

    @if($businesses->count())
        <ul class="divide-y bg-white rounded shadow">
            @foreach($businesses as $biz)
                @php
                    $titulo = $biz->business_name ?? ('Negócio #'.$biz->biz_id);
                    $slug = \Illuminate\Support\Str::slug($titulo);
                @endphp
                <li class="p-4">
                    <div class="font-semibold text-lg">
                        <a class="text-blue-600 hover:underline"
                           href="{{ route('business.show', ['id' => $biz->biz_id, 'slug' => $slug]) }}">
                            {{ $titulo }}
                        </a>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ \Illuminate\Support\Str::limit(strip_tags($biz->description ?? ''), 140) }}
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {{ $businesses->links() }}
        </div>
    @else
        <p>Nenhum negócio nesta cidade.</p>
    @endif

    <div class="mt-6">
        <a href="{{ route('cities.index') }}" class="text-blue-600 hover:underline">&larr; Voltar às cidades</a>
    </div>
</div>
@endsection
