@extends('layouts.app', ['title' => 'Nova Página'])

@section('content')
<div class="container py-4" style="max-width: 960px;">
  <h1 class="h4 mb-3">Nova Página</h1>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-semibold mb-1">Corrija os campos abaixo:</div>
      <ul class="mb-0 ps-3">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.pages.store') }}">
    @csrf
    @include('admin.pages.partials.form', ['page' => $page])
  </form>
</div>
@endsection
