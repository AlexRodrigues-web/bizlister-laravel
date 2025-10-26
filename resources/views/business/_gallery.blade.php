@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Str;

  // Normaliza a instância do negócio (compat com diferentes variáveis)
  $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));

  // Coleção final de URLs (máx. 9)
  $images = collect();

  // Helper: garante URL pública a partir de um "path"
  $toPublicUrl = function (?string $path) {
      if (!$path || !is_string($path)) return null;

      // URL absoluta
      if (Str::startsWith($path, ['http://', 'https://'])) {
          return $path;
      }

      // Storage público
      if (Storage::disk('public')->exists($path)) {
          return Storage::url($path);
      }

      // Arquivo dentro de /public
      if (file_exists(public_path($path))) {
          return url($path);
      }

      return null;
  };

  // 1) Preferência: relação images() SOMENTE se a tabela existir
  $canUseRelation =
      $__biz
      && method_exists($__biz, 'images')
      && Schema::hasTable('business_images'); // evita QueryException

  if ($canUseRelation) {
      try {
          $images = $__biz->images()
              ->orderByDesc('is_primary')
              ->orderBy('sort_order')
              ->orderBy('id')
              ->take(9)
              ->get()
              ->map(function ($img) use ($toPublicUrl) {
                  // campos comuns em tabelas de imagens
                  $cands = [
                      $img->path ?? null,
                      $img->url ?? null,
                      $img->image ?? null,
                      $img->filepath ?? null,
                  ];
                  foreach ($cands as $p) {
                      if (!$p) continue;
                      $u = $toPublicUrl($p);
                      if ($u) return $u;
                  }
                  return null;
              })
              ->filter()
              ->values();
      } catch (\Throwable $e) {
          // silencioso — caímos para os fallbacks
          $images = collect();
      }
  }

  // 2) Fallback por arquivos no storage/app/public/galleries/{biz_id}/*
  if ($images->isEmpty() && $__biz && isset($__biz->biz_id)) {
      $dir = "galleries/{$__biz->biz_id}";
      if (Storage::disk('public')->exists($dir)) {
          $files = collect(Storage::disk('public')->files($dir))
              ->filter(fn($f) => preg_match('/\.(jpe?g|png|webp)$/i', $f))
              ->take(9)
              ->map(fn($f) => Storage::url($f));
          if ($files->isNotEmpty()) $images = $files->values();
      }
  }

  // 3) Fallback por arquivos no storage/app/public/businesses/{biz_id}*
  if ($images->isEmpty() && $__biz && isset($__biz->biz_id)) {
      $prefix = "businesses/{$__biz->biz_id}";
      // tenta arquivos com vários sufixos
      $candidates = collect(Storage::disk('public')->files('businesses'))
          ->filter(function($f) use ($prefix) {
              return Str::startsWith($f, $prefix) &&
                     preg_match('/\.(jpe?g|png|webp)$/i', $f);
          })
          ->take(9)
          ->map(fn($f) => Storage::url($f));
      if ($candidates->isNotEmpty()) $images = $candidates->values();
  }

  // 4) Fallback por arquivos em /public/businesses (legado)
  if ($images->isEmpty() && $__biz && isset($__biz->biz_id)) {
      $pubDir = public_path('businesses');
      if (is_dir($pubDir)) {
          $all = glob($pubDir . DIRECTORY_SEPARATOR . $__.biz_id . '*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
          $urls = collect($all)
              ->take(9)
              ->map(function ($abs) {
                  $rel = 'businesses/' . basename($abs);
                  return url($rel);
              });
          if ($urls->isNotEmpty()) $images = $urls->values();
      }
  }

  // Nome para alt
  $bizName = (string)($__biz->business_name ?? 'Negócio');
@endphp

@if ($__biz && $images->count() > 0)
  <section class="gallery my-3">
    <h2 class="h6 mb-3">Galeria</h2>

    <div class="row g-3">
      @foreach ($images as $i => $src)
        @php
          $imgId = 'galleryImgModal_'.$i;
          $alt   = 'Imagem de '.$bizName;
        @endphp

        <div class="col-6 col-md-4">
          <a href="#{{ $imgId }}" class="text-decoration-none d-block card shadow-sm overflow-hidden"
             data-bs-toggle="modal" data-bs-target="#{{ $imgId }}" aria-label="Ampliar imagem">
            <div class="ratio ratio-4x3 bg-light">
              <img
                src="{{ $src }}"
                alt="{{ e($alt) }}"
                class="w-100 h-100"
                style="object-fit: cover;"
                loading="lazy"
                decoding="async"
                fetchpriority="low">
            </div>
          </a>
        </div>

        {{-- Modal --}}
        <div class="modal fade" id="{{ $imgId }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
              <button type="button" class="btn-close ms-auto me-2 mt-2" data-bs-dismiss="modal" aria-label="Fechar"></button>
              <div class="modal-body p-0">
                <img
                  src="{{ $src }}"
                  alt="{{ e($alt) }}"
                  class="w-100"
                  style="max-height:80vh;object-fit:contain;">
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>
@else
  <div class="alert alert-light border small mb-0">
    Nenhuma foto enviada para este negócio.
  </div>
@endif
