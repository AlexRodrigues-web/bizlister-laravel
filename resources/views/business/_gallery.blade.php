@php
  $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));
  $images = collect();
  if ($__biz) {
    if (method_exists($__biz, "images")) {
      $images = $__biz->images()->take(6)->get()->map(function($img){
        // tenta resolver colunas usuais
        $path = $img->path ?? $img->url ?? $img->image ?? null;
        if (!$path) return null;
        if (str_starts_with($path,'http://') || str_starts_with($path,'https://')) return $path;
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) return \Illuminate\Support\Facades\Storage::url($path);
        if (file_exists(public_path($path))) return url($path);
        return null;
      })->filter();
    }
  }
@endphp

@if($__biz && $images->count() > 0)
<section class="mt-6">
  <h2 class="mb-2 text-lg font-semibold text-slate-800">Galeria</h2>
  <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
    @foreach($images as $src)
      <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
        <img src="{{ $src }}" alt="Imagem do negócio" class="h-40 w-full object-cover md:h-48">
      </div>
    @endforeach
  </div>
</section>
@endif