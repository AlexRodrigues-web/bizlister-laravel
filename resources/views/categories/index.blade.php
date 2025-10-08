@extends("layouts.app")

@section("content")
<div class="max-w-4xl mx-auto px-4 py-8">
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-6">Categorias</h1>

  @php
    $isPaginator = ($categories ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator
                || ($categories ?? null) instanceof \Illuminate\Pagination\LengthAwarePaginator;
  @endphp

  @if(($isPaginator && $categories->count()) || (!$isPaginator && is_countable($categories ?? []) && count($categories ?? [])>0))
    <ul class="bg-white rounded-2xl border border-slate-200 divide-y divide-slate-100 overflow-hidden">
      @foreach($categories as $c)
        @php($name = $c->category ?? $c->cat_name ?? '')
        @php($slug = \Illuminate\Support\Str::slug($name))
        <li>
          <a href="{{ route('categories.show', [$c->cat_id ?? $c->id ?? 0, $slug]) }}"
             class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 transition">
            <span class="text-slate-800">{{ $name }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        </li>
      @endforeach
    </ul>

    @if($isPaginator)
      <div class="mt-6">
        {{ $categories->withQueryString()->links() }}
      </div>
    @endif
  @else
    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">
      Nenhuma categoria cadastrada.
    </div>
  @endif
</div>
@endsection