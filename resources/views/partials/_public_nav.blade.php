<nav class="mx-auto max-w-7xl px-4 py-3 flex flex-wrap items-center gap-4 text-sm">
  <a class="hover:underline" href="{{ route('categories.index') }}">Categorias</a>
  <a class="hover:underline" href="{{ route('cities.index') }}">Cidades</a>
  <a class="hover:underline" href="{{ route('search.index') }}">Buscar</a>
  <a class="hover:underline" href="/contato">Contato</a>

  <span class="opacity-50">|</span>

  @auth
    <a class="hover:underline" href="{{ route('dashboard') }}">Dashboard</a>
    @if(auth()->user()->is_admin ?? false)
      <a class="hover:underline" href="{{ route('admin.dashboard') }}">Admin</a>
    @endif
    <form method="POST" action="{{ route('logout') }}" class="inline">
      @csrf
      <button type="submit" class="hover:underline">Sair</button>
    </form>
  @else
    <a class="hover:underline" href="{{ route('login') }}">Login</a>
    @if (Route::has('register'))
      <a class="hover:underline" href="{{ route('register') }}">Registrar</a>
    @endif
  @endauth
</nav>
