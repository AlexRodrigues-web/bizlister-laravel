@php
/** @var \App\Models\Business $business */
$items = $business->reviews()->where("is_approved", true)->latest()->paginate(10);
@endphp

<section id="reviews" class="mt-10">
  <header class="mb-4 flex items-end justify-between">
    <h2 class="text-xl font-semibold text-slate-800">Avaliações</h2>
    <span class="text-sm text-slate-500">{{ $items->total() }} {{ Str::plural("avaliação", $items->total()) }}</span>
  </header>

  @forelse ($items as $r)
    <article class="mb-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="min-w-0">
          <div class="font-medium text-slate-800 truncate">{{ $r->user?->name ?? "Visitante" }}</div>
          <div class="text-xs text-slate-500">{{ $r->created_at?->format("d/m/Y H:i") }}</div>
        </div>
        <div class="shrink-0">
          <x-stars :value="$r->rating" size="sm" />
        </div>
      </div>

      @if($r->title)
        <h3 class="mt-3 text-slate-800 font-semibold">{{ $r->title }}</h3>
      @endif

      @if($r->body)
        <p class="mt-2 whitespace-pre-line leading-relaxed text-slate-700">{{ $r->body }}</p>
      @endif
    </article>
  @empty
    <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
      Ainda não há avaliações aprovadas.
    </div>
  @endforelse

  <div class="mt-4">{{ $items->onEachSide(1)->links() }}</div>
</section>