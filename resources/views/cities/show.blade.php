@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">
        {{ $city->city ?? 'Cidade' }}
    </h1>

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
