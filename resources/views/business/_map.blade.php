@php
  // Normaliza a instância
  $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));

  // Helper seguro para extrair string de objeto/array
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

  // Monta endereço (somente partes não vazias)
  $addr1 = (string) ($__biz->address_1 ?? '');
  $city  = $safe($__biz->city ?? null,  ['city','name','label']) ?: (string) ($__biz->city ?? '');
  $state = $safe($__biz->state ?? null, ['state','name','label']) ?: (string) ($__biz->state ?? '');

  $addrParts = array_filter([$addr1, $city, $state], fn($v) => is_string($v) && trim($v) !== '');
  $addrText  = $addrParts ? implode(', ', $addrParts) : null;
  $mapQ      = $addrText ? urlencode($addrText) : null;
@endphp

@if ($mapQ)
<section aria-labelledby="map" class="my-3">
  <div class="card">
    <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2">
      <h2 id="map" class="h6 mb-0">Localização</h2>
      @if ($addrText)
        <div class="small text-muted text-truncate">{{ $addrText }}</div>
      @endif
    </div>

    <div class="card-body p-0">
      <div class="ratio ratio-16x9 bg-light">
        <iframe
          src="https://www.google.com/maps?q={{ $mapQ }}&output=embed"
          title="Mapa - {{ $addrText }}"
          style="border:0;"
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
          allowfullscreen>
        </iframe>
      </div>
    </div>

    <div class="card-footer bg-white d-flex justify-content-end">
      <a class="btn btn-outline-secondary btn-sm"
         href="https://www.google.com/maps/search/?api=1&query={{ $mapQ }}"
         target="_blank" rel="noopener">
        Abrir no Google Maps
      </a>
    </div>
  </div>
</section>
@endif
