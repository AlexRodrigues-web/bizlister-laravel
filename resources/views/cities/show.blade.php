@extends("layouts.app")

@section("content")
<div class="container py-4">
  <h1 class="mb-3">{{ $pageTitle }}</h1>

  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-6">
      <input name="q" value="{{ $q }}" class="form-control" placeholder="Buscar por nome ou descrição...">
    </div>
    <div class="col-md-4">
      <select name="categoria" class="form-select">
        <option value="">Todas as categorias</option>
        @foreach($categories as $c)
          <option value="{{ $c->cat_id }}" @selected($catId==$c->cat_id)>{{ $c->cat_name }}</option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-primary">Filtrar</button>
    </div>
  </form>

  <p class="text-muted mb-2">
    Resultados: <strong>{{ $businesses->total() }}</strong> em {{ $city->city }}
    @if($q) • termo: “{{ $q }}” @endif
    @if($catId) • categoria: #{{ $catId }} @endif
  </p>

  @if($businesses->count())
    <div class="row row-cols-1 row-cols-md-3 g-3">
      @foreach($businesses as $biz)
        <div class="col">@include('components.biz-card',['biz'=>$biz])</div>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $businesses->withQueryString()->links() }}
    </div>
  @else
    <div class="alert alert-info">Nenhum negócio encontrado com os filtros aplicados.</div>
  @endif

  <div class="mt-4">
    <a href="{{ route('cities.index') }}">&larr; Voltar para cidades</a>
  </div>
</div>
@endsection