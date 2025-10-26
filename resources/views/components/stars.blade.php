@props([
  "value" => 0,
  "size" => "md", // xs, sm, md, lg
])

@php
  $v = max(0, min(5, (float) $value));
  $sizes = ["xs"=>"h-3 w-3", "sm"=>"h-4 w-4", "md"=>"h-5 w-5", "lg"=>"h-6 w-6"];
  $cls = $sizes[$size] ?? $sizes["md"];
@endphp

<div class="inline-flex items-center gap-1 align-middle" aria-label="rating {{ $v }} de 5">
  @for($i=1; $i<=5; $i++)
    @php $filled = $i <= floor($v); @endphp
    <svg class="{{ $cls }} {{ $filled ? 'text-amber-500' : 'text-slate-300' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.967 0 1.371 1.24.588 1.81l-2.802 2.035a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.538 1.118l-2.802-2.035a1 1 0 00-1.176 0L6.563 16.28c-.783.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.93 8.72c-.783-.57-.38-1.81.588-1.81H6.98a1 1 0 00.95-.69l1.12-3.293z"/>
    </svg>
  @endfor
</div>