@extends('layouts.app', ['title' => 'Páginas'])

@section('content')
<div class="container py-4" style="max-width: 1100px;">

  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Páginas</h1>

    <form class="d-flex gap-2" method="GET" action="{{ route('admin.pages.index') }}" role="search" aria-label="Buscar páginas">
      <input
        type="search"
        name="q"
        value="{{ request('q') }}"
        class="form-control form-control-sm"
        placeholder="Buscar por título ou slug…">
      <select name="sort" class="form-select form-select-sm">
        @php $sort = request('sort','-id'); @endphp
        <option value="-id" {{ $sort==='-id'?'selected':'' }}>Mais recentes</option>
        <option value="id"  {{ $sort==='id'?'selected':''  }}>Mais antigas</option>
        <option value="title" {{ $sort==='title'?'selected':'' }}>Título (A–Z)</option>
        <option value="-title" {{ $sort==='-title'?'selected':'' }}>Título (Z–A)</option>
        <option value="slug" {{ $sort==='slug'?'selected':'' }}>Slug (A–Z)</option>
        <option value="-slug" {{ $sort==='-slug'?'selected':'' }}>Slug (Z–A)</option>
      </select>
      <button class="btn btn-outline-secondary btn-sm" type="submit">Filtrar</button>
      <a class="btn btn-light btn-sm" href="{{ route('admin.pages.index') }}">Limpar</a>
      <a class="btn btn-primary btn-sm" href="{{ route('admin.pages.create') }}">Nova página</a>
    </form>
  </div>

  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  <div class="card">
    <div class="table-responsive">
      <table class="table table-sm align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="text-nowrap" style="width:80px;">ID</th>
            <th>Slug</th>
            <th>Título</th>
            <th class="text-nowrap" style="width:120px;">Status</th>
            <th class="text-nowrap" style="width:160px;">Publicada em</th>
            <th class="text-end" style="width:220px;">Ações</th>
          </tr>
        </thead>
        <tbody>
        @forelse ($pages as $p)
          <tr>
            <td>{{ $p->id }}</td>

            <td class="text-break">
              <div class="small fw-medium">{{ $p->slug }}</div>
              <div class="text-muted small">
                <a href="{{ route('pages.show', $p->slug) }}" target="_blank" rel="noopener">
                  {{ route('pages.show', $p->slug) }}
                </a>
              </div>
            </td>

            <td>{{ $p->title ?: '—' }}</td>

            <td>
              @if($p->is_active)
                <span class="badge bg-success-subtle text-success border border-success-subtle">Ativa</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Rascunho</span>
              @endif
            </td>

            <td>
              {{ optional($p->published_at)->format('d/m/Y H:i') ?: '—' }}
            </td>

            <td class="text-end">
              <div class="btn-group btn-group-sm" role="group" aria-label="Ações">
                <a class="btn btn-outline-secondary" href="{{ route('admin.pages.edit', $p) }}">Editar</a>
                <a class="btn btn-outline-primary" href="{{ route('pages.show', $p->slug) }}" target="_blank" rel="noopener">Ver</a>
                <form method="POST" action="{{ route('admin.pages.destroy', $p) }}" onsubmit="return confirm('Remover esta página?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger" type="submit">Excluir</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Nenhuma página encontrada.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="card-footer d-flex justify-content-between align-items-center">
      <div class="text-muted small">
        Exibindo {{ $pages->count() }} de {{ $pages->total() }} registros
      </div>
      {{ $pages->appends(request()->only('q','sort'))->links() }}
    </div>
  </div>
</div>
@endsection
