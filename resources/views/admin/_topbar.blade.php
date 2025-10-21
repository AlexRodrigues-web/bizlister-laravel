<div class="flex items-center gap-4 px-4 md:px-6 h-14">
  <a href="{{ route('admin.dashboard') }}" class="text-lg font-semibold">
    BizLister
  </a>

  <nav class="hidden md:flex items-center gap-2 text-sm">
    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Painel</a>

    @if (Route::has('admin.categories.index'))
      <a href="{{ route('admin.categories.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Categorias</a>
    @endif

    @if (Route::has('admin.cities.index'))
      <a href="{{ route('admin.cities.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Cidades</a>
    @endif

    @if (Route::has('admin.reviews.index'))
      <a href="{{ route('admin.reviews.index') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Avaliações</a>
    @endif

    @if (Route::has('admin.businesses.create'))
      <a href="{{ route('admin.businesses.create') }}" class="px-3 py-1.5 rounded-lg hover:bg-slate-100">Cadastrar Negócio</a>
    @endif
  </nav>

  <form method="get" action="{{ route('admin.dashboard') }}" class="ml-auto hidden md:flex">
    <input name="q" value="{{ request('q') }}" placeholder="Buscar"
           class="rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:outline-none">
  </form>

  <div class="text-sm text-slate-600 ml-2">
    {{ auth()->user()->name ?? 'Admin' }}
  </div>
</div>
