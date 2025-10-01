@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">
        {{ $category->category ?? 'Categoria' }}
    </h1>

    @if($businesses->count() > 0)
        <ul class="space-y-4">
            @foreach($businesses as $biz)
                @php
                    $slug = \Illuminate\Support\Str::slug($biz->business_name ?? ('negocio-'.$biz->biz_id));
                @endphp
                <li class="p-4 border rounded-lg hover:bg-gray-50">
                    <a class="text-blue-600 hover:underline text-lg font-semibold"
                       href="{{ route('business.show', ['id' => $biz->biz_id, 'slug' => $slug]) }}">
                        {{ $biz->business_name ?? ('Negócio #'.$biz->biz_id) }}
                    </a>

                    @if(!empty($biz->short_description ?? ''))
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $biz->short_description }}
                        </p>
                    @endif

                    <div class="text-xs text-gray-500 mt-1">
                        Cidade:
                        <a class="hover:underline"
                           href="{{ route('cities.show', ['id' => $biz->sid, 'slug' => \Illuminate\Support\Str::slug($biz->city ?? '')]) }}">
                           {{ $biz->city ?? '—' }}
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">
            {{ $businesses->withQueryString()->links() }}
        </div>
    @else
        <div class="p-6 border rounded-lg bg-yellow-50">
            Nenhum negócio nesta categoria.
        </div>
    @endif

    <div class="mt-6">
        <a class="text-sm text-gray-600 hover:underline" href="{{ route('categories.index') }}">← Voltar às categorias</a>
    </div>
</div>
@endsection
