@extends('layouts.app')

@section('title', 'Galeria - '.$business->business_name)

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Galeria — {{ $business->business_name }}</h1>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <form method="post" enctype="multipart/form-data" action="{{ route('business.gallery.store', $business->biz_id) }}" class="mb-4">
    @csrf
    <input type="file" name="images[]" multiple accept="image/*" class="form-control @error('images') is-invalid @enderror">
    @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <button class="btn btn-primary mt-2" type="submit">Enviar</button>
  </form>

  <div class="row g-3">
    @forelse ($images as $img)
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card">
          <img class="card-img-top" src="{{ Storage::disk('public')->exists($img->path ?? '') ? asset('storage/'.$img->path) : asset('images/placeholder-800x600.png') }}" alt="">
          <div class="card-body p-2 d-flex gap-2">
            <form method="post" action="{{ route('business.gallery.destroy', [$business->biz_id, $img->id]) }}">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" type="submit">Excluir</button>
            </form>
            @if (!$img->is_primary)
              <form method="post" action="{{ route('business.gallery.primary', [$business->biz_id, $img->id]) }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary" type="submit">Principal</button>
              </form>
            @else
              <span class="badge text-bg-success">Principal</span>
            @endif
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-light border">Sem imagens ainda.</div>
      </div>
    @endforelse
  </div>
</div>
@endsection
