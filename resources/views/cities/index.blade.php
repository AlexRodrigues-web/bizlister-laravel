{{-- PUBLIC_SKIN_TOP --}}
@extends('layouts.app', ['title' => ($pageTitle ?? ($title ?? (View::shared('title') ?? 'Cidades')))])

@section('content')
<div class="container py-4">

  {{-- Cabeçalho + busca --}}
  <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
    <div>
      <h1 class="h3 fw-bold mb-1">
        {{ $pageTitle ?? ($title ?? (View::shared('title') ?? 'Cidades')) }}
      </h1>
      <p class="text-muted mb-0">Encontre cidades e veja os negócios locais.</p>
    </div>

    <form method="GET" action="{{ route('cities.index') }}" class="w-100 w-md-auto" role="search">
      <div class="input-group">
        <input
          type="text"
          name="q"
          value="{{ request('q') }}"
          class="form-control"
          placeholder="Pesquisar..."
          aria-label="Pesquisar cidades"
        >
        <button class="btn btn-primary" type="submit">Pesquisar</button>
      </div>
    </form>
  </div>

  @php
    // Normaliza a fonte de dados
    $items = $cities ?? $all ?? $data ?? collect();
    if (is_array($items)) { $items = collect($items); }

    // Saber se é paginator para exibir links()
    $isPaginator = $items instanceof \Illuminate\Contracts\Pagination\Paginator
                || $items instanceof \Illuminate\Pagination\LengthAwarePaginator;

    // Helpers de leitura
    $getId = fn($c)    => is_object($c) ? ($c->city_id ?? $c->id ?? null) : ($c['city_id'] ?? $c['id'] ?? null);
    $getVal= fn($c,$k) => is_object($c) ? ($c->{$k} ?? null) : ($c[$k] ?? null);

    $countItems = function($items){
      try {
        return method_exists($items,'count') ? $items->count() : (is_countable($items) ? count($items) : 0);
      } catch (\Throwable $e) {
        return 0;
      }
    };
  @endphp

  @if($countItems($items) === 0)
    <div class="alert alert-secondary mb-0">Nenhuma cidade cadastrada.</div>
  @else
    <div class="card">
      <ul class="list-group list-group-flush">
        @foreach($items as $c)
          @php
            $id = $getId($c);

            // PRIORIDADE: usar a coluna `city`; fallback para `name` somente se não for numérico
            $rawCity = $getVal($c,'city');
            if (!is_string($rawCity) || trim($rawCity) === '') {
              $alt = $getVal($c,'name');
              $rawCity = (is_string($alt) && !preg_match('/^-?\d+$/', trim($alt))) ? $alt : null;
            }
            $name = $rawCity ? trim($rawCity) : ($id ? "Cidade #{$id}" : 'Cidade');

            // slug do banco, senão gera
            $slug = $getVal($c,'slug') ?: \Illuminate\Support\Str::slug($name);

            // URL padrão id + slug
            $url  = $id ? route('cities.show', ['id' => $id, 'slug' => $slug]) : '#';
          @endphp

          <li class="list-group-item">
            <a href="{{ $url }}" class="d-flex align-items-center justify-content-between text-decoration-none">
              <span class="text-dark">{{ $name }}</span>
              <span class="text-muted small">Ver</span>
            </a>
          </li>
        @endforeach
      </ul>
    </div>

    {{-- Paginação (se for paginator) --}}
    @if($isPaginator && method_exists($items, 'links'))
      <div class="mt-3">
        {{ $items->withQueryString()->links() }}
      </div>
    @endif
  @endif
</div>
{{-- /PUBLIC_SKIN_TOP --}}
@endsection
