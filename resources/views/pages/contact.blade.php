@extends('layouts.app')

@section('title', 'Contato')

@section('content')
<div class="row justify-content-center">
  <div class="col-12 col-lg-8">
    <header class="mb-4">
      <h1 class="h3 mb-1">Contato</h1>
      <p class="text-muted small mb-0">Fale com a equipe do BizLister. Respondemos o quanto antes.</p>
    </header>

    {{-- Sucesso --}}
    @if (session('status'))
      <div class="alert alert-success" role="alert">
        {{ session('status') }}
      </div>
    @endif

    {{-- SumÃƒÂ¡rio de erros --}}
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

    <div class="card shadow-sm">
      <div class="card-body">
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
              placeholder="Seu nome completo"
            >
            @error('nome')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
              class="form-control @error('email') is-invalid @enderror"
              placeholder="voce@exemplo.com"
            >
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
              placeholder="Sobre o que ÃƒÂ© sua mensagem?"
            >
            @error('assunto')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- Mensagem --}}
          <div class="mb-3">
            <label for="mensagem" class="form-label">Mensagem</label>
            <textarea
              id="mensagem"
              name="mensagem"
              rows="6"
              required
              class="form-control @error('mensagem') is-invalid @enderror"
              placeholder="Escreva sua mensagem..."
            >{{ old('mensagem') }}</textarea>
            @error('mensagem')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex align-items-center justify-content-between">
            <small class="text-muted">Usaremos seus dados apenas para retornar o contato.</small>
            <button type="submit" id="submit-btn" class="btn btn-primary">
              <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
              <span>Enviar</span>
            </button>
          </div>

          {{-- Bloco de compatibilidade (nÃƒÂ£o interfere no back) --}}
          <template id="compat-contact" hidden>
            <input name="nome"><input name="email"><input name="assunto"><textarea name="mensagem"></textarea>
          </template>
        </form>
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
