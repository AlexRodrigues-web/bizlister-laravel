@extends('layouts.app', ['title' => 'Editar Página'])

@section('content')
<div class="container py-4" style="max-width: 960px;">
  <h1 class="h4 mb-3">Editar Página</h1>

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

  <form method="POST" action="{{ route('admin.pages.update', $page) }}">
    @csrf @method('PUT')
    @include('admin.pages.partials.form', ['page' => $page])
  </form>
</div>
@endsection
