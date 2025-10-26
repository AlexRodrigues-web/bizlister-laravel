<div class="card mb-4">
  <div class="card-body">
    @php
      use Illuminate\Support\Facades\Schema;
      use Illuminate\Support\Facades\DB;
      use Illuminate\Support\Str;

      // Detecta tabela/colunas
      $cityTable = Schema::hasTable('city') ? 'city' : (Schema::hasTable('cities') ? 'cities' : null);
      $cities = collect();

      if ($cityTable) {
        $cols = Schema::getColumnListing($cityTable);
        $idCol   = in_array('city_id',$cols,true) ? 'city_id' : (in_array('id',$cols,true) ? 'id' : null);
        $nameCol = in_array('name',$cols,true)    ? 'name'    : (in_array('city',$cols,true) ? 'city' : null);
        $slugCol = in_array('slug',$cols,true)    ? 'slug'    : null;
        $actCol  = in_array('is_active',$cols,true) ? 'is_active' : null;

        if ($idCol) {
          $labelSql = $nameCol ? $nameCol : "CONCAT('Cidade #', $idCol)";
          $slugSql  = $slugCol ? $slugCol : "NULL";
          $query = DB::table($cityTable)
            ->selectRaw("$idCol AS city_id, $labelSql AS label, $slugSql AS slug");
          if ($actCol) $query->where($actCol, 1);
          $query->orderBy($nameCol ?: $idCol);
          $cities = $query->get();
        }
      }

      // Seleção atual: aceita slug (?city) ou id (?sid / ?city_id)
      $selSlug = (string) request('city', '');
      $selId   = (string) (request('sid', request('city_id', '')));
    @endphp

    <form method="GET" action="{{ route('search.index') }}" accept-charset="UTF-8" class="row g-3">
      <div class="col-12 col-md-3">
        <label for="city" class="form-label">Cidade</label>
        <select name="city" id="city" class="form-select">
          <option value="">Todas as Cidades</option>
          @foreach($cities as $c)
            @php
              $value = $c->slug ?: Str::slug($c->label ?? '');
              // selected se o slug bate OU, na falta de slug, se o id bate
              $isSelected = $selSlug !== ''
                ? ($selSlug === $value)
                : (($selId !== '') && ((string)$selId === (string)$c->city_id));
            @endphp
            <option value="{{ $value }}" {{ $isSelected ? 'selected' : '' }}>
              {{ $c->label }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-12 col-md">
        <label for="q" class="form-label">Buscar</label>
        <input type="text" name="q" id="q"
               value="{{ request('q') }}"
               placeholder="O que você procura?"
               class="form-control">
      </div>

      <div class="col-12 col-md-auto align-self-end">
        <button type="submit" class="btn btn-primary">Pesquisar</button>
      </div>
    </form>
  </div>
</div>
