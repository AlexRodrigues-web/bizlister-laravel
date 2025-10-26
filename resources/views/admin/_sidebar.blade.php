{{-- resources/views/admin/_sidebar.blade.php --}}
{{-- Admin sidebar colapsável (discreto) --}}
<div class="p-4">

  {{-- Cabeçalho compacto --}}
  <div class="mb-4">
    @php
      $dashUrl = Route::has('admin.dashboard') ? route('admin.dashboard') : url('/');
    @endphp
    <a href="{{ $dashUrl }}" class="text-xl font-bold tracking-tight">Painel</a>
    <div class="text-xs text-slate-500">Administração</div>
  </div>

  {{-- Botão que revela/oculta o menu (sem JS) --}}
  <details class="group">
    <summary
      class="list-none inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 cursor-pointer select-none"
      aria-label="Abrir menu administrativo">
      <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z"/>
      </svg>
      Menu
      <span class="text-slate-400 group-open:hidden">• oculto</span>
      <span class="text-slate-400 hidden group-open:inline">• aberto</span>
    </summary>

    {{-- Conteúdo do menu (aparece ao abrir) --}}
    <div class="mt-3 rounded-xl border border-slate-200 bg-white p-3 shadow-sm">

      @php
        // helpers
        $itemClass = function (bool $active = false) {
          $base = 'group flex items-center justify-between rounded-lg px-3 py-2 text-sm';
          return $active
            ? "$base bg-slate-900 text-white shadow-sm"
            : "$base text-slate-700 hover:bg-slate-100 hover:text-slate-900";
        };
        $iconWrap = fn(bool $active) => $active
          ? 'mr-2 rounded-md bg-white/10 p-1.5 text-white'
          : 'mr-2 rounded-md bg-slate-100 p-1.5 text-slate-500 group-hover:bg-slate-200 group-hover:text-slate-700';

        $tot = $totals ?? [];
        $fmt = fn($v) => number_format((int)($v ?? 0), 0, ',', '.');

        // Rotas de configurações: preferir edit; fallback index
        $settingsRouteName = Route::has('admin.settings.edit')
          ? 'admin.settings.edit'
          : (Route::has('admin.settings.index') ? 'admin.settings.index' : null);
      @endphp

      <nav class="space-y-1 text-sm" role="navigation" aria-label="Menu administrativo">
        {{-- Dashboard --}}
        @php $active = request()->routeIs('admin.dashboard'); @endphp
        <a href="{{ route('admin.dashboard') }}"
           class="{{ $itemClass($active) }}"
           @if($active) aria-current="page" @endif>
          <span class="flex items-center">
            <span class="{{ $iconWrap($active) }}" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 12 12 3l9 9h-2v8h-6v-6H11v6H5v-8H3Z"/>
              </svg>
            </span>
            Dashboard
          </span>
        </a>

        <div class="mt-3 px-3 text-xs uppercase tracking-wide text-slate-500">Conteúdo</div>

        {{-- Negócios --}}
        @php $active = request()->routeIs('admin.businesses.*'); @endphp
        <a href="{{ route('admin.businesses.index') }}"
           class="{{ $itemClass($active) }}"
           @if($active) aria-current="page" @endif>
          <span class="flex items-center">
            <span class="{{ $iconWrap($active) }}" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 21V7h18v14H3Zm2-2h14V9H5v10Zm2-6h5v2H7v-2Z"/>
              </svg>
            </span>
            Negócios
          </span>
          @if(isset($tot['business']))
            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $fmt($tot['business']) }}</span>
          @endif
        </a>

        {{-- Categorias --}}
        @php $active = request()->routeIs('admin.categories.*'); @endphp
        <a href="{{ route('admin.categories.index') }}"
           class="{{ $itemClass($active) }}"
           @if($active) aria-current="page" @endif>
          <span class="flex items-center">
            <span class="{{ $iconWrap($active) }}" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 6h18v2H3V6Zm0 5h18v2H3v-2Zm0 5h18v2H3v-2Z"/>
              </svg>
            </span>
            Categorias
          </span>
          @if(isset($tot['categories']))
            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $fmt($tot['categories']) }}</span>
          @endif
        </a>

        {{-- Páginas (CRUD) --}}
        @if (Route::has('admin.pages.index'))
          @php $active = request()->routeIs('admin.pages.*'); @endphp
          <a href="{{ route('admin.pages.index') }}"
             class="{{ $itemClass($active) }}"
             @if($active) aria-current="page" @endif>
            <span class="flex items-center">
              <span class="{{ $iconWrap($active) }}" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2Zm8 2H6v16h12V9h-4V4Zm-6 6h8v2H8v-2Zm0 4h8v2H8v-2Z"/>
                </svg>
              </span>
              Páginas
            </span>
            @if(isset($tot['pages']))
              <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $fmt($tot['pages']) }}</span>
            @endif
          </a>
        @endif

        {{-- Cidades --}}
        @php $active = request()->routeIs('admin.cities.*'); @endphp
        <a href="{{ route('admin.cities.index') }}"
           class="{{ $itemClass($active) }}"
           @if($active) aria-current="page" @endif>
          <span class="flex items-center">
            <span class="{{ $iconWrap($active) }}" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 21V8l7-5 7 5v13H3Z"/>
              </svg>
            </span>
            Cidades
          </span>
          @if(isset($tot['cities']))
            <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $fmt($tot['cities']) }}</span>
          @endif
        </a>

        <div class="mt-3 px-3 text-xs uppercase tracking-wide text-slate-500">Sistema</div>

        {{-- Avaliações --}}
        @php $active = request()->routeIs('admin.reviews.*'); @endphp
        <a href="{{ route('admin.reviews.index') }}"
           class="{{ $itemClass($active) }}"
           @if($active) aria-current="page" @endif>
          <span class="flex items-center">
            <span class="{{ $iconWrap($active) }}" aria-hidden="true">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                <path d="m12 17.27 6.18 3.73-1.64-7.03L21 9.24l-7.19-.61L12 2 10.19 8.63 3 9.24l4.46 4.73L5.82 21 12 17.27Z"/>
              </svg>
            </span>
            Avaliações
          </span>
        </a>

        {{-- Anúncios (form único) --}}
        @if (Route::has('admin.advertisements.edit'))
          @php $active = request()->routeIs('admin.advertisements.*'); @endphp
          <a href="{{ route('admin.advertisements.edit') }}"
             class="{{ $itemClass($active) }}"
             @if($active) aria-current="page" @endif>
            <span class="flex items-center">
              <span class="{{ $iconWrap($active) }}" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M4 4h16v4H4V4Zm0 6h16v10H4V10Zm2 2v6h12v-6H6Z"/>
                </svg>
              </span>
              Anúncios
            </span>
            @if(isset($tot['ads']))
              <span class="ml-2 rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-700">{{ $fmt($tot['ads']) }}</span>
            @endif
          </a>
        @endif

        {{-- Usuários (se existir) --}}
        @if (Route::has('admin.users.index'))
          @php $active = request()->routeIs('admin.users.*'); @endphp
          <a href="{{ route('admin.users.index') }}"
             class="{{ $itemClass($active) }}"
             @if($active) aria-current="page" @endif>
            <span class="flex items-center">
              <span class="{{ $iconWrap($active) }}" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4Zm-8 0c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4Zm0 2c-2.673 0-8 1.337-8 4v2h16v-2c0-2.663-5.327-4-8-4Zm8 0c-.29 0-.616.02-.97.055 1.16.832 1.97 1.93 1.97 3.445V19h7v-2c0-2.663-5.327-4-8-4Z"/>
                </svg>
              </span>
              Usuários
            </span>
          </a>
        @endif

        {{-- Configurações (form único) --}}
        @if ($settingsRouteName)
          @php $active = request()->routeIs('admin.settings.*'); @endphp
          <a href="{{ route($settingsRouteName) }}"
             class="{{ $itemClass($active) }}"
             @if($active) aria-current="page" @endif>
            <span class="flex items-center">
              <span class="{{ $iconWrap($active) }}" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                  <path d="m12 3 2 1 2-1 2 3-1 2 1 2-2 2v2l-2 1-2-1-2 1-2-1v-2l-2-2 1-2-1-2 2-3 2 1 2-1Z"/>
                </svg>
              </span>
              Configurações
            </span>
          </a>
        @endif
      </nav>
    </div>
  </details>

</div>
