@extends('layouts.app')

@section('content')
{{-- PUBLIC_SKIN_TOP --}}
<div class="mx-auto max-w-6xl px-4 py-8">
  <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
      {{ $pageTitle ?? ($title ?? (View::shared('title') ?? 'Cidades')) }}
    </h1>
    <form method="get" action="" class="flex items-stretch gap-2">
      <input name="q" value="{{ request('q') }}" placeholder="Pesquisar..."
             class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/30">
      <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
        Pesquisar
      </button>
    </form>
  </div>{{-- BLOCO_MODERN_* DESABILITADO TEMPORARIAMENTE (mantendo legado ativo) --}}

<div class="max-w-4xl mx-auto px-4 py-8">
  @php
    $cities = $cities ?? collect();
    $isPaginator = ($cities instanceof \Illuminate\Contracts\Pagination\Paginator)
                || ($cities instanceof \Illuminate\Pagination\LengthAwarePaginator);
  @endphp

  @if( ($isPaginator && $cities->count() > 0)
    || (!$isPaginator && is_countable($cities) && count($cities) > 0) )

    <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">Cidades</h1>

    <ul class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
      @foreach($cities as $c)
        @php($slug = \Illuminate\Support\Str::slug($c->city))
        <li>
          <a href="{{ route('cities.show', [$c->city_id, $slug]) }}"
             class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">
            <span class="text-slate-800">{{ $c->city }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </li>
      @endforeach
    </ul>

    @if($isPaginator && method_exists($cities, 'links'))
      <div class="mt-6">
        {{ $cities->withQueryString()->links() }}
      </div>
    @endif

  @else
    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">
      Nenhuma cidade cadastrada.
    </div>
  @endif
</div>

</div>
{{-- /PUBLIC_SKIN_TOP --}}
@endsection
