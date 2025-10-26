@if (!request()->routeIs('admin.dashboard'))
  <div class="mb-4 flex items-center justify-between">
    <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
      <span aria-hidden="true">←</span>
      <span>Dashboard público</span>
    </a>
  </div>
@endif
