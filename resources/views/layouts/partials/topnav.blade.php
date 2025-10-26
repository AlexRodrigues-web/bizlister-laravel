@php
  // --------- Fallback seguro: carrega categorias se o composer não forneceu ---------
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

  // --------- Carrega subcategorias e agrupa por category_id (compatível com legado) ---------
  $__subsByCat = collect();
  try {
      if (\Illuminate\Support\Facades\Schema::hasTable('subcategories')) {
          $scols   = \Illuminate\Support\Facades\Schema::getColumnListing('subcategories');
          $catCol  = in_array('category_id',$scols,true) ? 'category_id' : null;
          $nameCol = in_array('name',$scols,true)        ? 'name'
                   : (in_array('title',$scols,true)      ? 'title' : null);
          $slugCol = in_array('slug',$scols,true)        ? 'slug' : null;

          if ($catCol && $nameCol && $slugCol) {
              $subs = \Illuminate\Support\Facades\DB::table('subcategories')
                  ->selectRaw("$catCol AS category_id, $nameCol AS name, $slugCol AS slug")
                  ->orderBy($nameCol)
                  ->get();

              $__subsByCat = $subs->groupBy('category_id');
          }
      }
  } catch (\Throwable $e) { /* silencioso */ }
@endphp

<nav class="bl-navbar" role="navigation" aria-label="Barra principal">
  <div class="bl-container">

    {{-- LOGO (usa o arquivo legado em public/legacy/logo.png) --}}
    <a class="bl-brand" href="{{ url('/') }}" aria-label="Página inicial">
      <img src="{{ asset('legacy/logo.png') }}" alt="BizLister" class="bl-logo">
    </a>

    <ul class="bl-menu">
      {{-- INÍCIO --}}
      <li><a href="{{ url('/') }}">Painel</a></li>

      {{-- NAVEGAR ▾ (abre no clique) --}}
      <li class="bl-has-dropdown">
        <button class="bl-dropbtn" type="button">
          Navegar <span class="bl-caret" aria-hidden="true">▾</span>
        </button>
        <ul class="bl-dropdown">
          <li><a href="{{ route('search.index') }}">Todos os negócios</a></li>
          <li><a href="{{ route('search.index', ['sort' => 'popular']) }}">Negócios populares</a></li>
          <li><a href="{{ route('search.index', ['featured' => 1]) }}">Empresas em destaque</a></li>
        </ul>
      </li>

      {{-- CATEGORIAS ▾ (dinâmico, abre no clique) --}}
      <li class="bl-has-dropdown">
        <button class="bl-dropbtn" type="button">
          Categorias <span class="bl-caret" aria-hidden="true">▾</span>
        </button>
        <ul class="bl-dropdown bl-dropdown-wide">
          @forelse($__cats as $c)
            @php
              $catId   = $c->cat_id;
              $catName = $c->label ?? ('Categoria #'.$catId);
              $slug    = \Illuminate\Support\Str::slug($catName);
              $subs    = ($__subsByCat[$catId] ?? collect())->take(6);
              $total   = ($__subsByCat[$catId] ?? collect())->count();
            @endphp

            <li class="mb-2">
              <a href="{{ route('categories.show', [$catId, $slug]) }}" class="d-block">
                {{ $catName }}
              </a>

              {{-- Subcategorias (badges) --}}
              @if($subs->count())
                <div class="mt-1" aria-label="Subcategorias de {{ $catName }}">
                  @foreach($subs as $sub)
                    <a href="{{ route('subcategories.show', $sub->slug) }}"
                       class="bl-sub-badge"
                       title="{{ $sub->name }}">
                      {{ $sub->name }}
                    </a>
                  @endforeach

                  @if($total > 6)
                    <a href="{{ route('categories.show', [$catId, $slug]) }}" class="bl-sub-badge bl-more">
                      + ver todas
                    </a>
                  @endif
                </div>
              @endif
            </li>

            @if(!$loop->last)
              <li><hr class="dropdown-divider"></li>
            @endif
          @empty
            <li class="bl-empty">Sem categorias</li>
          @endforelse
        </ul>
      </li>

      {{-- CIDADES --}}
      <li><a href="{{ route('cities.index') }}">Cidades</a></li>

      {{-- BUSCA --}}
      <li><a href="{{ route('search.index') }}">Buscar</a></li>

      {{-- CONTATO --}}
      <li><a href="{{ route('contact.show') }}">Contato</a></li>

      {{-- ESPAÇADOR --}}
      <li class="bl-spacer" aria-hidden="true"></li>

      {{-- ENVIAR (cadastrar negócio) --}}
      <li><a class="bl-cta" href="{{ route('business.create') }}">Cadastrar Negócio</a></li>

      {{-- MINHA CONTA ▾ (abre no clique) --}}
      <li class="bl-has-dropdown">
        <button class="bl-dropbtn" type="button">
          Minha conta <span class="bl-caret" aria-hidden="true">▾</span>
        </button>
        <ul class="bl-dropdown">
          @auth
            <li class="bl-muted">Olá, {{ auth()->user()->name }}</li>
            <li><a href="{{ route('dashboard') }}">Meu painel</a></li>
            @can('admin')
              <li><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            @endcan
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="bl-linklike" type="submit">Sair</button>
              </form>
            </li>
          @else
            <li><a href="{{ route('login') }}">Entrar</a></li>
            @if (Route::has('register'))
              <li><a href="{{ route('register') }}">Registrar</a></li>
            @endif
          @endauth
        </ul>
      </li>
    </ul>
  </div>
