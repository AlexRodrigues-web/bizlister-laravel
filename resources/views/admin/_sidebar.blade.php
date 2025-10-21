<div class="p-4">
  <div class="mb-6">
    <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight">Painel</a>
    <div class="text-xs text-slate-500">Administração</div>
  </div>

  <nav class="space-y-1 text-sm">
    <a href="{{ route('admin.dashboard') }}"
       class="block rounded-lg px-3 py-2 hover:bg-slate-100">Dashboard</a>

    <div class="mt-3 text-xs uppercase text-slate-500 px-3">Conteúdo</div>
    <a href="{{ route('admin.businesses.index') }}"
       class="block rounded-lg px-3 py-2 hover:bg-slate-100">Negócios</a>
    <a href="{{ route('admin.categories.index') }}"
       class="block rounded-lg px-3 py-2 hover:bg-slate-100">Categorias</a>
    <a href="{{ route('admin.cities.index') }}"
       class="block rounded-lg px-3 py-2 hover:bg-slate-100">Cidades</a>

    <div class="mt-3 text-xs uppercase text-slate-500 px-3">Sistema</div>
    <a href="{{ route('admin.reviews.index') }}"
       class="block rounded-lg px-3 py-2 hover:bg-slate-100">Avaliações</a>

    @if (Route::has('admin.users.index'))
      <a href="{{ route('admin.users.index') }}"
         class="block rounded-lg px-3 py-2 hover:bg-slate-100">Usuários</a>
    @endif

    @if (Route::has('admin.settings.index'))
      <a href="{{ route('admin.settings.index') }}"
         class="block rounded-lg px-3 py-2 hover:bg-slate-100">Configurações</a>
    @endif
  </nav>
</div>
