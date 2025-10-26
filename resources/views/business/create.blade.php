{{-- resources/views/business/create.blade.php --}}
@extends('layouts.app', ['title' => 'Cadastrar Negócio'])

@section('content')
<div class="container py-4" style="max-width: 860px;">
  <header class="mb-3">
    <h1 class="h3 fw-semibold mb-1">Cadastrar Negócio</h1>
    <p class="text-muted small mb-0">Preencha os campos abaixo para incluir um novo negócio no BizLister.</p>
  </header>

  {{-- Sumário de erros (acessível) --}}
  @if ($errors->any())
    <div class="alert alert-danger" role="alert" aria-live="polite">
      <div class="fw-semibold mb-1">Verifique os campos abaixo:</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Avisos quando fonte de dados vier vazia (não bloqueia o envio) --}}
  @if (($categories ?? collect())->count() === 0)
    <div class="alert alert-warning small" role="status">
      Nenhuma categoria encontrada. Verifique se a tabela <code>categories</code> existe e possui as colunas <code>id</code>/<code>name</code>
      (ou os aliases legados <code>cat_id</code>/<code>category</code>/<code>category_name</code>).
    </div>
  @endif
  @if (($cities ?? collect())->count() === 0)
    <div class="alert alert-warning small" role="status">
      Nenhuma cidade encontrada. Verifique se a tabela <code>city</code> (ou <code>cities</code>) existe e possui as colunas <code>id</code>/<code>name</code>
      (ou os aliases legados <code>city_id</code>/<code>city</code>).
    </div>
  @endif

  <form id="biz-create-form"
        method="POST"
        action="{{ route('business.store') }}"
        enctype="multipart/form-data"
        novalidate>
    @csrf

    {{-- Nome do Negócio --}}
    <div class="mb-3">
      <label for="business_name" class="form-label">Nome do Negócio <span class="text-danger">*</span></label>
      <input
        id="business_name"
        type="text"
        name="business_name"
        value="{{ old('business_name') }}"
        required
        maxlength="120"
        autocomplete="organization"
        class="form-control @error('business_name') is-invalid @enderror">
      <div class="form-text">Ex.: “Pizzaria Central”, “Hotel Centro”.</div>
      @error('business_name')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Categoria --}}
    <div class="mb-3">
      <label for="cid" class="form-label">Categoria <span class="text-danger">*</span></label>
      <select
        id="cid"
        name="cid"
        required
        class="form-select @error('cid') is-invalid @enderror"
        aria-describedby="cidHelp">
        <option value="">Selecione...</option>
        @foreach(($categories ?? collect()) as $c)
          @php
            // Compat: aceita tanto objetos quanto stdClass/arrays do DB::table
            $catId   = is_object($c) ? ($c->id ?? $c->cat_id ?? null) : ($c['id'] ?? $c['cat_id'] ?? null);
            $catText = is_object($c)
              ? ($c->name ?? $c->category_name ?? $c->category ?? $c->label ?? ($catId ? "Categoria #{$catId}" : 'Categoria'))
              : ($c['name'] ?? $c['category_name'] ?? $c['category'] ?? $c['label'] ?? ($catId ? "Categoria #{$catId}" : 'Categoria'));
          @endphp
          <option value="{{ $catId }}" {{ (string)old('cid') === (string)$catId ? 'selected' : '' }}>
            {{ $catText }}
          </option>
        @endforeach
      </select>
      <div id="cidHelp" class="form-text">
        A lista é carregada de <code>categories</code>.
      </div>
      @error('cid')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Subcategoria (opcional) — NOVO --}}
    <div class="mb-3">
      <label for="subcategory_id" class="form-label">Subcategoria</label>
      <select
        id="subcategory_id"
        name="subcategory_id"
        class="form-select @error('subcategory_id') is-invalid @enderror"
        aria-describedby="subHelp">
        <option value="">— (opcional) —</option>
        {{-- opções carregadas via JS conforme a categoria --}}
      </select>
      <div id="subHelp" class="form-text">
        Subcategorias são carregadas automaticamente após escolher a categoria.
      </div>
      @error('subcategory_id')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Cidade --}}
    <div class="mb-3">
      <label for="sid" class="form-label">Cidade <span class="text-danger">*</span></label>
      <select
        id="sid"
        name="sid"
        required
        class="form-select @error('sid') is-invalid @enderror"
        aria-describedby="sidHelp">
        <option value="">Selecione...</option>
        @foreach(($cities ?? collect()) as $s)
          @php
            $cityId   = is_object($s) ? ($s->id ?? $s->city_id ?? null) : ($s['id'] ?? $s['city_id'] ?? null);
            $cityText = is_object($s)
              ? ($s->name ?? $s->city ?? $s->label ?? ($cityId ? "Cidade #{$cityId}" : 'Cidade'))
              : ($s['name'] ?? $s['city'] ?? $s['label'] ?? ($cityId ? "Cidade #{$cityId}" : 'Cidade'));
          @endphp
          <option value="{{ $cityId }}" {{ (string)old('sid') === (string)$cityId ? 'selected' : '' }}>
            {{ $cityText }}
          </option>
        @endforeach
      </select>
      <div id="sidHelp" class="form-text">A lista é carregada de <code>city</code>/<code>cities</code>.</div>
      @error('sid')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Descrição --}}
    <div class="mb-3">
      <label for="description" class="form-label">Descrição</label>
      <textarea
        id="description"
        name="description"
        rows="4"
        maxlength="2000"
        class="form-control @error('description') is-invalid @enderror"
        placeholder="Conte um pouco sobre o negócio...">{{ old('description') }}</textarea>
      <div class="form-text">Dicas: serviços oferecidos, diferenciais, horário, formas de contato.</div>
      @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror
    </div>

    {{-- Imagem (com preview) --}}
    <div class="mb-4">
      <label for="image" class="form-label">Imagem (opcional)</label>
      <input
        id="image"
        type="file"
        name="image"
        accept=".jpg,.jpeg,.png,.webp"
        class="form-control @error('image') is-invalid @enderror">
      <div class="form-text">Até 2MB. Formatos: JPG, PNG, WEBP. Use imagens na horizontal para melhor resultado.</div>
      @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
      @enderror

      {{-- Preview inline (aparece após escolher arquivo) --}}
      <div id="image-preview" class="mt-2 d-none">
        <div class="ratio ratio-4x3 bg-light rounded overflow-hidden">
          <img id="image-preview-img" alt="Pré-visualização" class="w-100 h-100" style="object-fit:cover;">
        </div>
      </div>
    </div>

    {{-- Ações --}}
    <div class="d-flex gap-2">
      <button id="submit-btn" type="submit" class="btn btn-primary">
        <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
        Salvar
      </button>
      <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>

    {{-- ====== Bloco de compatibilidade (mantido; não afeta back) ====== --}}
    <template id="compat-names" hidden>
      <input name="business_name">
      <input name="description">
      <input name="cid">
      <input name="sid">
      <input name="subcategory_id">
      <input name="image">
    </template>
    <!-- name="business_name" name="description" name="cid" name="sid" name="subcategory_id" name="image" -->
  </form>
