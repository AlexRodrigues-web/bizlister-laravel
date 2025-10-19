{{-- PUBLIC_UI_V2_MARK --}}
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8">
  @php
    $catName = $category->category ?? $category->cat_name ?? $category->name ?? $category->label ?? "Categoria";
  @endphp

  <header class="mb-6 flex items-end justify-between gap-4">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">{{ $catName }}</h1>
      <p class="text-slate-600 text-sm">Negócios nessa categoria.</p>
    </div>
    <a href="{{ route('categories.index') }}" class="text-sm rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-50">← Todas categorias</a>
  </header>

  @php $list = $businesses ?? $items ?? collect(); @endphp

  @if($list->count() === 0)
    <div class="rounded-xl border border-slate-200 bg-white p-6 text-slate-600">Nenhum negócio nesta categoria.</div>
  @else
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      @foreach($list as $biz)
        @php
          $img = null;
          $cands = [
            $biz->image_lg ?? null,
            $biz->image ?? null,
            ($biz->biz_id ?? null) ? ('businesses/'.($biz->image ?? ''.$biz->biz_id.'.jpg')) : null,
          ];
          foreach ($cands as $p) {
            if (!$p) continue;
            if (is_string($p) && (\Illuminate\Support\Str::startsWith($p, ['http://','https://']))) { $img = $p; break; }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($p)) { $img = \Illuminate\Support\Facades\Storage::url($p); break; }
            if (file_exists(public_path($p))) { $img = url($p); break; }
          }
          if (!$img) $img = asset('images/placeholder-800x600.png');

          $name = $biz->business_name ?? 'Negócio';
          $desc = $biz->short_description ?? $biz->description ?? '';
          $url  = route('business.show', [$biz->biz_id, \Illuminate\Support\Str::slug($name)]);
        @endphp

        <a href="{{ $url }}" class="block rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition">
          <div class="aspect-[4/3] overflow-hidden rounded-t-2xl bg-slate-100">
            <img src="{{ $img }}" alt="Imagem de {{ $name }}" class="h-full w-full object-cover">
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-slate-800">{{ $name }}</h3>
            @if($desc)
              <p class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $desc }}</p>
            @endif
            <span class="mt-3 inline-block text-sm text-slate-700 underline">Ver detalhes</span>
          </div>
        </a>
      @endforeach
    </div>
  @endif
</div>
@endsection