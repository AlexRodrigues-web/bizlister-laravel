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
    :root{
      --container-bg:#f8fafc;

      /* Footer theme */
      --footer-bg:#0f172a;            /* slate-900 */
      --footer-fg:#cbd5e1;            /* slate-300 */
      --footer-muted:#94a3b8;         /* slate-400 */
      --footer-link:#e2e8f0;          /* slate-200 */
      --footer-link-hover:#ffffff;    /* white */
    }

    body { background: var(--container-bg); }
    .navbar-brand { font-weight:700; letter-spacing:-.01em; }
    .card-list .card + .card { margin-top:.75rem; }

    /* A11y: link "pular para conteúdo" visível ao focar */
    .skip-link {
      position:absolute; left:-9999px; top:auto; width:1px; height:1px; overflow:hidden;
    }
    .skip-link:focus {
      left:1rem; top:1rem; width:auto; height:auto; z-index:1030;
      background:#0d6efd; color:#fff; padding:.5rem .75rem; border-radius:.5rem;
      text-decoration:none; box-shadow:0 .5rem 1rem rgba(0,0,0,.15);
    }

    /* ==== Footer aprimorado (usa o partial partials/footer) ==== */
    .site-footer { background:var(--footer-bg); color:var(--footer-fg); }
    .site-footer h6 { color:#e2e8f0; font-weight:700; letter-spacing:.02em; font-size:.95rem; margin-bottom:.75rem; }
    .site-footer .list-unstyled li + li { margin-top:.35rem; }
    .site-footer a { color:var(--footer-link); text-decoration:none; }
    .site-footer a:hover { color:var(--footer-link-hover); text-decoration:underline; }
    .site-footer .muted { color:var(--footer-muted); }
    .site-footer .footer-top { padding-top:2rem; padding-bottom:1.25rem; }
    .site-footer .footer-bottom { border-top:1px solid rgba(148,163,184,.25); padding-top:.9rem; padding-bottom:.9rem; }
    .site-footer .brand { font-weight:700; letter-spacing:-.01em; }
    .site-footer .inline-links > * + * { margin-left:1rem; }
    .site-footer .chip { display:inline-block; padding:.2rem .5rem; border-radius:999px; border:1px solid rgba(148,163,184,.25); color:var(--footer-fg); font-size:.75rem; }
    @media (max-width:575.98px){
      .site-footer .inline-links { display:block; }
      .site-footer .inline-links > * { display:block; margin-left:0 !important; }
      .site-footer .inline-links > * + * { margin-top:.35rem; }
    }
  </style>
  @stack('styles')
</head>
<body>
<a href="#conteudo" class="skip-link">Pular para o conteúdo</a>

@include('layouts.partials.topnav')
@include('partials.ads', ['slot' => 'ad1'])

<main id="conteudo" class="container py-4">
  @include('partials.alerts')
  @yield('content')
</main>

{{-- Rodapé centralizado em partial, com defaults à prova de "Undefined variable" --}}
@includeIf('partials.footer')

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
