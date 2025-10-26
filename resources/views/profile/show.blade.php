{{-- resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Meu perfil')

@section('content')
@php
  use Illuminate\Support\Str;

  /** @var \App\Models\User $user */
  $user = $user ?? auth()->user();

  $initials = collect(explode(' ', trim((string)($user->name ?? ''))))
    ->filter()->take(2)->map(fn($p)=>Str::upper(Str::substr($p,0,1)))->implode('');

  $joined   = optional($user->created_at)->format('d/m/Y H:i');
  $isAdmin  = (bool)($user->is_admin ?? false);
@endphp

<div class="container py-4">

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="{{ url('/') }}">Início</a></li>
      <li class="breadcrumb-item active" aria-current="page">Meu perfil</li>
    </ol>
  </nav>

  {{-- Cabeçalho --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap align-items-center gap-3">
      <div class="rounded-3 bg-primary text-white fw-bold d-grid place-items-center"
           style="width:64px;height:64px;font-size:1.25rem;">
        {{ $initials ?: 'U' }}
      </div>

      <div class="me-auto">
        <h1 class="h3 mb-1">Meu perfil</h1>
        <div class="small text-muted">
          Bem-vindo, {{ $user->name }}.
          @if($joined) Conta criada em <strong class="text-body">{{ $joined }}</strong>. @endif
        </div>
        <div class="mt-2">
          <span class="badge bg-light text-body border me-1">{{ $isAdmin ? 'Admin' : 'Usuário' }}</span>
          @if($user->email_verified_at)
            <span class="badge bg-success-subtle text-success border border-success-subtle">✔ E-mail verificado</span>
          @else
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">⚠ Verifique seu e-mail</span>
          @endif
        </div>
      </div>

      <div class="d-flex flex-wrap gap-2">
        @if (Route::has('profile.edit'))
          <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
            ✏️ Editar perfil
          </a>
        @endif
        @can('admin')
          <a href="{{ route('admin.dashboard') }}" class="btn btn-dark">Painel Admin</a>
        @endcan
        <form method="POST" action="{{ route('logout') }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-light border">Sair</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Alertas globais --}}
  @includeWhen(View::exists('partials.alerts'), 'partials.alerts')

  {{-- Alertas locais --}}
  @foreach (['status','success','error'] as $flash)
    @if (session($flash))
      <div class="alert {{ $flash==='error' ? 'alert-danger' : 'alert-success' }} alert-dismissible fade show" role="alert">
        {{ session($flash) }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
      </div>
    @endif
  @endforeach

  <div class="row g-3">
    {{-- Dados da conta --}}
    <div class="col-12 col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h2 class="h6 mb-0">Informações da conta</h2>
            @if(!empty($user->email))
              <button type="button" class="btn btn-sm btn-outline-secondary" data-copy="{{ $user->email }}">
                Copiar e-mail
              </button>
            @endif
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="small text-muted">Nome</div>
              <div class="fw-semibold">{{ $user->name ?? '—' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="small text-muted">E-mail</div>
              <div class="fw-semibold">{{ $user->email ?? '—' }}</div>
            </div>
            <div class="col-12 col-md-6">
              <div class="small text-muted">Cadastro</div>
              <div>{{ $joined ?? '—' }}</div>
            </div>
            @if($user->email_verified_at)
              <div class="col-12 col-md-6">
                <div class="small text-muted">Verificado em</div>
                <div>{{ optional($user->email_verified_at)->format('d/m/Y H:i') }}</div>
              </div>
            @endif
            @if($isAdmin)
              <div class="col-12">
                <div class="small text-muted">Permissões</div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Admin</span>
              </div>
            @endif
          </div>

          <div class="mt-3 d-flex flex-wrap gap-2">
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Atualizar dados</a>
            @if(Route::has('bookmarks.index'))
              <a href="{{ route('bookmarks.index') }}" class="btn btn-outline-secondary">⭐ Meus favoritos</a>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Ações rápidas --}}
    <div class="col-12 col-lg-4">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h2 class="h6">Ações rápidas</h2>
          <div class="list-group list-group-flush mt-2">
            <a href="{{ route('business.create') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span>Cadastrar novo negócio</span> <span class="text-muted">→</span>
            </a>
            <a href="{{ route('search.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span>Buscar negócios</span> <span class="text-muted">→</span>
            </a>
            <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span>Ver categorias</span> <span class="text-muted">→</span>
            </a>
            <a href="{{ route('cities.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <span>Ver cidades</span> <span class="text-muted">→</span>
            </a>
          </div>

          <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-outline-danger w-100">Sair da conta</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  {{-- Voltar --}}
  <div class="mt-4">
    <a href="{{ url()->previous() }}" class="btn btn-light border">← Voltar</a>
  </div>
</div>

@push('scripts')
<script>
  // Copiar e-mail com Bootstrap-friendly feedback
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;
    const value = btn.getAttribute('data-copy');
    if (!value) return;

    navigator.clipboard.writeText(value).then(() => {
      const prev = btn.innerHTML;
      btn.innerHTML = 'Copiado!';
      btn.classList.remove('btn-outline-secondary');
      btn.classList.add('btn-success');
      setTimeout(() => {
        btn.innerHTML = prev;
        btn.classList.add('btn-outline-secondary');
        btn.classList.remove('btn-success');
      }, 1200);
    }).catch(() => alert('Não foi possível copiar agora.'));
  });
</script>
@endpush
@endsection
