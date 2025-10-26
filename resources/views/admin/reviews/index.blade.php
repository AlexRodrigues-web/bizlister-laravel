@extends('layouts.admin')

@section('content')
@include('admin._back_public')

<div class="mx-auto max-w-6xl px-4 py-8 space-y-8">
  {{-- Cabeçalho / breadcrumbs + flash --}}
  <div class="flex flex-wrap items-center justify-between gap-3">
    <nav class="text-sm text-slate-500">
      <a href="{{ route('admin.dashboard') }}" class="hover:underline">Painel</a>
      <span class="mx-2 opacity-60">/</span>
      <span class="text-slate-700 font-medium">Moderação de Avaliações</span>
    </nav>

    @if(session('status'))
      <div class="rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700 border border-green-200">
        {{ session('status') }}
      </div>
    @endif
  </div>

  {{-- ===================== PENDENTES ===================== --}}
  <section aria-labelledby="pending-title" class="rounded-2xl border border-slate-200 bg-white/80 shadow-sm">
    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-3">
      <h2 id="pending-title" class="text-sm font-semibold text-slate-800">Pendentes</h2>
      @if(method_exists($pending, 'total'))
        <span class="text-xs rounded-full bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5">
          {{ number_format($pending->total()) }}
        </span>
      @endif
    </div>

    <div class="p-5">
      @forelse($pending as $r)
        @php
          $rid = $r->id ?? $r->review_id ?? $r->rew_id ?? $r->r_id ?? $r->rid ?? $r->rev_id ?? null;
          $bid = $r->biz_id ?? $r->b_id ?? null;
          $bizName = $r->business_name ?? (optional($r->business)->business_name ?? 'N/A');
          $userName = optional($r->user)->name ?? ($r->user_name ?? 'Visitante');
          $created = optional($r->created_at)->format('d/m/Y H:i');
        @endphp

        <article class="mb-3 last:mb-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="font-medium text-slate-800 truncate">
                Biz #{{ $bid }} — {{ $bizName }}
              </div>
              <div class="text-xs text-slate-500 flex items-center gap-2">
                <span>Por: {{ $userName }}</span>
                <span class="opacity-40">•</span>
                <x-stars :value="$r->rating" size="sm" />
                @if($created)
                  <span class="opacity-40">•</span>
                  <time datetime="{{ optional($r->created_at)->toIso8601String() }}">{{ $created }}</time>
                @endif
              </div>
            </div>

            <form method="POST" action="{{ $rid ? route('admin.reviews.approve', ['review' => $rid]) : '#' }}">
              @csrf
              <button
                class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed"
                {{ $rid ? '' : 'disabled' }}>
                Aprovar
              </button>
            </form>
          </div>

          @if(!empty($r->title))
            <div class="mt-2 font-semibold text-slate-800">{{ $r->title }}</div>
          @endif
          @if(!empty($r->body))
            <p class="mt-1 text-slate-700">{{ $r->body }}</p>
          @endif>
        </article>
      @empty
        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
          Sem pendências.
        </div>
      @endforelse

      @if(method_exists($pending, 'links'))
        <div class="mt-4">{{ $pending->links() }}</div>
      @endif
    </div>
  </section>

  {{-- ===================== APROVADAS ===================== --}}
  <section aria-labelledby="approved-title" class="rounded-2xl border border-slate-200 bg-white/80 shadow-sm">
    <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-5 py-3">
      <h2 id="approved-title" class="text-sm font-semibold text-slate-800">Aprovadas</h2>
      @if(method_exists($approved, 'total'))
        <span class="text-xs rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5">
          {{ number_format($approved->total()) }}
        </span>
      @endif
    </div>

    <div class="p-5">
      @forelse($approved as $r)
        @php
          $rid = $r->id ?? $r->review_id ?? $r->rew_id ?? $r->r_id ?? $r->rid ?? $r->rev_id ?? null;
          $bid = $r->biz_id ?? $r->b_id ?? null;
          $bizName = $r->business_name ?? (optional($r->business)->business_name ?? 'N/A');
          $userName = optional($r->user)->name ?? ($r->user_name ?? 'Visitante');
          $created = optional($r->created_at)->format('d/m/Y H:i');
        @endphp

        <article class="mb-3 last:mb-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="font-medium text-slate-800 truncate">
                Biz #{{ $bid }} — {{ $bizName }}
              </div>
              <div class="text-xs text-slate-500 flex items-center gap-2">
                <span>Por: {{ $userName }}</span>
                <span class="opacity-40">•</span>
                <x-stars :value="$r->rating" size="sm" />
                @if($created)
                  <span class="opacity-40">•</span>
                  <time datetime="{{ optional($r->created_at)->toIso8601String() }}">{{ $created }}</time>
                @endif
              </div>
            </div>

            <div class="flex gap-2">
              <form method="POST" action="{{ $rid ? route('admin.reviews.hide', ['review' => $rid]) : '#' }}">
                @csrf
                <button
                  class="rounded-lg bg-amber-600 px-4 py-2 text-white hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  {{ $rid ? '' : 'disabled' }}>
                  Ocultar
                </button>
              </form>

              <form method="POST" action="{{ $rid ? route('admin.reviews.destroy', ['review' => $rid]) : '#' }}"
                    onsubmit="return {{ $rid ? 'confirm(\'Excluir review?\')' : 'false' }};">
                @csrf
                @method('DELETE')
                <button
                  class="rounded-lg bg-red-700 px-4 py-2 text-white hover:bg-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
                  {{ $rid ? '' : 'disabled' }}>
                  Excluir
                </button>
              </form>
            </div>
          </div>

          @if(!empty($r->title))
            <div class="mt-2 font-semibold text-slate-800">{{ $r->title }}</div>
          @endif
          @if(!empty($r->body))
            <p class="mt-1 text-slate-700">{{ $r->body }}</p>
          @endif
        </article>
      @empty
        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
          Nenhuma avaliação aprovada no momento.
        </div>
      @endforelse

      @if(method_exists($approved, 'links'))
        <div class="mt-4">{{ $approved->links() }}</div>
      @endif
    </div>
  </section>
</div>
@endsection
