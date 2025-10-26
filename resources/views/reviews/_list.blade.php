@php
  /** @var \App\Models\Business $business */
  // Evita N+1 carregando o usuário junto
  $items = $business->reviews()
    ->where('is_approved', true)
    ->with('user')
    ->latest()
    ->paginate(10);

  $total = (int) $items->total();
  $plural = \Illuminate\Support\Str::plural('avaliação', $total);
@endphp

<section id="reviews" class="mt-4">
  <div class="d-flex align-items-end justify-content-between mb-3">
    <h2 class="h5 mb-0">Avaliações</h2>
    <small class="text-muted">{{ $total }} {{ $plural }}</small>
  </div>

  @forelse($items as $r)
    <article class="card mb-3 shadow-sm">
      <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
          <div class="min-w-0">
            <div class="fw-medium text-truncate">
              {{ trim($r->user->username ?? $r->user->name ?? 'Visitante') }}
            </div>
            <div class="text-muted small">
              {{ optional($r->created_at)->format('d/m/Y H:i') }}
            </div>
          </div>

          {{-- Estrelas (★) simples, sem dependências --}}
          @php $rating = max(0, min(5, (int) $r->rating)); @endphp
          <div class="text-nowrap small">
            @for ($i = 1; $i <= 5; $i++)
              <span class="{{ $i <= $rating ? 'text-warning' : 'text-secondary' }}">★</span>
            @endfor
          </div>
        </div>

        @if(filled($r->title))
          <h3 class="h6 mt-3 mb-1">{{ $r->title }}</h3>
        @endif

        @php
          $body = trim((string)($r->body ?? ''));
        @endphp
        @if($body !== '')
          <p class="mb-0 mt-2" style="white-space:pre-line">{{ $body }}</p>
        @endif
      </div>
    </article>
  @empty
    <div class="alert alert-light border text-center text-muted">
      Ainda não há avaliações aprovadas.
    </div>
  @endforelse

  {{-- Paginação Bootstrap 5 --}}
  @if($items->hasPages())
    <div class="mt-3">
      {{ $items->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
  @endif
</section>
