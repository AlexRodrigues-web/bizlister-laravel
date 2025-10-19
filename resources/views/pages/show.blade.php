@extends('layouts.app')

@section('content')
  <div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">{{ $page->title }}</h1>
    <div class="prose">{!! $page->content !!}</div>
  </div>
@endsection
