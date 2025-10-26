{{-- resources/views/layouts/navigation.blade.php --}}
@php
    use Illuminate\Support\Str;
@endphp
<div class="bg-red-700 text-white">
  <div x-data="{ openNav:false }" class="mx-auto max-w-7xl px-4">
    <div class="flex h-14 items-center justify-between">

      {{-- ESQUERDA: Logo + menus principais --}}
      <div class="flex items-center gap-6">
        {{-- LOGO (coloque sua imagem legada em public/legacy/logo.png) --}}
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2">
          <img src="{{ asset('legacy/logo.png') }}" alt="BizLister" class="h-8 w-auto" />
          <span class="sr-only">BizLister</span>
        </a>

        {{-- Menus desktop --}}
        <nav class="hidden md:flex items-center gap-2 text-sm font-medium">
          <a href="{{ url('/') }}" class="px-3 py-2 hover:bg-red-800 rounded">Lar</a>

          {{-- NAVeGAR (dropdown simples com links fixos) --}}
          <div x-data="{ open:false }" class="relative">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    class="px-3 py-2 hover:bg-red-800 rounded inline-flex items-center gap-1">
              Navegar
              <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 12a1 1 0 01-.707-.293l-4-4a1 1 0 111.414-1.414L10 9.586l3.293-3.293a1 1 0 011.414 1.414l-4 4A1 1 0 0110 12z" clip-rule="evenodd"/></svg>
            </button>
            <div x-show="open" @mouseenter="open=true" @mouseleave="open=false"
                 x-transition
                 class="absolute left-0 mt-2 w-56 rounded-lg bg-white text-slate-800 shadow-lg ring-1 ring-black/5 z-30">
              <a href="{{ route('search.index') }}" class="block px-4 py-2 hover:bg-slate-100">Todos os negócios</a>
              <a href="{{ route('search.index', ['sort' => 'popular']) }}" class="block px-4 py-2 hover:bg-slate-100">negócios populares</a>
              <a href="{{ route('search.index', ['featured' => 1]) }}" class="block px-4 py-2 hover:bg-slate-100">Empresas em Destaque</a>
            </div>
          </div>

          {{-- CATEGORIAS (dropdown povoado do banco) --}}
          <div x-data="{ open:false }" class="relative">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    class="px-3 py-2 hover:bg-red-800 rounded inline-flex items-center gap-1">
              Categorias
              <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 12a1 1 0 01-.707-.293l-4-4a1 1 0 111.414-1.414L10 9.586l3.293-3.293a1 1 0 011.414 1.414l-4 4A1 1 0 0110 12z" clip-rule="evenodd"/></svg>
            </button>
            <div x-show="open" @mouseenter="open=true" @mouseleave="open=false"
                 x-transition
                 class="absolute left-0 mt-2 w-64 rounded-lg bg-white text-slate-800 shadow-lg ring-1 ring-black/5 z-30">
              @forelse($__navCats ?? collect() as $c)
                <a href="{{ route('categories.show', ['id' => $c->cat_id, 'slug' => $c->slug ?? Str::slug($c->label ?? '')]) }}"
                   class="block px-4 py-2 hover:bg-slate-100">
                  {{ $c->label ?? ('Categoria #'.$c->cat_id) }}
                </a>
              @empty
                <span class="block px-4 py-2 text-slate-500">Sem categorias</span>
              @endforelse
            </div>
          </div>

          {{-- LINKS públicos adicionais --}}
          <a href="{{ route('cities.index') }}" class="px-3 py-2 hover:bg-red-800 rounded">Cidades</a>
          <a href="{{ route('contact.show') }}" class="px-3 py-2 hover:bg-red-800 rounded">Contato</a>

          {{-- Link para área admin (se autorizado) --}}
          @can('admin')
            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 hover:bg-red-800 rounded">Admin</a>
          @endcan

          {{-- Cadastrar (enviar) – só logado --}}
          @auth
            <a href="{{ route('business.create') }}" class="px-3 py-2 hover:bg-red-800 rounded">Cadastrar Negócio</a>
          @endauth
        </nav>
      </div>

      {{-- DIREITA: conta/entrar --}}
      <div class="hidden md:flex items-center gap-4 text-sm">
        @auth
          <div x-data="{ open:false }" class="relative">
            <button @click="open=!open" class="inline-flex items-center gap-2 px-3 py-2 bg-red-800/50 hover:bg-red-800 rounded">
              {{ Auth::user()->name }}
              <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd"/></svg>
            </button>
            <div x-show="open" @click.outside="open=false" x-transition
                 class="absolute right-0 mt-2 w-48 rounded-lg bg-white text-slate-800 shadow-lg ring-1 ring-black/5">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-4 py-2 hover:bg-slate-100">Sair</button>
              </form>
            </div>
          </div>
        @else
          <a href="{{ route('login') }}" class="px-3 py-2 bg-red-800/50 hover:bg-red-800 rounded">Entrar</a>
        @endauth
      </div>

      {{-- MOBILE: botão --}}
      <button @click="openNav=!openNav" class="md:hidden inline-flex items-center justify-center p-2 rounded hover:bg-red-800"
              aria-label="Abrir menu">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </div>

    {{-- MOBILE: menu colapsável --}}
    <div x-show="openNav" x-transition class="md:hidden pb-4 space-y-1 text-sm">
      <a href="{{ url('/') }}" class="block px-3 py-2 rounded hover:bg-red-800">Lar</a>

      <details class="px-2" >
        <summary class="cursor-pointer px-1 py-2 rounded hover:bg-red-800">Navegar</summary>
        <div class="mt-1 ml-3 space-y-1 bg-white text-slate-800 rounded">
          <a class="block px-3 py-2 hover:bg-slate-100" href="{{ route('search.index') }}">Todos os negócios</a>
          <a class="block px-3 py-2 hover:bg-slate-100" href="{{ route('search.index',['sort'=>'popular']) }}">negócios populares</a>
          <a class="block px-3 py-2 hover:bg-slate-100" href="{{ route('search.index',['featured'=>1]) }}">Empresas em Destaque</a>
        </div>
      </details>

      <details class="px-2">
        <summary class="cursor-pointer px-1 py-2 rounded hover:bg-red-800">Categorias</summary>
        <div class="mt-1 ml-3 space-y-1 bg-white text-slate-800 rounded">
          @forelse($__navCats ?? collect() as $c)
            <a class="block px-3 py-2 hover:bg-slate-100"
               href="{{ route('categories.show', ['id' => $c->cat_id, 'slug' => $c->slug ?? Str::slug($c->label ?? '')]) }}">
              {{ $c->label ?? ('Categoria #'.$c->cat_id) }}
            </a>
          @empty
            <span class="block px-3 py-2 text-slate-500">Sem categorias</span>
          @endforelse
        </div>
      </details>

      <a href="{{ route('cities.index') }}" class="block px-3 py-2 rounded hover:bg-red-800">Cidades</a>
      <a href="{{ route('contact.show') }}" class="block px-3 py-2 rounded hover:bg-red-800">Contato</a>

      @can('admin')
        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-red-800">Admin</a>
      @endcan
      @auth
        <a href="{{ route('business.create') }}" class="block px-3 py-2 rounded hover:bg-red-800">Cadastrar Negócio</a>
      @else
        <a href="{{ route('login') }}" class="block px-3 py-2 rounded hover:bg-red-800">Entrar</a>
      @endauth
    </div>
  </div>
</div>
