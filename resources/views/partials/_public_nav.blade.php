@php
  // Carrega categorias para o dropdown (compatível com 'categories' ou 'category')
  $__cats = $__navCats ?? collect();
  if ($__cats->isEmpty()) {
      try {
          $catTable = \Illuminate\Support\Facades\Schema::hasTable('categories') ? 'categories'
                    : (\Illuminate\Support\Facades\Schema::hasTable('category') ? 'category' : null);
          if ($catTable) {
              $cols = \Illuminate\Support\Facades\Schema::getColumnListing($catTable);
              $id   = in_array('cat_id',$cols,true) ? 'cat_id' : (in_array('id',$cols,true) ? 'id' : null);
              $lbl  = collect(['category','category_name','cat_name','name','title','label'])
                      ->first(fn($c)=>in_array($c,$cols,true));
              if ($id) {
                  $__cats = \Illuminate\Support\Facades\DB::table($catTable)
                      ->selectRaw("$id AS cat_id, ".($lbl ? "$lbl" : "CONCAT('Categoria #', $id)")." AS label")
                      ->orderBy($lbl ?: $id)
                      ->get();
              }
          }
      } catch (\Throwable $e) { /* silencioso */ }
  }

  // classes de estado ativo/inativo
  $linkBase = 'inline-flex items-center rounded-md px-3 py-2 font-semibold tracking-tight';
  $active   = $linkBase.' bg-white/15';
  $idle     = $linkBase.' hover:bg-white/10';
@endphp

<nav class="bl-navbar" role="navigation" aria-label="Barra principal">
  <div class="bl-container">

    {{-- LOGO (public/legacy/logo.png) --}}
    <a class="bl-brand" href="{{ url('/') }}" aria-label="Página inicial">
      <img src="{{ asset('legacy/logo.png') }}" alt="BizLister" class="bl-logo" height="80" width="auto">
    </a>

    <ul class="bl-menu" role="menubar">
      {{-- Início --}}
      <li role="none">
        <a role="menuitem" href="{{ url('/') }}"
           class="{{ request()->is('/') ? $active : $idle }}">Início</a>
      </li>

      {{-- Navegar --}}
      <li class="bl-has-dropdown" role="none">
        <button class="bl-dropbtn" type="button" aria-haspopup="true" aria-expanded="false">
          Navegar <span class="bl-caret" aria-hidden="true">▾</span>
        </button>
        <ul class="bl-dropdown" role="menu" aria-label="Navegar">
          <li role="none"><a role="menuitem" href="{{ route('search.index') }}">Todos os negócios</a></li>
          <li role="none"><a role="menuitem" href="{{ route('search.index', ['sort' => 'popular']) }}">Negócios populares</a></li>
          <li role="none"><a role="menuitem" href="{{ route('search.index', ['featured' => 1]) }}">Empresas em destaque</a></li>
        </ul>
      </li>

      {{-- Categorias --}}
      <li class="bl-has-dropdown" role="none">
        <button class="bl-dropbtn" type="button" aria-haspopup="true" aria-expanded="false">
          Categorias <span class="bl-caret" aria-hidden="true">▾</span>
        </button>
        <ul class="bl-dropdown bl-dropdown-wide" role="menu" aria-label="Categorias">
          @forelse($__cats as $c)
            @php $slug = \Illuminate\Support\Str::slug($c->label ?? ''); @endphp
            <li role="none">
              <a role="menuitem" href="{{ route('categories.show', [$c->cat_id, $slug]) }}">
                {{ $c->label ?? ('Categoria #'.$c->cat_id) }}
              </a>
            </li>
          @empty
            <li class="bl-empty" role="none">Sem categorias</li>
          @endforelse
        </ul>
      </li>

      {{-- Cidades / Buscar / Contato --}}
      <li role="none">
        <a role="menuitem" href="{{ route('cities.index') }}"
           class="{{ request()->routeIs('cities.*') ? $active : $idle }}">Cidades</a>
      </li>
      <li role="none">
        <a role="menuitem" href="{{ route('search.index') }}"
           class="{{ request()->routeIs('search.*') ? $active : $idle }}">Buscar</a>
      </li>
      <li role="none">
        <a role="menuitem" href="{{ route('contact.show') }}"
           class="{{ request()->routeIs('contact.*') ? $active : $idle }}">Contato</a>
      </li>

      <li class="bl-spacer" aria-hidden="true"></li>

      {{-- Cadastrar negócio --}}
      <li role="none">
        <a role="menuitem" class="bl-cta" href="{{ route('business.create') }}">Cadastrar Negócio</a>
      </li>

      {{-- Minha conta --}}
      <li class="bl-has-dropdown" role="none">
        <button class="bl-dropbtn" type="button" aria-haspopup="true" aria-expanded="false">
          Minha conta <span class="bl-caret" aria-hidden="true">▾</span>
        </button>
        <ul class="bl-dropdown" role="menu" aria-label="Minha conta">
          @auth
            <li class="bl-muted" role="none">Olá, {{ auth()->user()->name }}</li>
            <li role="none"><a role="menuitem" href="{{ route('profile.show') }}">Meu perfil</a></li>
            <li role="none"><a role="menuitem" href="{{ route('dashboard') }}">Meu painel</a></li>
            @can('admin')
              <li role="none"><a role="menuitem" href="{{ route('admin.dashboard') }}">Admin</a></li>
            @endcan
            <li role="none">
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bl-linklike" type="submit">Sair</button>
              </form>
            </li>
          @else
            <li role="none"><a role="menuitem" href="{{ route('login') }}">Entrar</a></li>
            @if (Route::has('register'))
              <li role="none"><a role="menuitem" href="{{ route('register') }}">Registrar</a></li>
            @endif
          @endauth
        </ul>
      </li>
    </ul>
  </div>
