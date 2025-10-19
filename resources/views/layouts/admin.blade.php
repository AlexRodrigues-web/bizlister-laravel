<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield("title", "Admin")</title>

  {{-- Usa seu pipeline existente (mix/app.css) + fallback Tailwind CDN --}}
  @if(function_exists("mix"))
    <link rel="stylesheet" href="{{ asset(mix("css/app.css")) }}">
    <script src="{{ asset(mix("js/app.js")) }}" defer></script>
  @endif
  <link rel="stylesheet" href="{{ asset("css/app.css") }}">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
  <div class="flex min-h-screen">
    <aside class="hidden md:block w-72 bg-white border-r border-slate-200">
      @include("admin._sidebar")
    </aside>

    <div class="flex-1 flex flex-col">
      <header class="sticky top-0 z-30 bg-white border-b border-slate-200">
        @include("admin._topbar")
      {{-- Barra auxiliar: botão Voltar em todas as páginas que não sejam o painel --}}
      @if (!request()->routeIs("admin.dashboard"))
        <div class="bg-slate-50/60 border-b border-slate-200">
          <div class="px-4 md:px-6 py-2">
            <a href="{{ route("admin.dashboard") }}"
               class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
              <span>←</span><span>Voltar ao painel</span>
            </a>
          </div>
        </div>
      @endif
      </header>

      <main class="p-4 md:p-6 lg:p-8">
        {{-- Flash messages / erros centralizados no layout --}}
        @if (session("success") || session("status"))
          <div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-green-800">
            {{ session("success") ?? session("status") }}
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

        @yield("content")
      </main>
    </div>
  </div>
</body>
</html>