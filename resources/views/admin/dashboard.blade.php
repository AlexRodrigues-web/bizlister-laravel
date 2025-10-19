@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-6">Admin</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">Cidades</div>
            <div class="text-3xl font-bold">{{ $totCities ?? '-' }}</div>
            <div class="mt-3 flex gap-2">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.cities.index') }}">Listar</a>
                <a class="btn btn-sm" href="{{ route('admin.cities.create') }}">Novo</a>
            </div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">Categorias</div>
            <div class="text-3xl font-bold">{{ $totCategories ?? '-' }}</div>
            <div class="mt-3 flex gap-2">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.categories.index') }}">Listar</a>
                <a class="btn btn-sm" href="{{ route('admin.categories.create') }}">Nova</a>
            </div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">NegÃƒÂ³cios</div>
            <div class="text-3xl font-bold">{{ $totBusinesses ?? '-' }}</div>
            <div class="mt-3 flex gap-2">
                <a class="btn btn-sm btn-primary" href="{{ route('admin.businesses.index') }}">Listar</a>
                {{-- <a class="btn btn-sm" href="{{ route('admin.businesses.create') }}">Novo</a> --}}
            </div>
        </div>
        <div class="p-4 border rounded">
            <div class="text-sm text-gray-500">AÃƒÂ§ÃƒÂµes rÃƒÂ¡pidas</div>
            <div class="mt-3 flex flex-col gap-2">
                <a class="btn btn-sm" href="{{ route('business.create') }}">Cadastrar NegÃƒÂ³cio (pÃƒÂºblico)</a>
                <a class="btn btn-sm" href="{{ route('search.index') }}">Buscar NegÃƒÂ³cios</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.categories.index') }}" class="p-4 border rounded hover:bg-gray-50">
            <div class="font-semibold">Gerir Categorias</div>
            <div class="text-sm text-gray-600">Listar, criar, editar, remover</div>
        </a>
        <a href="{{ route('admin.cities.index') }}" class="p-4 border rounded hover:bg-gray-50">
            <div class="font-semibold">Gerir Cidades</div>
            <div class="text-sm text-gray-600">Listar, criar, editar, remover</div>
        </a>
        <a href="{{ route('admin.businesses.index') }}" class="p-4 border rounded hover:bg-gray-50">
            <div class="font-semibold">Gerir NegÃƒÂ³cios</div>
            <div class="text-sm text-gray-600">Listar e editar/remover</div>
        </a>
    </div>
</div>
@endsection
