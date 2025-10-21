@php
  $addrParts = array_filter([
    $business->address_1 ?? null,
    $business->city ?? null,
    $business->state ?? null,
  ]);
  $mapQ = $addrParts ? urlencode(implode(", ", $addrParts)) : null;
@endphp

@if($mapQ)
<section aria-labelledby="map" class="mt-10">
  <h2 id="map" class="text-lg font-semibold text-slate-800 mb-2">Localização</h2>
  <div class="overflow-hidden rounded-2xl border border-slate-200 shadow-sm">
    <div class="aspect-video w-full bg-slate-100">
      <iframe
        src="https://www.google.com/maps?q={{ $mapQ }}&output=embed"
        width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
    </div>
  </div>
</section>
@endif