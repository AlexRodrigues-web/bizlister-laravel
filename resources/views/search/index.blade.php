@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">{{ __('Buscar') }}</h1>

  <form method="GET" action="{{ route('search.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="md:col-span-2">
      <label class="block text-sm mb-1" for="q">{{ __('Termo') }}</label>
      <input
        id="q"
        type="text"
        name="q"
        value="{{ $q }}"
        class="border rounded px-3 py-2 w-full"
        placeholder="Buscar por nome ou descrição..." />
    </div>

    <div>
      <label class="block text-sm mb-1" for="categoria">{{ __('Categoria') }}</label>
      <select id="categoria" name="categoria" class="border rounded px-3 py-2 w-full">
        <option value="">{{ __('Todas') }}</option>
        @foreach($categories as $c)
          <option value="{{ $c->cat_id }}" {{ request('categoria') == $c->cat_id ? 'selected' : '' }}>
            {{ $c->label }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm mb-1" for="cidade">{{ __('Cidade') }}</label>
      <select id="cidade" name="cidade" class="border rounded px-3 py-2 w-full">
        <option value="">{{ __('Todas') }}</option>
        @foreach($cities as $c)
          <option value="{{ $c->city_id }}" {{ request('cidade') == $c->city_id ? 'selected' : '' }}>
            {{ $c->city }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-4 flex gap-3">
      <button class="px-4 py-2 bg-blue-600 text-white rounded">{{ __('Filtrar') }}</button>
      <a href="{{ route('search.index') }}" class="px-4 py-2 border rounded">{{ __('Limpar') }}</a>
    </div>
  </form>

  @if($results->count())
    <p class="text-sm text-gray-600 mb-3">
      {{ __('Resultados') }}:
      <strong>
        @if($results instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
          {{ $results->total() }}
        @else
          {{ $results->count() }}
        @endif
      </strong>
      @php
        $hasTerm = filled($q);
        $hasCat  = filled($cat ?? null);
        $hasSid  = filled($sid ?? null);
      @endphp
      @if($hasTerm) • {{ __('termo') }}: “{{ $q }}” @endif
      @if($hasCat)  • {{ __('categoria') }}: #{{ $cat }} @endif
      @if($hasSid)  • {{ __('cidade') }}: #{{ $sid }} @endif
    </p>

    <div class="space-y-4">
      @foreach($results as $r)
        <div class="border rounded p-4">
          <h2 class="text-lg font-medium">{{ $r->business_name }}</h2>
          <p class="text-sm text-gray-600">{{ $r->city }}</p>
          @if(!empty($r->description))
            <p class="mt-2">{!! nl2br(e($r->description)) !!}</p>
          @endif
          <a href="{{ route('business.show', [$r->biz_id, \Illuminate\Support\Str::slug($r->business_name)]) }}"
             class="inline-block mt-2 text-blue-600 hover:underline">
            {{ __('Ver detalhes') }}
          </a>
        </div>
      @endforeach
    </div>

    @if(method_exists($results, 'links'))
      <div class="mt-4">
        {{ $results->withQueryString()->links() }}
      </div>
    @endif
  @else
    <p class="text-gray-600">{{ __('Nenhum resultado encontrado.') }}</p>
  @endif
</div>
@endsection
