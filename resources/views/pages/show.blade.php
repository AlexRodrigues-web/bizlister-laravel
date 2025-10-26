@extends('layouts.app')

@php
  use Illuminate\Support\Str;

  // Fallbacks seguros
  /** @var object|null $page */
  $title   = $page->title   ?? 'Página';
  $excerpt = $page->excerpt ?? null;
  $slug    = $page->slug    ?? null;
  $content = $page->content ?? '';

  // Regras de contato
  $hasContactShortcode = is_string($content) && Str::contains($content, ['[contact-form]', '[contato]']);
  $isContact = in_array(Str::slug($slug ?? ''), ['contato', 'contact']);
@endphp

@section('title', $title)

@section('content')
  <main class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-10 lg:py-14" role="main" aria-labelledby="page-title">
    {{-- Cabeçalho --}}
    <header class="mb-7">
      <h1 id="page-title"
          class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
        {{ $title }}
      </h1>

      @if (!empty($excerpt))
        <p class="mt-2 text-sm md:text-base text-slate-600 dark:text-slate-400">
          {{ $excerpt }}
        </p>
      @endif
    </header>

    {{-- Conteúdo / Contato --}}
    @if ($isContact || $hasContactShortcode)
      <section aria-label="Formulário de Contato"
               class="rounded-2xl border border-slate-200/80 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/70 backdrop-blur p-6 md:p-8 shadow-sm">
        @include('contact._form') {{-- precisa existir em resources/views/contact/_form.blade.php --}}
      </section>
    @else
      @if (trim(strip_tags($content)) === '')
        {{-- Estado vazio elegante quando ainda não há conteúdo --}}
        <div class="rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 p-8 text-center">
          <div class="text-slate-500 dark:text-slate-400">
            <p class="text-base md:text-lg">Conteúdo a ser definido.</p>
          </div>
        </div>
      @else
        {{-- Conteúdo vindo do BD (NÃO escapar) --}}
        <article
          class="prose prose-slate max-w-none
                 prose-headings:scroll-mt-24
                 prose-a:transition-colors
                 prose-a:text-indigo-600 hover:prose-a:text-indigo-700
                 dark:prose-invert dark:prose-a:text-indigo-400 dark:hover:prose-a:text-indigo-300">
          {!! $content !!}
        </article>
      @endif
    @endif
  </main>
@endsection
