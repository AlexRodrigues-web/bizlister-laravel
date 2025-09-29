@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">
        {{ $category->category ?? 'Categoria' }}
    </h1>

    @if($businesses->count())
        <ul class="divide-y bg-white rounded shadow">
            @foreach($businesses as $biz)
                <li class="p-4">
                    <div class="font-semibold text-lg">
                        {{ $biz->business_name ?? ('Negócio #'.$biz->biz_id) }}
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
        <p>Nenhum negócio nesta categoria.</p>
    @endif

    <div class="mt-6">
        <a href="{{ route('categories.index') }}" class="text-blue-600 hover:underline">&larr; Voltar às categorias</a>
    </div>
</div>
@endsection
