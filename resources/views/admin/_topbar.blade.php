<div class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-slate-200 shadow-sm">
  <div class="flex items-center gap-3 px-4 md:px-6 h-16">

    {{-- Brand com logo legado --}}
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-lg font-semibold tracking-tight text-slate-900">
      <img src="{{ asset('legacy/logo.png') }}" alt="BizLister" class="h-10 w-auto block rounded-sm">
      <span class="hidden sm:inline">BizLister</span>
    </a>

    {{-- Navegação (desktop) --}}
    <nav class="hidden md:flex items-center gap-1 text-sm ml-2">
      @php
        $linkBase = 'px-3 py-2 rounded-lg transition-colors';
        $active   = $linkBase.' bg-slate-900 text-white';
        $idle     = $linkBase.' text-slate-700 hover:bg-slate-100';
      @endphp

      <a href="{{ route('admin.dashboard') }}"
         class="{{ request()->routeIs('admin.dashboard') ? $active : $idle }}"
         aria-current="{{ request()->routeIs('admin.dashboard') ? 'page' : 'false' }}">
        Painel
      </a>

      @if (Route::has('admin.categories.index'))
        <a href="{{ route('admin.categories.index') }}"
           class="{{ request()->routeIs('admin.categories.*') ? $active : $idle }}"
           aria-current="{{ request()->routeIs('admin.categories.*') ? 'page' : 'false' }}">
          Categorias
        </a>
      @endif

      @if (Route::has('admin.cities.index'))
        <a href="{{ route('admin.cities.index') }}"
           class="{{ request()->routeIs('admin.cities.*') ? $active : $idle }}"
           aria-current="{{ request()->routeIs('admin.cities.*') ? 'page' : 'false' }}">
          Cidades
        </a>
      @endif

      @if (Route::has('admin.reviews.index'))
        <a href="{{ route('admin.reviews.index') }}"
           class="{{ request()->routeIs('admin.reviews.*') ? $active : $idle }}"
           aria-current="{{ request()->routeIs('admin.reviews.*') ? 'page' : 'false' }}">
          Avaliações
        </a>
      @endif

      @if (Route::has('admin.businesses.create'))
        <a href="{{ route('admin.businesses.create') }}"
           class="{{ request()->routeIs('admin.businesses.create') ? $active : $idle }}"
           aria-current="{{ request()->routeIs('admin.businesses.create') ? 'page' : 'false' }}">
          Cadastrar Negócio
        </a>
      @endif
    </nav>

    {{-- Busca (desktop) --}}
    <form method="get" action="{{ route('admin.dashboard') }}" class="ml-auto hidden md:flex items-center">
      <div class="relative">
        <input
          name="q"
          value="{{ request('q') }}"
          placeholder="Buscar"
          class="w-56 rounded-lg border border-slate-300 pl-9 pr-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition"
          aria-label="Buscar no painel">
        <svg class="pointer-events-none absolute left-2.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M10 4a6 6 0 1 1 0 12 6 6 0 0 1 0-12Zm8.707 13.293-3.387-3.387A8 8 0 1 0 12 20a8 8 0 0 0 5.526-2.2l3.167 3.167 1.414-1.414Z"/>
        </svg>
      </div>
    </form>

    {{-- Usuário (desktop) --}}
    <div class="hidden md:flex items-center text-sm ml-3">
      <details class="relative group">
        <summary class="list-none cursor-pointer inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-2.5 py-1 text-slate-700">
          <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5Z"/>
          </svg>
          {{ auth()->user()->name ?? 'Admin' }}
          <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
        </summary>
        <div class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
          <nav class="flex flex-col text-sm">
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700">Meu painel</a>
            @auth
              @if (Route::has('profile.show'))
                <a href="{{ route('profile.show') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700">Meu perfil</a>
              @endif
            @endauth
            @can('admin')
              <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700">Admin</a>
            @endcan
            <hr class="my-2">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700">Sair</button>
            </form>
          </nav>
        </div>
      </details>
    </div>

    {{-- Menu (mobile) --}}
    <div class="ml-auto md:hidden">
      <details class="relative">
        <summary class="list-none cursor-pointer inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-sm text-slate-700">
          Menu
          <svg class="ml-2 h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
          </svg>
        </summary>
        <div class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white p-2 shadow-lg">
          <nav class="flex flex-col text-sm">
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Painel</a>

            @if (Route::has('admin.categories.index'))
              <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('admin.categories.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Categorias</a>
            @endif

            @if (Route::has('admin.cities.index'))
              <a href="{{ route('admin.cities.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('admin.cities.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Cidades</a>
            @endif

            @if (Route::has('admin.reviews.index'))
              <a href="{{ route('admin.reviews.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('admin.reviews.*') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Avaliações</a>
            @endif

            @if (Route::has('admin.businesses.create'))
              <a href="{{ route('admin.businesses.create') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 {{ request()->routeIs('admin.businesses.create') ? 'bg-slate-900 text-white' : 'text-slate-700' }}">Cadastrar Negócio</a>
            @endif

            @auth
              @if (Route::has('profile.show'))
                <a href="{{ route('profile.show') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700">Meu perfil</a>
              @endif
            @endauth

            <hr class="my-2">
            <div class="px-3 py-1.5 text-slate-600">
              {{ auth()->user()->name ?? 'Admin' }}
            </div>

            <form method="get" action="{{ route('admin.dashboard') }}" class="px-2 py-1.5">
              <input name="q" value="{{ request('q') }}" placeholder="Buscar"
                     class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
            </form>
          </nav>
        </div>
      </details>
    </div>

  </div>
</div>
