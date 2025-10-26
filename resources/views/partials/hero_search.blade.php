<section class="bl-hero py-4 py-md-5">
  <div class="container">
    <h1 class="h4 fw-bold mb-3">Procurar</h1>

    @php
      use Illuminate\Support\Facades\Schema;
      use Illuminate\Support\Facades\DB;
      use Illuminate\Support\Str;

      // Detecta tabela e colunas
      $cityTable = Schema::hasTable('city') ? 'city' : (Schema::hasTable('cities') ? 'cities' : null);
      $cities = collect();

      if ($cityTable) {
        $cols = Schema::getColumnListing($cityTable);
        $idCol   = in_array('city_id',$cols,true) ? 'city_id' : (in_array('id',$cols,true) ? 'id' : null);
        $nameCol = in_array('name',$cols,true)    ? 'name'    : (in_array('city',$cols,true) ? 'city' : null);
        $slugCol = in_array('slug',$cols,true)    ? 'slug'    : null;
        $ufCol   = in_array('state',$cols,true)   ? 'state'   : (in_array('uf',$cols,true) ? 'uf' : null);
        $actCol  = in_array('is_active',$cols,true) ? 'is_active' : null;

        if ($idCol) {
          $labelSql = $nameCol ? $nameCol : "CONCAT('Cidade #', $idCol)";
          $slugSql  = $slugCol ? $slugCol : "NULL";
          $ufSql    = $ufCol   ? $ufCol   : "NULL";

          $q = DB::table($cityTable)
            ->selectRaw("$idCol AS city_id, $labelSql AS label, $slugSql AS slug, $ufSql AS uf");
          if ($actCol) $q->where($actCol, 1);
          $q->orderBy($nameCol ?: $idCol);

          $cities = $q->get()->map(function($c){
            // Anexa UF no rótulo se existir
            $uf = is_string($c->uf ?? null) ? trim($c->uf) : '';
            $c->full_label = $uf ? "{$c->label} ({$uf})" : $c->label;
            return $c;
          });
        }
      }

      // Seleção atual: por slug (?city) OU id (?sid / ?city_id)
      $selSlug = (string) request('city', '');
      $selId   = (string) (request('sid', request('city_id', '')));
    @endphp

    <form method="GET" action="{{ route('search.index') }}" accept-charset="UTF-8" class="row g-2 g-md-3 align-items-end">
      <div class="col-12 col-md-4">
        <label for="hero_city" class="form-label">Cidade</label>
        <select name="city" id="hero_city" class="form-select">
          <option value="">Todas as Cidades</option>
          @foreach ($cities as $c)
            @php
              // valor do option: prioriza slug; fallback para slug do label
              $value = $c->slug ?: Str::slug($c->label ?? '');
              $isSelected = $selSlug !== ''
                ? ($selSlug === $value)
                : (($selId !== '') && ((string)$selId === (string)$c->city_id));
            @endphp
            <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
              {{ $c->full_label ?? $c->label }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md">
        <label for="hero_q" class="form-label">O que você procura?</label>
        <input
          type="text"
          name="q"
          id="hero_q"
          value="{{ request('q') }}"
          class="form-control"
          placeholder="Ex.: pizzaria, hotel, eletricista..."
        >
      </div>

      <div class="col-12 col-md-auto">
        <button type="submit" class="btn btn-primary w-100">
          🔎 Pesquisar
        </button>
      </div>
    </form>
  </div>
</section>

<style>
  .bl-hero{background:#f8fafc;border-bottom:1px solid #e9ecef}
</style>
