<div class="card">
  <div class="card-body">
    <div class="mb-3">
      <label class="form-label">Título</label>
      <input type="text" name="title" class="form-control"
             value="{{ old('title',$page->title) }}" required maxlength="255">
    </div>

    <div class="mb-3">
      <label class="form-label">Slug (opcional, gerado pelo título)</label>
      <input type="text" name="slug" class="form-control"
             value="{{ old('slug',$page->slug) }}" maxlength="255">
      <div class="form-text">Usado na URL (ex.: <code>/sobre</code>).</div>
    </div>

    <div class="mb-3">
      <label class="form-label">Conteúdo</label>
      <textarea name="content" rows="10" class="form-control">{{ old('content',$page->content) }}</textarea>
    </div>

    <div class="row g-3">
      <div class="col-md-3 form-check mt-2">
        <input id="is_active" type="checkbox" class="form-check-input" name="is_active" value="1"
               {{ old('is_active',$page->is_active) ? 'checked' : '' }}>
        <label for="is_active" class="form-check-label">Ativa</label>
      </div>
      <div class="col-md-6">
        <label class="form-label">Publicada em (opcional)</label>
        <input type="datetime-local" name="published_at" class="form-control"
               value="{{ old('published_at', optional($page->published_at)->format('Y-m-d\TH:i')) }}">
      </div>
    </div>
  </div>

  <div class="card-footer d-flex gap-2">
    <button class="btn btn-primary" type="submit">Salvar</button>
    <a class="btn btn-outline-secondary" href="{{ route('admin.pages.index') }}">Voltar</a>
  </div>
</div>
