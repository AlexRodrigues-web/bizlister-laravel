@php
  /** Defaults à prova de undefined */
  $footerPages   = ($footerPages   ?? collect()) ?: collect();
  $footerCities  = ($footerCities  ?? collect()) ?: collect();
  $settings      = $settings      ?? null;
  $year          = $year          ?? now()->year;
  $siteName      = data_get($settings, 'site_name', config('app.name', 'BizLister'));
@endphp

<footer class="site-footer mt-5">
  <div class="container">

    {{-- Topo do rodapé: colunas --}}
    <div class="footer-top row gy-4">

      <div class="col-12 col-md">
        <h6>Links úteis</h6>
        <ul class="list-unstyled mb-0">
          <li><a href="{{ url('/sobre') }}">Sobre Nós</a></li>
          <li><a href="{{ url('/termos') }}">Termos de Uso</a></li>
          <li><a href="{{ url('/politica-de-privacidade') }}">Política de Privacidade</a></li>
          <li><a href="{{ url('/contato') }}">Contato</a></li>
          <li><a href="{{ route('sitemap') }}">Sitemap</a></li>
        </ul>
      </div>

      @if($footerPages->count())
        <div class="col-12 col-md">
          <h6>Institucional</h6>
          <ul class="list-unstyled mb-0">
            @foreach($footerPages as $p)
              <li><a href="{{ url('/'.$p->slug) }}">{{ $p->title }}</a></li>
            @endforeach
          </ul>
        </div>
      @endif

      @if($footerCities->count())
        <div class="col-12 col-md">
          <h6>Cidades</h6>
          <ul class="list-unstyled mb-0">
            @foreach($footerCities->take(8) as $c)
              <li>
                <a href="{{ route('cities.show', [
                    'id' => data_get($c,'city_id', data_get($c,'id')),
                    'slug' => \Illuminate\Support\Str::slug(data_get($c,'name', data_get($c,'city','')))
                ]) }}">
                  {{ data_get($c,'name', data_get($c,'city','')) }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      @endif

    </div>

    {{-- Base do rodapé: marca e atalhos inline --}}
    <div class="footer-bottom d-flex flex-column flex-sm-row align-items-center justify-content-between">
      <div class="muted">
        © {{ $year }} <span class="brand">{{ $siteName }}</span>
      </div>
      <div class="inline-links mt-2 mt-sm-0">
        <a class="muted" href="{{ url('/') }}">Início</a>
        <a class="muted" href="{{ route('categories.index') }}">Categorias</a>
        <a class="muted" href="{{ route('cities.index') }}">Cidades</a>
        <a class="muted" href="{{ route('business.create') }}">Cadastrar negócio</a>
      </div>
    </div>

  </div>
</footer>
