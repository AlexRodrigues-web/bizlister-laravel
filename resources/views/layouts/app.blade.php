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
  </head>

  <body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">

      @include('layouts.navigation')

      {{-- Header opcional: sÃ³ renderiza se a view definir section("header") --}}
      @hasSection('header')
        <header class="bg-white shadow">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            @yield('header')
          </div>
        </header>
      @endif

      <!-- Page Content -->
      <main>
        {{-- Views clÃ¡ssicas --}}
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

