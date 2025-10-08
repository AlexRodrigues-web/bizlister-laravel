@extends("layouts.app")

@section("content")
<div class="max-w-3xl mx-auto px-4 py-8">
  <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-slate-800 mb-4">
    {{ $biz->business_name }}
  </h1>

  @if(session("success"))
    <div class="mb-4 rounded-md bg-green-50 p-3 text-green-700">{{ session("success") }}</div>
  @endif

  @if($biz->image)
    <img src="{{ asset('storage/'.$biz->image) }}" alt="{{ $biz->business_name }}" class="mb-4 rounded-lg max-h-80 object-cover">
  @endif

<dl class="space-y-2">
  <div><dt class="font-semibold">Categoria:</dt><dd>{{ $biz->cid }}</dd></div>
  <div><dt class="font-semibold">Cidade:</dt><dd>{{ $biz->city }}</dd></div>
  <div><dt class="font-semibold">Descrição:</dt><dd>{!! nl2br(e($biz->description)) !!}</dd></div>
</dl>

  <div class="mt-6">
    <a href="{{ url()->previous() }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">Voltar</a>
  </div>
</div>
@endsection