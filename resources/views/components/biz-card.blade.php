@props([
  'biz'         => null,   // \App\Models\Business ou similar
  'id'          => null,   // forÃ§a id (opcional)
  'title'       => null,   // forÃ§a tÃ­tulo (opcional)
  'subtitle'    => null,   // forÃ§a subtÃ­tulo (opcional)
  'href'        => null,   // forÃ§a link (opcional)
  'meta'        => null,   // forÃ§a meta (opcional)
  'image'       => null,   // URL da imagem (opcional)
  'description' => null,   // forÃ§a descriÃ§Ã£o (opcional)
])

@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Storage;

  $biz = $biz ?? null;

  // Helper seguro: extrai string de objeto/array
  $pick = function($obj, array $keys = []) {
    if (is_string($obj)) return trim($obj);
    if (is_object($obj) || is_array($obj)) {
      foreach ($keys as $k) {
        $val = is_object($obj) ? ($obj->{$k} ?? null) : ($obj[$k] ?? null);
        if (is_string($val) && trim($val) !== '') return trim($val);
      }
    }
    return '';
  };

  // ID
  $bid = $id ?? ($biz->biz_id ?? ($biz->id ?? null));

  // Nome / TÃ­tulo
  $name = $title
       ?? ($biz->business_name ?? $biz->title ?? $biz->name ?? '');

  $displayName = $name !== '' ? $name : ($bid ? ('NegÃ³cio #'.$bid) : 'NegÃ³cio');

  // Slug e link
  $slug = Str::slug($displayName ?: 'negocio');
  $link = $href ?? ($bid ? route('business.show', [$bid, $slug]) : null);

  // SubtÃ­tulo e meta (texto apenas)
  $sub = $subtitle
      ?? ($biz->address ?? $biz->address_1 ?? null)
      ?? ($biz->category_name ?? null)
      ?? $pick($biz->category ?? null, ['category','name','label','cat_name','category_name']);

  $met = $meta
      ?? ($biz->city_name ?? $biz->cidade ?? $biz->state_name ?? null)
      ?? $pick($biz->city ?? null, ['city','name','label']);

  // DescriÃ§Ã£o
  $desc = $description ?? ($biz->short_description ?? $biz->description ?? '');

  // Imagem
  $img = null;
  $candidates = [];
  if ($image) $candidates[] = $image;
  $candidates[] = $biz->image_lg ?? null;
  $candidates[] = $biz->image ?? null;

  foreach ($candidates as $p) {
    if (!$p) continue;
    if (is_string($p) && preg_match('~^https?://~i', $p)) { $img = $p; break; }
    if (is_string($p) && Storage::disk('public')->exists($p)) { $img = asset('storage/'.$p); break; }
    if (is_string($p) && file_exists(public_path($p))) { $img = url($p); break; }
  }
  if (!$img) $img = asset('images/placeholder-800x600.png');
@endphp

<div class="card h-100 shadow-sm">
  <a href="{{ $link ?: 'javascript:void(0)' }}" class="text-decoration-none">
    <div class="ratio ratio-4x3 bg-light">
      <img src="{{ $img }}" alt="Imagem de {{ $displayName }}" class="w-100 h-100" style="object-fit:cover;">
    </div>
  </a>

  <div class="card-body d-flex flex-column">
    <div class="d-flex align-items-start gap-2 mb-2">
      <a href="{{ $link ?: 'javascript:void(0)' }}" class="flex-grow-1 text-decoration-none text-dark">
        <h3 class="h6 mb-1 text-truncate">{{ $displayName }}</h3>
      </a>
      @if($sub)
        <span class="badge text-bg-light border">{{ $sub }}</span>
      @endif
      @if($met)
        <span class="badge text-bg-light border">{{ $met }}</span>
      @endif
    </div>

    @if($desc)
      <p class="card-text small text-muted mb-3" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
        {{ Str::limit(strip_tags($desc), 140) }}
      </p>
    @endif

    <div class="mt-auto">
      @if($link)
        <a href="{{ $link }}" class="btn btn-primary btn-sm">Ver detalhes</a>
      @endif
    </div>
  </div>
</div>
