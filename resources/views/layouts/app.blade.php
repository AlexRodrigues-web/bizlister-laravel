<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BizLister') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- CSS/JS (Laravel Mix com fallback para asset()) -->
    @php($hasManifest = file_exists(public_path('mix-manifest.json')))
    @if ($hasManifest)
      <link rel="stylesheet" href="{{ asset(mix('css/app.css')) }}">
      <script src="{{ asset(mix('js/app.js')) }}" defer></script>
    @else
      <link rel="stylesheet" href="{{ asset('css/app.css') }}">
      <script src="{{ asset('js/app.js') }}" defer></script>
    @endif

    <!-- TW_CDN (dev only) -->
    <script src="https://cdn.tailwindcss.com"></script><!--TW_CDN_MARK=v1-->
  </head>

  <body class="font-sans antialiased">
{{-- PUBLIC_NAV_MARK --}}
@if (request()->is('admin*'))
  @include('partials._public_nav')
@endif
{{-- ADMIN_RIBBON_MARK --}}
@if (request()->is('admin*'))
  <div class="bg-amber-50 border-b border-amber-200">
    <div class="mx-auto max-w-6xl px-4 md:px-6 py-2 flex items-center justify-between">
      <div class="text-sm text-amber-900/80">
        VocÃƒÆ’Ã‚Âª estÃƒÆ’Ã‚Â¡ no <strong>Admin</strong>.
      </div>
      <a href="{{ route('dashboard', [], false) ?? url('/') }}"
         class="inline-flex items-center gap-2 rounded-lg border border-amber-300 bg-white px-3 py-1.5 text-sm hover:bg-amber-100">
        ÃƒÂ¢Ã¢â‚¬Â Ã‚Â Dashboard
      </a>
    </div>
  </div>
@endif
 <!--LAYOUT_APP_MARK=v1-->
    <div class="min-h-screen bg-gray-100">

      @include('layouts.navigation')

      {{-- Header opcional: sÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â³ renderiza se a view definir section("header") --}}
      @hasSection('header')
        <header class="bg-white shadow">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('header')
          </div>
        </header>
      @endif

      <!-- Page Content -->
      <main>
        {{-- Views clÃƒÆ’Ã†â€™Ãƒâ€šÃ‚Â¡ssicas --}}
        @auth
          <div class="max-w-6xl mx-auto px-4 mt-3 mb-4">
            <div class="flex items-center justify-end gap-3">
              <span class="text-sm text-slate-600">
                {{ auth()->user()->username ?? auth()->user()->name ?? auth()->user()->email }}
              </span>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1.5 text-sm text-slate-700 hover:bg-slate-200">
                  Sair
                </button>
              </form>
            </div>
          </div>
        @endauth

        @yield('content')

        {{-- Componentes tipo <x-app-layout> --}}
        {{ $slot ?? '' }}
      </main>
    </div>
  </body>
</html>
