@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12">
  <h1 class="text-3xl font-bold text-slate-800 mb-3">Página não encontrada (404)</h1>
  <p class="text-slate-600 mb-6">
    O endereço pode ter mudado. Veja alguns negócios recentes ou volte para o início.
  </p>

  @if(!empty($latest) && count($latest))
    <h2 class="text-xl font-semibold text-slate-800 mb-3">Talvez você esteja procurando:</h2>
    <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 mb-8">
      @foreach($latest as $b)
        <li>
          <a class="block rounded-lg border border-slate-200 px-4 py-2 hover:bg-slate-50"
             href="{{ route('business.show', ['id' => $b->biz_id, 'slug' => \Illuminate\Support\Str::slug($b->business_name)]) }}">
            {{ $b->business_name }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif

  <div class="flex gap-3">
    <a href="{{ url('/') }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">
      Voltar ao início
    </a>
    <a href="{{ route('business.create') }}" class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-white hover:bg-indigo-700">
      Cadastrar um negócio
    </a>
  </div>
</div>
@endsection
