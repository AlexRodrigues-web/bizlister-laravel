@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 space-y-8">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-slate-900">Moderação de Avaliações</h1>
    @if(session('status'))
      <div class="rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700 border border-green-200">
        {{ session('status') }}
      </div>
    @endif
  </div>

  {{-- ===================== PENDENTES ===================== --}}
  <section>
    <h2 class="mb-3 text-lg font-semibold text-slate-800">Pendentes</h2>

    @forelse($pending as $r)
      @php
        // ID da review (compatível com legado)
        $rid = $r->id
            ?? $r->review_id
            ?? $r->rew_id
            ?? $r->r_id
            ?? $r->rid
            ?? $r->rev_id
            ?? null;

        // Biz id (compatível com legado)
        $bid = $r->biz_id ?? $r->b_id ?? null;

        // Nome do negócio (relacionamento OU colunas juntadas)
        $bizName = $r->business_name
            ?? (optional($r->business)->business_name ?? 'N/A');

        // Nome do usuário (relacionamento OU string/coluna) — cai em "Visitante"
        $userName = optional($r->user)->name
            ?? ($r->user_name ?? 'Visitante');
      @endphp

      <article class="mb-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div class="min-w-0">
            <div class="font-medium text-slate-800 truncate">
              Biz #{{ $bid }} &mdash; {{ $bizName }}
            </div>
            <div class="text-xs text-slate-500">
              Por: {{ $userName }} &mdash; <x-stars :value="$r->rating" size="sm" />
            </div>
          </div>

          <form method="POST" action="{{ route('admin.reviews.approve', ['review' => $rid]) }}">
            @csrf
            <button class="rounded-lg bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700">Aprovar</button>
          </form>
        </div>

        @if(!empty($r->title))
          <div class="mt-2 font-semibold text-slate-800">{{ $r->title }}</div>
        @endif
        @if(!empty($r->body))
          <p class="mt-1 text-slate-700">{{ $r->body }}</p>
        @endif
      </article>
    @empty
      <div class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">Sem pendências.</div>
    @endforelse

    <div class="mt-3">{{ $pending->links() }}</div>
  </section>

  {{-- ===================== APROVADAS ===================== --}}
  <section class="pt-4 border-t border-slate-200">
    <h2 class="mb-3 text-lg font-semibold text-slate-800">Aprovadas</h2>

    @foreach($approved as $r)
      @php
        $rid = $r->id
            ?? $r->review_id
            ?? $r->rew_id
            ?? $r->r_id
            ?? $r->rid
            ?? $r->rev_id
            ?? null;

        $bid = $r->biz_id ?? $r->b_id ?? null;

        $bizName = $r->business_name
            ?? (optional($r->business)->business_name ?? 'N/A');

        $userName = optional($r->user)->name
            ?? ($r->user_name ?? 'Visitante');
      @endphp

      <article class="mb-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div class="min-w-0">
            <div class="font-medium text-slate-800 truncate">
              Biz #{{ $bid }} &mdash; {{ $bizName }}
            </div>
            <div class="text-xs text-slate-500">
              Por: {{ $userName }} &mdash; <x-stars :value="$r->rating" size="sm" />
            </div>
          </div>

          <div class="flex gap-2">
            <form method="POST" action="{{ route('admin.reviews.hide', ['review' => $rid]) }}">
              @csrf
              <button class="rounded-lg bg-amber-600 px-4 py-2 text-white hover:bg-amber-700">Ocultar</button>
            </form>

            <form method="POST" action="{{ route('admin.reviews.destroy', ['review' => $rid]) }}"
                  onsubmit="return confirm('Excluir review?')">
              @csrf
              @method('DELETE')
              <button class="rounded-lg bg-red-700 px-4 py-2 text-white hover:bg-red-800">Excluir</button>
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
    @endforeach

    <div class="mt-3">{{ $approved->links() }}</div>
  </section>
</div>
@endsection
