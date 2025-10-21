@extends('layouts.app')

@section('title', $page->title)

@section('content')
  <div class="container mx-auto max-w-3xl p-6">
    <h1 class="text-3xl font-bold mb-4">{{ $page->title }}</h1>
    <article class="prose max-w-none">
      {!! $page->content !!}
    </article>
  </div>
@endsection