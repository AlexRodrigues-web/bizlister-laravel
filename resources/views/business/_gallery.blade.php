@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  // Normaliza a instância do negócio (compatível com legado)
  $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));

  // Coleta até 9 imagens vindas da relação (se existir)
  $images = collect();

  if ($__biz && method_exists($__biz, 'images')) {
      $images = $__biz->images()
        ->take(9)
        ->get()
        ->map(function ($img) {
            // Colunas mais comuns no legado
            $path = $img->path ?? $img->url ?? $img->image ?? null;
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
        })
        ->filter()
        ->values();
  }

  $bizName = (string)($__biz->business_name ?? 'Negócio');
@endphp

@if ($__biz && $images->count() > 0)
  <section class="gallery my-3">
    <h2 class="h6 mb-3">Galeria</h2>

    {{-- Grade responsiva de imagens --}}
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

        {{-- Modal para visualização ampliada --}}
        <div class="modal fade" id="{{ $imgId }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0">
              <button type="button" class="btn-close ms-auto me-2 mt-2" data-bs-dismiss="modal" aria-label="Fechar"></button>
              <div class="modal-body p-0">
                <img
                  src="{{ $src }}"
                  alt="{{ e($alt) }}"
                  class="img-fluid w-100"
                  style="display:block;"
                  loading="eager"
                  decoding="async">
              </div>
              <div class="modal-footer justify-content-between">
                <small class="text-muted text-truncate">
                  {{ $bizName }}
                </small>
                <a class="btn btn-outline-secondary btn-sm" href="{{ $src }}" target="_blank" rel="noopener">
                  Abrir original
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>
@endif
