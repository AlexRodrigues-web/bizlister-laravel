@extends('layouts.app')

@section('title', $page->title ?? 'PÃ¡gina')

@section('content')
  <div class="mx-auto max-w-3xl px-4 py-8">
    {{-- CabeÃ§alho --}}
    <header class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800">
        {{ $page->title ?? 'PÃ¡gina' }}
      </h1>
      @if (!empty($page->excerpt))
        <p class="mt-1 text-sm text-slate-600">{{ $page->excerpt }}</p>
      @endif
    </header>

    @php
      $slug = $page->slug ?? null;
      $content = $page->content ?? '';
      $hasContactShortcode = is_string($content) && Str::contains($content, ['[contact-form]', '[contato]']);
      $isContact = in_array(Str::slug($slug ?? ''), ['contato','contact']);
    @endphp

    {{-- Se for a pÃ¡gina de Contato, mostra o formulÃ¡rio estilizado --}}
    @if ($isContact || $hasContactShortcode)
      <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        @include('contact._form') {{-- crie resources/views/contact/_form.blade.php com o conteÃºdo do contact.blade estilizado --}}
      </div>
    @else
      {{-- ConteÃºdo padrÃ£o vindo do CMS --}}
      <article class="prose prose-slate max-w-none prose-a:text-indigo-600 hover:prose-a:text-indigo-700">
        {!! $content !!}
      </article>
    @endif
  </div>
@endsection
