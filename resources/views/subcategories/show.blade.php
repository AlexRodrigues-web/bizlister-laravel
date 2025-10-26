@extends('layouts.app')

@php
    /** @var \App\Models\Subcategory $subcategory */
    /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator|\App\Models\Business[] $businesses */
    use Illuminate\Support\Str;

    $cat = $subcategory->category; // pode vir null no legado

    // Nome da categoria com fallbacks (legado)
    $catName = $cat->name
        ?? ($cat->category_name ?? null)
        ?? ($cat->category ?? null)
        ?? ($cat->label ?? null)
        ?? 'Categoria';

    // Descobre dinamicamente a PK correta (id ou cat_id)
    $catKeyName = ($cat && method_exists($cat, 'getKeyName')) ? $cat->getKeyName() : 'id';
    $catId      = $cat->{$catKeyName} ?? null;

    $catSlug = $cat ? Str::slug($catName) : null;
@endphp

@section('title', $subcategory->name . ($cat ? ' | ' . $catName : ''))

@section('content')
<div class="container py-4">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Início</a></li>
      <li class="breadcrumb-item"><a href="{{ route('categories.index') }}">Categorias</a></li>
      @if($catId)
        <li class="breadcrumb-item">
          <a href="{{ route('categories.show', ['id' => $catId, 'slug' => $catSlug]) }}">{{ $catName }}</a>
        </li>
      @endif
      <li class="breadcrumb-item active" aria-current="page">{{ $subcategory->name }}</li>
    </ol>
  </nav>

  <header class="mb-3">
    <h1 class="h3 mb-1">{{ $subcategory->name }}</h1>
    @if($catId)
      <p class="text-muted mb-0">
        Em <a href="{{ route('categories.show', ['id' => $catId, 'slug' => $catSlug]) }}">{{ $catName }}</a>
      </p>
    @endif
  </header>

  @if(!empty($subcategory->description))
    <div class="mb-4">
      <p class="mb-0">{{ $subcategory->description }}</p>
    </div>
  @endif

  @if($businesses->count())
    <div class="row">
      @foreach($businesses as $biz)
        <div class="col-md-4 mb-3">
          @include('components.biz-card', ['biz' => $biz])
        </div>
      @endforeach
    </div>

    {{-- Paginação (preserva query string para futuros filtros) --}}
    <div class="mt-3">
      {{ $businesses->withQueryString()->links() }}
    </div>
  @else
    <div class="alert alert-info">
      Não há negócios nesta subcategoria ainda.
      @auth
        <a href="{{ route('business.create') }}" class="alert-link">Seja o primeiro a cadastrar</a>.
      @endauth
    </div>
  @endif

</div>
@endsection
