@php
  // Carrega a linha única (id=1) e cacheia por 5 minutos
  $ad = \Illuminate\Support\Facades\Cache::remember('ads.row.1', 300, function () {
      return \App\Models\Advertisement::query()->where('id', 1)->first();
  });

  // Aceita ['slot' => 'ad1'] ou ['position' => 'ad1']
  $slot = $slot ?? ($position ?? 'ad1');

  // (opcional) força para ad1/ad2/ad3 se quiser limitar:
  // $slot = in_array($slot, ['ad1','ad2','ad3'], true) ? $slot : 'ad1';

  $html = $ad?->{$slot} ?? '';
@endphp

@if (is_string($html) && trim($html) !== '')
  <div class="my-3 ads-block ads-{{ $slot }}">
    {!! $html !!} {{-- intencional: HTML vindo do admin --}}
  </div>
@endif
