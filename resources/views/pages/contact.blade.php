@extends('layouts.app')

@section('title', 'Contato')

@section('content')
<style>
  /* Toque visual leve sem depender de outro build */
  .contact-hero {
    border-radius: 16px;
    background:
      radial-gradient(1300px 500px at 90% -10%, rgba(99,102,241,.15), transparent 70%),
      radial-gradient(1000px 400px at -10% 0%, rgba(59,130,246,.12), transparent 70%),
      linear-gradient(180deg, #fff, #fff);
    border: 1px solid rgba(0,0,0,.06);
  }
  .contact-card { border-radius: 14px; }
  .contact-card:hover { box-shadow: 0 12px 28px rgba(2,6,23,.08); }
  .form-hint { color:#64748b; font-size: .875rem; }
  .icon-badge {
    width: 38px; height: 38px; display:grid; place-items:center;
    border-radius: 10px; background:#f1f5f9; color:#0f172a;
  }
</style>

<div class="row justify-content-center">
  <div class="col-12 col-xxl-10">

    {{-- HERO --}}
    <section class="contact-hero p-4 p-md-5 mb-4">
      <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
        <div>
          <h1 class="h3 mb-1">Contato</h1>
          <p class="text-muted mb-0">Fale com a equipe do BizLister. Respondemos o quanto antes.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
          <div class="d-none d-md-flex align-items-center gap-2">
            <span class="icon-badge" aria-hidden="true">
              {{-- phone icon --}}
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M6.62 10.79a15.07 15.07 0 006.59 6.59l2.2-2.2a1 1 0 011.02-.24 11.36 11.36 0 003.56.57 1 1 0 011 1V20a1 1 0 01-1 1A17 17 0 013 4a1 1 0 011-1h3.5a1 1 0 011 1 11.36 11.36 0 00.57 3.56 1 1 0 01-.24 1.02l-2.2 2.2z"/></svg>
            </span>
            <span class="small text-muted">Atendimento em horário comercial</span>
          </div>
        </div>
      </div>
    </section>

    <div class="row g-4">
      {{-- COLUNA FORM --}}
      <div class="col-12 col-lg-8">
        {{-- Sucesso --}}
        @if (session('status'))
          <div class="alert alert-success" role="alert">
            {{ session('status') }}
          </div>
        @endif

        {{-- Resumo de erros --}}
        @if ($errors->any())
          <div class="alert alert-danger" role="alert" aria-live="polite">
            <div class="fw-semibold">Verifique os campos abaixo:</div>
            <ul class="mb-0 ps-3">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="card shadow-sm contact-card">
          <div class="card-body p-4 p-md-5">
            <form id="contact-form" method="POST" action="{{ route('contact.send') }}" novalidate>
              @csrf

              {{-- Nome --}}
              <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input
                  id="nome"
                  type="text"
                  name="nome"
                  value="{{ old('nome') }}"
                  required
                  autocomplete="name"
                  class="form-control @error('nome') is-invalid @enderror"
                  placeholder="Seu nome completo">
                @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- E-mail --}}
              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input
                  id="email"
                  type="email"
                  name="email"
                  value="{{ old('email') }}"
                  required
                  autocomplete="email"
                  inputmode="email"
                  class="form-control @error('email') is-invalid @enderror"
                  placeholder="voce@exemplo.com">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- Assunto --}}
              <div class="mb-3">
                <label for="assunto" class="form-label">Assunto</label>
                <input
                  id="assunto"
                  type="text"
                  name="assunto"
                  value="{{ old('assunto') }}"
                  required
                  class="form-control @error('assunto') is-invalid @enderror"
                  placeholder="Sobre o que é sua mensagem?">
                @error('assunto') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              {{-- Mensagem --}}
              <div class="mb-4">
                <label for="mensagem" class="form-label">Mensagem</label>
                <textarea
                  id="mensagem"
                  name="mensagem"
                  rows="6"
                  required
                  class="form-control @error('mensagem') is-invalid @enderror"
                  placeholder="Escreva sua mensagem...">{{ old('mensagem') }}</textarea>
                @error('mensagem') <div class="invalid-feedback">{{ $message }}</div> @enderror
                <div class="form-hint mt-2">Usaremos seus dados apenas para retornar o contato.</div>
              </div>

              <div class="d-flex align-items-center justify-content-between">
                <div class="small text-muted">
                  Tempo médio de resposta: <strong>até 1 dia útil</strong>.
                </div>
                <button type="submit" id="submit-btn" class="btn btn-primary">
                  <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                  <span>Enviar</span>
                </button>
              </div>

              {{-- Compatibilidade (não interfere no back) --}}
              <template id="compat-contact" hidden>
                <input name="nome"><input name="email"><input name="assunto"><textarea name="mensagem"></textarea>
              </template>
            </form>
          </div>
        </div>
      </div>

      {{-- COLUNA INFO/ATENDIMENTO --}}
      <div class="col-12 col-lg-4">
        <div class="card shadow-sm contact-card h-100">
          <div class="card-body p-4 p-md-5">
            <h2 class="h6 text-uppercase text-muted mb-3">Outros canais</h2>

            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="icon-badge" aria-hidden="true">
                {{-- mail --}}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20 4H4a2 2 0 00-2 2v.4l10 6.25L22 6.4V6a2 2 0 00-2-2zm0 4.25l-8 5-8-5V18a2 2 0 002 2h12a2 2 0 002-2V8.25z"/></svg>
              </div>
              <div>
                <div class="fw-semibold">E-mail</div>
                <a href="mailto:suporte@bizlister.local" class="text-decoration-none">suporte@bizlister.local</a>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-3">
              <div class="icon-badge" aria-hidden="true">
                {{-- help --}}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1010 10A10 10 0 0012 2zm.1 15a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5zm2.48-7.27a3.18 3.18 0 00-3.43-2.46 3.48 3.48 0 00-3 3.36h2a1.58 1.58 0 013.05-.36c.22.56-.08 1.03-.76 1.53-.94.68-2.19 1.6-2.19 3.2v.3h2v-.3c0-.77.78-1.39 1.62-2 .94-.68 1.99-1.44 1.09-3.27z"/></svg>
              </div>
              <div>
                <div class="fw-semibold">Central de ajuda</div>
                <a href="{{ url('/sobre') }}" class="text-decoration-none">Sobre o BizLister</a>
              </div>
            </div>

            <hr class="my-4">

            <h3 class="h6 text-uppercase text-muted mb-3">FAQ rápido</h3>
            <div class="accordion accordion-flush" id="faq-contact">
              <div class="accordion-item">
                <h2 class="accordion-header" id="fq1">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq1c" aria-expanded="false" aria-controls="fq1c">
                    Quanto tempo leva para responder?
                  </button>
                </h2>
                <div id="fq1c" class="accordion-collapse collapse" aria-labelledby="fq1" data-bs-parent="#faq-contact">
                  <div class="accordion-body small text-muted">Normalmente respondemos em até 1 dia útil. Em períodos de alta demanda, pode levar um pouco mais.</div>
                </div>
              </div>
              <div class="accordion-item">
                <h2 class="accordion-header" id="fq2">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#fq2c" aria-expanded="false" aria-controls="fq2c">
                    Preciso estar logado para enviar?
                  </button>
                </h2>
                <div id="fq2c" class="accordion-collapse collapse" aria-labelledby="fq2" data-bs-parent="#faq-contact">
                  <div class="accordion-body small text-muted">Não. Basta informar seu e-mail para retorno.</div>
                </div>
              </div>
            </div>

            <hr class="my-4">

            <div class="small text-muted">Preferir WhatsApp? Inclua seu número no corpo da mensagem e entraremos em contato.</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- Evita duplo envio + spinner --}}
@push('scripts')
<script>
  (function () {
    const form = document.getElementById('contact-form');
    const btn  = document.getElementById('submit-btn');
    if (!form || !btn) return;

    form.addEventListener('submit', function () {
      const spinner = btn.querySelector('.spinner-border');
      const text = btn.querySelector('span:last-child');
      if (spinner) spinner.classList.remove('d-none');
      if (text) text.textContent = 'Enviando...';
      btn.setAttribute('disabled', 'disabled');
    }, { once: true });
  })();
</script>
@endpush
@endsection
