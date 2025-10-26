<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title', 'Admin')</title>

  {{-- Usa seu pipeline existente (mix/app.css) + fallback --}}
  @if(function_exists('mix'))
    <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>
  @endif
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Drawer básico (sem libs) */
    .drawer-backdrop { background: rgba(2,6,23,.45); }
    .drawer-panel { transform: translateX(-100%); transition: transform .2s ease; }
    .drawer-open .drawer-panel { transform: translateX(0); }
  </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
  <div class="flex min-h-screen">

    {{-- Sidebar fixa: desativada por padrão. Mude para true se quiser fixo. --}}
    @php $useFixedAdminSidebar = false; @endphp
    @if($useFixedAdminSidebar)
      <aside class="hidden md:block w-72 bg-white border-r border-slate-200">
        @include('admin._sidebar')
      </aside>
    @endif

    <div class="flex-1 flex flex-col">
      <header class="sticky top-0 z-30 bg-white border-b border-slate-200">
        @php
          /** Welcome (home pública): sempre aponta para a raiz do site */
          $publicHomeUrl = url('/');
        @endphp

        <div class="flex items-center justify-between gap-2 px-4 md:px-6 h-14">
          {{-- Botão para abrir a sidebar como gaveta (mobile) --}}
          <button
            type="button"
            id="btn-open-drawer"
            class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50 md:hidden"
            aria-label="Abrir menu">
            ☰ Menu
          </button>

          {{-- Topbar existente --}}
          <div class="flex-1">
            @include('admin._topbar')
          </div>

          {{-- Botão: Página inicial (Welcome) --}}
          <a href="{{ $publicHomeUrl }}"
             class="hidden sm:inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50"
             title="Ir para a página inicial (Welcome)">
            <span aria-hidden="true">↗</span>
            <span>Página inicial</span>
          </a>
        </div>

        {{-- Barra auxiliar “Voltar ao painel” (não exibe no admin.dashboard) --}}
        @php
          $isAdminDashboard = request()->routeIs('admin.dashboard');
        @endphp
        @if (! $isAdminDashboard)
          <div class="bg-slate-50/60 border-t border-slate-200">
            <div class="px-4 md:px-6 py-2">
              <a href="{{ route('admin.dashboard') }}
                 " class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
                <span aria-hidden="true">←</span><span>Voltar ao painel</span>
              </a>
            </div>
          </div>
        @endif
      </header>

      {{-- Drawer da sidebar (mobile e quando quiser abrir manualmente) --}}
      <div id="admin-drawer" class="fixed inset-0 z-40 hidden" aria-hidden="true">
        <div class="drawer-backdrop absolute inset-0"></div>
        <aside class="drawer-panel relative z-10 h-full w-72 bg-white border-r border-slate-200 shadow-xl">
          <div class="flex items-center justify-between px-4 h-14 border-b border-slate-200">
            <strong class="text-slate-800">Menu</strong>
            <button type="button" id="btn-close-drawer" class="rounded-lg px-2 py-1 text-sm hover:bg-slate-100" aria-label="Fechar">✕</button>
          </div>
          <div class="p-4">
            @include('admin._sidebar')
          </div>
        </aside>
      </div>

      <main class="p-4 md:p-6 lg:p-8">
        {{-- Flash messages / erros centralizados no layout --}}
        @if (session('success') || session('status'))
          <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-800">
            {{ session('success') ?? session('status') }}
          </div>
        @endif

        @if ($errors->any())
          <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-red-800">
            <ul class="list-disc list-inside">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </main>
    </div>
  </div>

  <script>
    (function () {
      const drawer   = document.getElementById('admin-drawer');
      const btnOpen  = document.getElementById('btn-open-drawer');
      const btnClose = document.getElementById('btn-close-drawer');

      const open = () => {
        if (!drawer) return;
        drawer.classList.remove('hidden');
        requestAnimationFrame(() => drawer.classList.add('drawer-open'));
      };
      const close = () => {
        if (!drawer) return;
        drawer.classList.remove('drawer-open');
        setTimeout(() => drawer.classList.add('hidden'), 200);
      };

      btnOpen?.addEventListener('click', open);
      btnClose?.addEventListener('click', close);
      drawer?.addEventListener('click', (e) => {
        if (e.target === drawer) close();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') close();
      });
    })();
  </script>
</body>
</html>
