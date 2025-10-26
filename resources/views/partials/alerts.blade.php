@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('info'))
  <div class="alert alert-info">{{ session('info') }}</div>
@endif
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif