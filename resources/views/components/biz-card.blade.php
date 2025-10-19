@props([
  'biz'         => null,   // objeto Business OU null
  'id'          => null,   // opcional: força o id
  'title'       => null,   // opcional: força o título
  'subtitle'    => null,   // opcional: força subtítulo (ex.: endereço/categoria)
  'href'        => null,   // opcional: força o link
  'meta'        => null,   // opcional: força meta (ex.: cidade/estado)
  'image'       => null,   // opcional: URL da imagem
  'description' => null,   // opcional: força a descrição
])

@php
  use Illuminate\Support\Str;

  // Garante variável local, mesmo se nada vier
  $biz = $biz ?? null;

  // ID (tenta em ordem: prop -> biz_id -> id)
  $bid = $id
      ?? ($biz->biz_id ?? null)
      ?? ($biz->id ?? null);

  // Nome/Título
  $name = $title
      ?? ($biz->business_name ?? null)
      ?? ($biz->title ?? null)
      ?? ($biz->name ?? null)
      ?? '';

  // Slug (pode ficar vazio; só será usado se houver ID)
  $slug = Str::slug($name);

  // Link (prop tem prioridade; senão, monta via rota se tiver ID)
  $link = $href ?? ($bid ? route('business.show', [$bid, $slug]) : null);

  // Subtítulo e meta com fallbacks comuns do seu schema
  $sub  = $subtitle
      ?? ($biz->address ?? null)
      ?? ($biz->category_name ?? null);

  $met  = $meta
      ?? ($biz->city ?? null)
      ?? ($biz->city_name ?? null)
      ?? ($biz->state_name ?? null);

  // Descrição: prop > do objeto > vazio
  $desc = $description
      ?? ($biz->description ?? '');

  // Título de fallback se não houver nome
  $displayName = $name !== '' ? $name : ($bid ? ('Negócio #'.$bid) : 'Negócio');
@endphp

<article class="rounded-2xl border border-slate-200 shadow-sm bg-white overflow-hidden flex flex-col h-full">
  <div class="p-4 flex-1">
    <h3 class="text-base font-semibold text-slate-800 leading-snug">
      @if ($link)
        <a class="hover:underline" href="{{ $link }}">
          {{ $displayName }}
        </a>
      @else
        {{ $displayName }}
      @endif
    </h3>

    @if(!empty($met))
      <p class="text-xs text-slate-500 mt-1">{{ $met }}</p>
    @endif

    @if(!empty($desc))
      <p class="text-sm text-slate-600 mt-3">
        {{ Str::limit(strip_tags($desc), 140) }}
      </p>
    @endif
  </div>

  @if ($link)
    <div class="px-4 py-3 border-t border-slate-100 bg-slate-50">
      <a class="inline-flex items-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-700"
         href="{{ $link }}">
        Ver detalhes
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
  @endif
</article>
