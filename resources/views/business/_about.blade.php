@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));
  $img = null;

  if ($__biz) {
    $candidates = [
      $__biz->image ?? null,
      $__biz->photo ?? null,
      $__biz->logo  ?? null,
      ($__biz->biz_id ?? null) ? ("business/{$__biz->biz_id}.jpg") : null,
      ($__biz->biz_id ?? null) ? ("business/{$__biz->biz_id}.png") : null,
    ];
    foreach ($candidates as $p) {
      if (!$p) continue;
      if (is_string($p) && Str::startsWith($p, ['http://','https://'])) { $img = $p; break; }
      if (Storage::disk('public')->exists($p)) { $img = Storage::url($p); break; }
      if (file_exists(public_path($p))) { $img = url($p); break; }
    }
  }
  if (!$img) {
    $name = trim($__biz->business_name ?? 'Negócio');
    $img  = "https://ui-avatars.com/api/?background=0F172A&color=fff&name=".urlencode($name)."&size=512";
  }
@endphp

@if($__biz)
<!-- ABOUT_BLOCK_START -->
<section class="mt-6">
  <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="grid gap-5 md:grid-cols-3">
      <div class="md:col-span-1">
        <div class="aspect-[4/3] overflow-hidden rounded-xl bg-slate-100">
          <img src="{{ $img }}" alt="Imagem de {{ $__biz->business_name ?? 'Negócio' }}" class="h-full w-full object-cover">
        </div>
      </div>
      <div class="md:col-span-2">
        <div class="flex flex-wrap gap-2 text-sm text-slate-600">
          @if(!empty($__biz->category))
            <span class="inline-flex items-center rounded-full border border-slate-300 px-2.5 py-1">Categoria: {{ $__biz->category }}</span>
          @endif
          @if(!empty($__biz->city))
            <span class="inline-flex items-center rounded-full border border-slate-300 px-2.5 py-1">Cidade: {{ $__biz->city }}</span>
          @endif
          @if(!empty($__biz->state))
            <span class="inline-flex items-center rounded-full border border-slate-300 px-2.5 py-1">UF: {{ $__biz->state }}</span>
          @endif
        </div>

        @php
          $about = $__biz->description ?? $__biz->about ?? $__biz->short_description ?? null;
        @endphp

        @if($about)
          <h2 class="mt-4 text-lg font-semibold text-slate-800">Sobre o negócio</h2>
          <p class="mt-1 whitespace-pre-line leading-relaxed text-slate-700">{{ $about }}</p>
        @endif
      </div>
    </div>
  </div>
</section>
<!-- ABOUT_BLOCK_END -->
@endif