</nav>

<style>
  /* ====== ESTILO DA BARRA (legado modernizado + clique) ====== */
  .bl-navbar{background:#c41210;color:#fff;position:sticky;top:0;z-index:50}
  .bl-container{max-width:1220px;margin:0 auto;display:flex;align-items:center;gap:18px;padding:10px 16px; min-height:64px}
  .bl-brand{display:inline-flex;align-items:center}
  .bl-logo{height:70px; width:auto; display:block}
  .bl-menu{display:flex;align-items:center;gap:6px;list-style:none;margin:0;padding:0;flex:1}
  .bl-menu>li>a,
  .bl-dropbtn{color:#fff;text-decoration:none;font-weight:700;letter-spacing:.2px;padding:8px 12px;border-radius:4px}
  .bl-menu>li>a:hover,
  .bl-dropbtn:hover{background:#aa0f0e}
  .bl-cta{background:#fff;color:#c41210 !important;border-radius:6px}
  .bl-cta:hover{filter:brightness(.95)}
  .bl-has-dropdown{position:relative}
  .bl-dropbtn{background:transparent;border:0;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
  .bl-caret{opacity:.9;font-size:12px}
  .bl-dropdown{position:absolute;top:100%;left:0;min-width:220px;background:#fff;color:#1f2937;border-radius:8px;box-shadow:0 10px 25px rgba(0,0,0,.18);padding:8px;margin-top:8px;display:none}
  .bl-dropdown-wide{min-width:260px}
  /* 👉 abre só quando o <li> tiver a classe .open (controlada por JS) */
  .bl-has-dropdown.open > .bl-dropdown{display:block}
  .bl-dropdown>li>a{display:block;padding:10px 12px;border-radius:6px;color:#111827;text-decoration:none;font-weight:600}
  .bl-dropdown>li>a:hover{background:#f2f2f2}
  .bl-dropdown .bl-muted{padding:8px 12px;color:#6b7280;font-size:12px}
  .bl-dropdown .bl-empty{padding:10px 12px;color:#6b7280}
  .bl-linklike{background:none;border:0;padding:10px 12px;width:100%;text-align:left;border-radius:6px;font-weight:600;cursor:pointer;color:#111827}
  .bl-linklike:hover{background:#f2f2f2}
  .bl-spacer{flex:1}

  /* Badges de subcategoria */
  .bl-sub-badge{
    display:inline-block; font-size:12px; line-height:1;
    padding:6px 8px; margin:2px 6px 2px 0;
    border-radius:999px; background:#f3f4f6; color:#111827;
    text-decoration:none; font-weight:600; border:1px solid #e5e7eb;
  }
  .bl-sub-badge:hover{background:#e5e7eb}
  .bl-sub-badge.bl-more{background:#eef2ff; border-color:#e0e7ff}

  @media (max-width: 480px){
    .bl-dropdown{position:fixed;left:16px;right:16px}
  }
</style>

<script>
  // Controle de dropdown por clique (fecha ao clicar fora ou Esc)
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

      btn.setAttribute('aria-haspopup','true');
      btn.setAttribute('aria-expanded','false');

      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const isOpen = li.classList.contains('open');
        closeAll();
        if (!isOpen) {
          li.classList.add('open');
          btn.setAttribute('aria-expanded','true');
        }
      });

      // não fecha ao clicar dentro do dropdown
      dd.addEventListener('click', (e) => e.stopPropagation());
    });

    document.addEventListener('click', closeAll);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeAll(); });
  })();
</script>
