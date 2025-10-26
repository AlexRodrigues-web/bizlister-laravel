{{-- resources/views/admin/advertisements/edit.blade.php --}}
@extends('layouts.app', ['title' => 'Anúncios'])

@section('content')
<div class="container py-4" style="max-width: 920px;">
  <h1 class="h3 fw-semibold mb-3">Gerenciar Anúncios</h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Corrija os campos abaixo:</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.advertisements.update') }}">
    @csrf
    @method('PUT')

    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Slot 1</div>
      <div class="card-body">
        <label class="form-label">Código (HTML/JS permitido)</label>
        <textarea
          name="ad1"
          class="form-control"
          rows="5"
          placeholder="Cole aqui o snippet do anúncio (ex.: script do provedor)">{{ old('ad1', $ad->ad1 ?? '') }}</textarea>
        <div class="form-text">Exibido onde o slot 1 for incluído no site.</div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Slot 2</div>
      <div class="card-body">
        <label class="form-label">Código (HTML/JS permitido)</label>
        <textarea
          name="ad2"
          class="form-control"
          rows="5"
          placeholder="Opcional">{{ old('ad2', $ad->ad2 ?? '') }}</textarea>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header bg-white fw-semibold">Slot 3</div>
      <div class="card-body">
        <label class="form-label">Código (HTML/JS permitido)</label>
        <textarea
          name="ad3"
          class="form-control"
          rows="5"
          placeholder="Opcional">{{ old('ad3', $ad->ad3 ?? '') }}</textarea>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">Salvar</button>
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
  </form>
</div>
@endsection
