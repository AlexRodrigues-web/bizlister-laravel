@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Str;

  // Normaliza a instância do negócio (compatível com legado)
  $__biz  = $business ?? ($biz ?? ($b ?? ($item ?? ($company ?? null))));
  $url    = url()->current();
  $title  = $__biz->business_name ?? config('app.name');

  // Resolve imagem absoluta (http/https, storage/public ou public_path)
  $imgUrl = null;
  if ($__biz) {
    $candidate = $__biz->image_lg ?? $__biz->image ?? null;
    if ($candidate) {
      if (Str::startsWith($candidate, ['http://','https://'])) {
        $imgUrl = $candidate;
      } elseif (Storage::disk('public')->exists($candidate)) {
        $imgUrl = asset('storage/'.$candidate);
      } elseif (file_exists(public_path($candidate))) {
        $imgUrl = url($candidate);
      }
    }
  }
@endphp

<div class="d-flex flex-wrap gap-2 my-3">
  {{-- Facebook --}}
  <a
    class="btn btn-outline-primary btn-sm"
    href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}"
    target="_blank" rel="noopener"
    aria-label="Compartilhar no Facebook">
    Facebook
  </a>

  {{-- X (Twitter) --}}
  <a
    class="btn btn-outline-info btn-sm"
    href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}&text={{ urlencode($title) }}"
    target="_blank" rel="noopener"
    aria-label="Compartilhar no X (Twitter)">
    X (Twitter)
  </a>

  {{-- Pinterest (só se houver imagem) --}}
  @if ($imgUrl)
    <a
      class="btn btn-outline-danger btn-sm"
      href="https://pinterest.com/pin/create/button/?url={{ urlencode($url) }}&media={{ urlencode($imgUrl) }}&description={{ urlencode($title) }}"
      target="_blank" rel="noopener"
      aria-label="Compartilhar no Pinterest">
      Pinterest
    </a>
  @endif

  {{-- Bookmark (somente logado e se a rota existir) --}}
  @auth
    @if (Route::has('business.bookmark') && $__biz?->biz_id)
      <form method="POST" action="{{ route('business.bookmark', $__biz->biz_id) }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-secondary btn-sm" aria-label="Salvar nos favoritos">
          Salvar
        </button>
      </form>
    @endif
  @endauth
</div>
