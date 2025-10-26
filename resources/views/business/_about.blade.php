@php
  // Normaliza a instância do negócio (compatível com legado)
  $biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));

  // Helper: extrai string segura de objeto/array pelas chaves informadas
  $safe = function ($v, array $keys = []) {
      if (is_string($v)) return trim($v);
      if (is_object($v) || is_array($v)) {
          foreach ($keys as $k) {
              $val = is_object($v) ? ($v->{$k} ?? null) : ($v[$k] ?? null);
              if (is_string($val) && trim($val) !== '') return trim($val);
          }
      }
      return '';
  };

  // Categoria (apenas string, sem objeto bruto)
  $catLabel = '';
  if (isset($category)) {
      $catLabel = $safe($category, ['category','name','label','cat_name','category_name']);
  }
  if ($catLabel === '' && $biz) {
      $catLabel = $safe(($biz->category ?? null), ['category','name','label','cat_name','category_name'])
               ?: ($biz->category_name ?? $biz->cat_name ?? '');
  }
  if ($catLabel === '' && $biz) {
      $catLabel = (string) ($biz->cid ?? '');
  }

  // Cidade (apenas string)
  $cityLabel = '';
  if (isset($city)) {
      $cityLabel = $safe($city, ['city','name','label']);
  }
  if ($cityLabel === '' && $biz) {
      $cityLabel = $safe(($biz->city ?? null), ['city','name','label'])
               ?: ($biz->city_name ?? $biz->cidade ?? '');
  }
  if ($cityLabel === '' && $biz) {
      $cityLabel = (string) ($biz->city ?? '');
  }

  $hasDesc = $biz && filled($biz->description);
@endphp

<div class="about-business">
  {{-- Badges de contexto --}}
  <div class="d-flex flex-wrap gap-2 mb-3">
    <span class="badge text-bg-light border" title="Categoria">
      {{ $catLabel !== '' ? $catLabel : 'Sem categoria' }}
    </span>
    <span class="badge text-bg-light border" title="Cidade">
      {{ $cityLabel !== '' ? $cityLabel : 'Sem cidade' }}
    </span>
  </div>

  {{-- Resumo tipo &ldquo;ficha&rdquo; (opcional) --}}
  <dl class="row gy-2 small mb-0">
    <dt class="col-sm-2 text-muted">Categoria</dt>
    <dd class="col-sm-10 fw-medium">{{ $catLabel ?: '&mdash;' }}</dd>

    <dt class="col-sm-2 text-muted">Cidade</dt>
    <dd class="col-sm-10 fw-medium">{{ $cityLabel ?: '&mdash;' }}</dd>
  </dl>

  {{-- Sobre o negócio --}}
  @if ($hasDesc)
    <div class="mt-3">
      <h2 class="h6 mb-2">Sobre o negócio</h2>
      <div class="text-secondary">{!! nl2br(e($biz->description)) !!}</div>
    </div>
  @endif
</div>
