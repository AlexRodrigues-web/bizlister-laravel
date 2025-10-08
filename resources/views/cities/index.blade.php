@extends("layouts.app")

@section("content")
<div class="max-w-4xl mx-auto px-4 py-8">@if(($cities instanceof \Illuminate\Contracts\Pagination\Paginator) || ($cities instanceof \Illuminate\Pagination\LengthAwarePaginator))
      @if($cities->count() > 0)
        
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

        <div class="mt-6">
          {{ $cities->withQueryString()->links() }}
        </div>
      @else
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">
          Nenhuma cidade cadastrada.
        </div>
      @endif

  @else
      @if(is_countable($cities) && count($cities) > 0)
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
      @else
        <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">
          Nenhuma cidade cadastrada.
        </div>
      @endif
  @endif
</div>
@endsection