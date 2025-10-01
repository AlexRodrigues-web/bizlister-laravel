@extends("layouts.app")

@section("content")
<div class="container py-4">
  <h1 class="mb-3">Cidades</h1>

  @if($cities->count())
    <div class="list-group">
      @foreach($cities as $c)
        @php($slug = \Illuminate\Support\Str::slug($c->city))
        <a class="list-group-item list-group-item-action"
           href="{{ route('cities.show', [$c->city_id, $slug]) }}">
          {{ $c->city }}
        </a>
      @endforeach
    </div>

    <div class="mt-3">
      {{ $cities->withQueryString()->links() }}
    </div>
  @else
    <div class="alert alert-info">Nenhuma cidade cadastrada.</div>
  @endif
</div>
@endsection