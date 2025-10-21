@if (!request()->is('dashboard') && !request()->is('dashboard/*'))
  <div class="mb-4 flex items-center justify-between">
    <a href="{{ url('/dashboard') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:bg-slate-50">
      ← Dashboard público
    </a>
  </div>
@endif