</nav>

<style>
  .bl-navbar{background:#c41210;color:#fff;position:sticky;top:0;z-index:50}
  .bl-container{max-width:1220px;margin:0 auto;display:flex;align-items:center;gap:18px;padding:10px 16px;min-height:72px}
  .bl-brand{display:inline-flex;align-items:center}
  .bl-logo{height:80px;width:auto;display:block}
  .bl-menu{display:flex;align-items:center;gap:6px;list-style:none;margin:0;padding:0;flex:1}
  .bl-menu>li>a,.bl-dropbtn{color:#fff;text-decoration:none;letter-spacing:.2px;padding:8px 12px;border-radius:8px}
  .bl-menu>li>a:hover,.bl-dropbtn:hover{background:#aa0f0e}
  .bl-cta{background:#fff;color:#c41210 !important;border-radius:8px;font-weight:800}
  .bl-cta:hover{filter:brightness(.95)}
  .bl-has-dropdown{position:relative}
  .bl-dropbtn{background:transparent;border:0;cursor:pointer;display:inline-flex;align-items:center;gap:6px;font-weight:800}
  .bl-caret{opacity:.9;font-size:12px}
  .bl-dropdown{position:absolute;top:100%;left:0;min-width:220px;background:#fff;color:#1f2937;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,.18);padding:8px;margin-top:8px;display:none}
  .bl-dropdown-wide{min-width:260px;max-height:60vh;overflow:auto}
  .bl-has-dropdown.open > .bl-dropdown{display:block}
  .bl-dropdown>li>a{display:block;padding:10px 12px;border-radius:8px;color:#111827;text-decoration:none;font-weight:700}
  .bl-dropdown>li>a:hover{background:#f2f2f2}
  .bl-dropdown .bl-muted{padding:8px 12px;color:#6b7280;font-size:12px}
  .bl-dropdown .bl-empty{padding:10px 12px;color:#6b7280}
  .bl-linklike{background:none;border:0;padding:10px 12px;width:100%;text-align:left;border-radius:8px;font-weight:700;cursor:pointer;color:#111827}
  .bl-linklike:hover{background:#f2f2f2}
  .bl-spacer{flex:1}
  @media (max-width: 480px){
    .bl-dropdown{position:fixed;left:16px;right:16px}
  }
</style>

<script>
  // Dropdown por clique com acessibilidade básica
  (function () {
    const items = document.querySelectorAll('.bl-has-dropdown');

    function closeAll() {
      items.forEach(li => {
        li.classList.remove('open');
        const btn = li.querySelector('.bl-dropbtn');
        if (btn) btn.setAttribute('aria-expanded','false');
      });
    }

    items.forEach(li => {
      const btn = li.querySelector('.bl-dropbtn');
      const dd  = li.querySelector('.bl-dropdown');
      if (!btn || !dd) return;

      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const willOpen = !li.classList.contains('open');
        closeAll();
        if (willOpen) {
          li.classList.add('open');
          btn.setAttribute('aria-expanded','true');
          // foca o primeiro link do menu quando abrir
          const firstLink = dd.querySelector('a,button');
          if (firstLink) setTimeout(() => firstLink.focus(), 0);
        }
      });

      // mantém aberto ao interagir dentro do dropdown
      dd.addEventListener('click', (e) => e.stopPropagation());

      // fecha ao sair com Tab do último item
      dd.addEventListener('keydown', (e) => {
        if (e.key === 'Tab' && !e.shiftKey) {
          const focusables = dd.querySelectorAll('a,button');
          if (focusables.length && document.activeElement === focusables[focusables.length - 1]) {
            closeAll();
          }
        }
      });
    });

    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });
  })();
</script>
