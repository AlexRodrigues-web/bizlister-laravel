@php
  /** @var \App\Models\Business $biz */
  use Illuminate\Support\Str;

  $name = $biz->business_name ?? '';
  $slug = Str::slug($name);
@endphp

<article class="rounded-2xl border border-slate-200 shadow-sm bg-white overflow-hidden flex flex-col h-full">
  <div class="p-4 flex-1">
    <h3 class="text-base font-semibold text-slate-800 leading-snug">
      <a class="hover:underline" href="{{ route('business.show', [$biz->biz_id, $slug]) }}">
        {{ $name !== '' ? $name : ('Negócio #'.$biz->biz_id) }}
      </a>
    </h3>

    <p class="text-xs text-slate-500 mt-1">{{ $biz->city ?? '—' }}</p>

    <p class="text-sm text-slate-600 mt-3">
      {{ Str::limit(strip_tags($biz->description ?? ''), 140) }}
    </p>
  </div>

  <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
    <a class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700"
       href="{{ route('business.show', [$biz->biz_id, $slug]) }}">
       Ver detalhes
       <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
       </svg>
    </a>
  </div>
</article>