</div>

@push('scripts')
<script>
  (function () {
    const form = document.getElementById('biz-create-form');
    const btn  = document.getElementById('submit-btn');
    const spin = btn?.querySelector('.spinner-border');

    // Evita duplo envio + mostra spinner
    if (form && btn) {
      form.addEventListener('submit', function () {
        btn.setAttribute('disabled', 'disabled');
        if (spin) spin.classList.remove('d-none');
      }, { once: true });
    }

    // Preview da imagem
    const input = document.getElementById('image');
    const wrap  = document.getElementById('image-preview');
    const img   = document.getElementById('image-preview-img');

    if (input && wrap && img) {
      input.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (!file) {
          wrap.classList.add('d-none');
          img.removeAttribute('src');
          return;
        }
        const okType = /image\/(jpeg|png|webp)/i.test(file.type);
        if (!okType) {
          wrap.classList.add('d-none');
          img.removeAttribute('src');
          return;
        }
        const url = URL.createObjectURL(file);
        img.src = url;
        wrap.classList.remove('d-none');
      });
    }

    // ===== Subcategorias (AJAX) =====
    const $cat = document.getElementById('cid');
    const $sub = document.getElementById('subcategory_id');
    const selectedSub = '{{ old('subcategory_id', '') }}';

    async function loadSubcategories(catId) {
      if (!$sub) return;
      // limpa e coloca opção vazia
      $sub.innerHTML = '';
      const optEmpty = document.createElement('option');
      optEmpty.value = '';
      optEmpty.textContent = '— (opcional) —';
      $sub.appendChild(optEmpty);

      if (!catId) return;

      try {
        const res = await fetch(`/categories/${encodeURIComponent(catId)}/subcategories`, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return; // rota não disponível ou erro
        const list = await res.json();
        if (!Array.isArray(list)) return;

        list.forEach(s => {
          const opt = document.createElement('option');
          opt.value = s.id;
          opt.textContent = s.name ?? s.slug ?? `#${s.id}`;
          if (String(selectedSub) === String(s.id)) opt.selected = true;
          $sub.appendChild(opt);
        });
      } catch (e) {
        // silencia para não travar o formulário
        console.error('Falha ao carregar subcategorias:', e);
      }
    }

    if ($cat) {
      $cat.addEventListener('change', function () {
        loadSubcategories(this.value);
      });

      // dispara carregamento inicial (caso tenha old('cid'))
      if ($cat.value) {
        loadSubcategories($cat.value);
      }
    }
  })();
</script>
@endpush
@endsection
