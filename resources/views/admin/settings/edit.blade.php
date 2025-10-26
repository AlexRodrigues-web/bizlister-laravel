@extends('layouts.app', ['title' => 'Configurações'])

@section('content')
<div class="container py-4" style="max-width: 920px;">
  <h1 class="h3 fw-semibold mb-3">Configurações do Site</h1>

  {{-- Flash --}}
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  {{-- Erros gerais --}}
  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Corrija os campos abaixo:</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.settings.update') }}" novalidate>
    @csrf
    @method('PUT')

    {{-- GERAL --}}
    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Geral</div>
      <div class="card-body row g-3">
        <div class="col-md-8">
          <label for="site_title" class="form-label">Título do site</label>
          <input id="site_title" name="site_title"
                 value="{{ old('site_title',$setting->site_title) }}"
                 class="form-control @error('site_title') is-invalid @enderror">
          @error('site_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="template" class="form-label">Template</label>
          <input id="template" name="template"
                 value="{{ old('template',$setting->template) }}"
                 class="form-control @error('template') is-invalid @enderror">
          @error('template') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-8">
          <label for="site_link" class="form-label">URL do site</label>
          <input id="site_link" name="site_link" type="url"
                 value="{{ old('site_link',$setting->site_link) }}"
                 placeholder="https://exemplo.com"
                 class="form-control @error('site_link') is-invalid @enderror">
          @error('site_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="vertion" class="form-label">Versão</label>
          <input id="vertion" name="vertion"
                 value="{{ old('vertion',$setting->vertion) }}"
                 class="form-control @error('vertion') is-invalid @enderror">
          @error('vertion') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label for="home_text" class="form-label">Texto da home</label>
          <input id="home_text" name="home_text"
                 value="{{ old('home_text',$setting->home_text) }}"
                 class="form-control @error('home_text') is-invalid @enderror">
          @error('home_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- SEO --}}
    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">SEO</div>
      <div class="card-body row g-3">
        <div class="col-12">
          <label for="meta_keywords" class="form-label">Meta keywords</label>
          <input id="meta_keywords" name="meta_keywords"
                 value="{{ old('meta_keywords',$setting->meta_keywords) }}"
                 class="form-control @error('meta_keywords') is-invalid @enderror">
          @error('meta_keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-12">
          <label for="meta_description" class="form-label">Meta description</label>
          <input id="meta_description" name="meta_description"
                 value="{{ old('meta_description',$setting->meta_description) }}"
                 class="form-control @error('meta_description') is-invalid @enderror">
          @error('meta_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- CONTATO --}}
    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Contato</div>
      <div class="card-body row g-3">
        <div class="col-md-6">
          <label for="site_email" class="form-label">E-mail</label>
          <input id="site_email" name="site_email" type="email"
                 value="{{ old('site_email',$setting->site_email) }}"
                 class="form-control @error('site_email') is-invalid @enderror">
          @error('site_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label for="county" class="form-label">County</label>
          <input id="county" name="county"
                 value="{{ old('county',$setting->county) }}"
                 class="form-control @error('county') is-invalid @enderror">
          @error('county') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <label for="zip" class="form-label">ZIP</label>
          <input id="zip" name="zip"
                 value="{{ old('zip',$setting->zip) }}"
                 class="form-control @error('zip') is-invalid @enderror">
          @error('zip') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- SOCIAL --}}
    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Social</div>
      <div class="card-body row g-3">
        <div class="col-md-4">
          <label for="fb_app_id" class="form-label">Facebook App ID</label>
          <input id="fb_app_id" name="fb_app_id"
                 value="{{ old('fb_app_id',$setting->fb_app_id) }}"
                 class="form-control @error('fb_app_id') is-invalid @enderror">
          @error('fb_app_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="fb_secret_key" class="form-label">Facebook Secret</label>
          <input id="fb_secret_key" name="fb_secret_key"
                 value="{{ old('fb_secret_key',$setting->fb_secret_key) }}"
                 class="form-control @error('fb_secret_key') is-invalid @enderror">
          @error('fb_secret_key') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="fb_page" class="form-label">Facebook Page</label>
          <input id="fb_page" name="fb_page"
                 value="{{ old('fb_page',$setting->fb_page) }}"
                 class="form-control @error('fb_page') is-invalid @enderror">
          @error('fb_page') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="twitter_link" class="form-label">Twitter</label>
          <input id="twitter_link" name="twitter_link" type="url"
                 value="{{ old('twitter_link',$setting->twitter_link) }}"
                 class="form-control @error('twitter_link') is-invalid @enderror">
          @error('twitter_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="pinterest_link" class="form-label">Pinterest</label>
          <input id="pinterest_link" name="pinterest_link" type="url"
                 value="{{ old('pinterest_link',$setting->pinterest_link) }}"
                 class="form-control @error('pinterest_link') is-invalid @enderror">
          @error('pinterest_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="google_pluse_link" class="form-label">Google+ (legado)</label>
          <input id="google_pluse_link" name="google_pluse_link" type="url"
                 value="{{ old('google_pluse_link',$setting->google_pluse_link) }}"
                 class="form-control @error('google_pluse_link') is-invalid @enderror">
          @error('google_pluse_link') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- STATUS --}}
    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Status</div>
      <div class="card-body row g-3 align-items-center">
        <div class="col-md-3">
          {{-- Hidden para enviar 0 quando desmarcado --}}
          <input type="hidden" name="active" value="0">
          <div class="form-check">
            <input id="active" type="checkbox" class="form-check-input" name="active" value="1"
                   {{ old('active',$setting->active) ? 'checked' : '' }}>
            <label for="active" class="form-check-label">Site ativo</label>
          </div>
          @error('active') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-3">
          <input type="hidden" name="rev_active" value="0">
          <div class="form-check">
            <input id="rev_active" type="checkbox" class="form-check-input" name="rev_active" value="1"
                   {{ old('rev_active',$setting->rev_active) ? 'checked' : '' }}>
            <label for="rev_active" class="form-check-label">Reviews ativas</label>
          </div>
          @error('rev_active') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-6">
          <label for="site_views" class="form-label">Views (somente leitura opcional)</label>
          <input id="site_views" name="site_views" type="number" min="0" step="1"
                 value="{{ old('site_views',$setting->site_views) }}"
                 class="form-control @error('site_views') is-invalid @enderror">
          @error('site_views') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Salvar</button>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
@endsection
