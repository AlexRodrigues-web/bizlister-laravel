@php
  // Normaliza a instância do negócio para garantir biz_id
  $__biz = $business ?? ($biz ?? ($item ?? ($company ?? null)));
@endphp

@auth
<section aria-labelledby="write-review" class="my-3">
  <div class="card">
    <div class="card-header bg-white">
      <h2 id="write-review" class="h6 mb-0">Escrever uma avaliação</h2>
    </div>
    <div class="card-body">

      @if (!empty($__biz?->biz_id))
        <form method="POST" action="{{ route('reviews.store', ['id' => $__biz->biz_id]) }}" novalidate>
          @csrf

          {{-- Nota --}}
          <div class="mb-3">
            <label class="form-label">Nota</label>
            <div class="d-flex align-items-center gap-3">
              <select name="rating" id="rv-rating"
                      class="form-select w-auto @error('rating') is-invalid @enderror"
                      required>
                @for ($i = 5; $i >= 1; $i--)
                  <option value="{{ $i }}" {{ (int)old('rating',5) === $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
              </select>

              {{-- Pré-visualização de estrelas (somente visual) --}}
              <div id="rv-stars" class="text-nowrap small" aria-hidden="true">
                @for ($i=1; $i<=5; $i++)
                  <span data-star="{{ $i }}" class="text-warning">★</span>
                @endfor
              </div>
            </div>
            @error('rating')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          {{-- Título --}}
          <div class="mb-3">
            <label for="rv-title" class="form-label">Título</label>
            <input type="text"
                   id="rv-title"
                   name="title"
                   value="{{ old('title') }}"
                   class="form-control @error('title') is-invalid @enderror"
                   maxlength="150"
                   placeholder="Resumo curto opcional">
            @error('title')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Comentário --}}
          <div class="mb-3">
            <label for="rv-body" class="form-label">Comentário</label>
            <textarea id="rv-body"
                      name="body"
                      rows="4"
                      class="form-control @error('body') is-invalid @enderror"
                      maxlength="5000"
                      placeholder="Conte mais sobre sua experiência">{{ old('body') }}</textarea>
            @error('body')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Sua avaliação ficará visível após aprovação.</div>
          </div>

          <button class="btn btn-primary">
            Enviar avaliação
          </button>
        </form>
      @else
        <div class="alert alert-warning mb-0">Não foi possível identificar o negócio para avaliação.</div>
      @endif

    </div>
  </div>
</section>

@push('scripts')
<script>
  (function () {
    const select = document.getElementById('rv-rating');
    const wrap   = document.getElementById('rv-stars');
    if (!select || !wrap) return;

    function paintStars(val){
      const n = parseInt(val || '0', 10);
      wrap.querySelectorAll('[data-star]').forEach(el => {
        const i = parseInt(el.getAttribute('data-star'), 10);
        el.className = (i <= n) ? 'text-warning' : 'text-secondary';
        el.textContent = '★';
      });
    }
    // Inicial
    paintStars(select.value);
    // Ao mudar
    select.addEventListener('change', function(){ paintStars(this.value); });
  })();
</script>
@endpush

@else
  <div class="alert alert-light border my-3">
    <a class="link-underline" href="{{ route('login') }}">Entre</a> para avaliar.
  </div>
@endauth
