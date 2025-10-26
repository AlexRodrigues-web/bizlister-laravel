<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale() ?? 'pt-BR') }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="color-scheme" content="light">
  <title>@yield('title', $title ?? config('app.name'))</title>

  {{-- CSS principal (mantido) --}}
  <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">

  {{-- Estilos pontuais: mantém visual atual e dá um polish leve --}}
  <style>
    :root { --container-bg: #f8fafc; }
    body { background: var(--container-bg); }
    .navbar-brand { font-weight: 700; letter-spacing: -.01em; }
    .card-list .card + .card { margin-top: .75rem; }

    /* A11y: link "pular para conteúdo" visível ao focar */
    .skip-link {
      position: absolute; left: -9999px; top: auto; width: 1px; height: 1px; overflow: hidden;
    }
    .skip-link:focus {
      left: 1rem; top: 1rem; width: auto; height: auto; z-index: 1030;
      background: #0d6efd; color: #fff; padding: .5rem .75rem; border-radius: .5rem;
      text-decoration: none; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
    }

    /* Footer mais elegante, mantendo simplicidade */
    footer { background: #fff; }
  </style>
  @stack('styles')
</head>
<body>
<a href="#conteudo" class="skip-link">Pular para o conteúdo</a>

@include('layouts.partials.topnav')

<main id="conteudo" class="container py-4">
  @include('partials.alerts')
  @yield('content')
</main>

<footer class="border-top py-3">
  <div class="container text-muted small">
    &copy; {{ date('Y') }} {{ config('app.name') }}
  </div>
</footer>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
