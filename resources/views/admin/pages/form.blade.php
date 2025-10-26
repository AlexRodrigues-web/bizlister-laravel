@extends('layouts.app')

@section('title', ($page->exists?'Editar':'Nova').' página')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">{{ $page->exists?'Editar':'Nova' }} página</h1>

  <form method="POST" action="{{ $page->exists ? route('admin.pages.update',$page) : route('admin.pages.store') }}">
    @csrf
    @if($page->exists) @method('PUT') @endif

    <div class="card p-3">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Slug</label>
          <input type="text" name="slug" class="form-control" value="{{ old('slug',$page->slug) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Título</label>
          <input type="text" name="title" class="form-control" value="{{ old('title',$page->title) }}">
        </div>
        <div class="col-12">
          <label class="form-label">Conteúdo</label>
          <textarea name="content" rows="10" class="form-control">{{ old('content',$page->content) }}</textarea>
        </div>
        <div class="col-md-3 form-check ms-2">
          <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active',$page->is_active))>
          <label class="form-check-label">Ativa</label>
        </div>
        <div class="col-md-4">
          <label class="form-label">Publicado em</label>
          <input type="datetime-local" name="published_at" class="form-control"
                 value="{{ old('published_at', optional($page->published_at)->format('Y-m-d\TH:i')) }}">
        </div>
      </div>

      <div class="mt-3">
        <button class="btn btn-primary">Salvar</button>
        <a href="{{ route('admin.pages.index') }}" class="btn btn-outline-secondary">Cancelar</a>
      </div>
    </div>
  </form>
</div>
@endsection
