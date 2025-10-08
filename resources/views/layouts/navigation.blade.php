<div class="bg-white border-b border-gray-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">

      <div class="flex">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
          <a href="{{ url('/') }}" class="font-semibold">BizLister</a>
        </div>

        <!-- Links (lado esquerdo) -->
        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
          <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            {{ __('Dashboard') }}
          </x-nav-link>

          <x-nav-link :href="route('categories.index')" :active="request()->routeIs('categories.*')">
            {{ __('Categorias') }}
          </x-nav-link>

          <x-nav-link :href="route('cities.index')" :active="request()->routeIs('cities.*')">
            {{ __('Cidades') }}
          </x-nav-link>

          <x-nav-link :href="route('search.index')" :active="request()->routeIs('search.*')">
            {{ __('Buscar') }}
          </x-nav-link>

          @can('admin')
            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.*')">
              {{ __('Admin') }}
            </x-nav-link>
          @endcan

          @auth
            <x-nav-link :href="route('business.create')" :active="request()->routeIs('business.create')">
              {{ __('Cadastrar Negócio') }}
            </x-nav-link>
          @endauth
        </div>
      </div>

      <!-- Lado direito -->
      <div class="hidden sm:flex sm:items-center sm:ml-6">
        @auth
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button type="button" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition">
                <div>{{ Auth::user()->name }}</div>
                <div class="ml-1">
                  <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                  </svg>
                </div>
              </button>
            </x-slot>

            <x-slot name="content">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                  {{ __('Sair') }}
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        @endauth

        @guest
          <a href="{{ route('login') }}" class="text-sm text-gray-600 hover:text-gray-900">
            {{ __('Entrar') }}
          </a>
        @endguest
      </div>

      <!-- Hamburger (mobile) -->
      <div class="-mr-2 flex items-center sm:hidden">
        <button class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none" type="button" aria-label="Abrir menu">
          <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" fill="none" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

    </div>
  </div>
</div>
