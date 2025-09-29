@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-4">
    <h1 class="text-2xl font-bold mb-6">Categorias</h1>

    <ul class="space-y-2">
        @foreach($categories as $cat)
            @php
                $nome = $cat->category ?? ('Categoria #'.$cat->cat_id);
                $slug = \Illuminate\Support\Str::slug($nome);
            @endphp
            <li>
                <a class="text-blue-600 hover:underline"
                   href="{{ route('categories.show', ['id' => $cat->cat_id, 'slug' => $slug]) }}">
                    {{ $nome }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
