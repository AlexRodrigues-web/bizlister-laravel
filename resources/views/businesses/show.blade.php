@extends("layouts.app")

@section("content")
<div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
    <div class="bg-white rounded shadow p-6">
        <h1 class="text-2xl font-bold mb-2">
            {{ $biz->business_name ?? ("Negócio #".$biz->biz_id) }}
        </h1>

        @if($category || $city)
            <div class="text-sm text-gray-600 mb-4">
                @if($category)
                    <span>Categoria: <strong>{{ $category->category ?? ("#".$category->cat_id) }}</strong></span>
                @endif
                @if($category && $city) <span class="mx-2">•</span> @endif
                @if($city)
                    <span>Cidade: <strong>{{ $city->city ?? ("#".$city->city_id) }}</strong></span>
                @endif
            </div>
        @endif

        @if(!empty($biz->description))
            <div class="prose max-w-none">
                {!! nl2br(e($biz->description)) !!}
            </div>
        @endif
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white rounded shadow p-6">
            <h2 class="font-semibold mb-3">Contato</h2>
            <ul class="text-sm space-y-1">
                @if(!empty($biz->phone))     <li><strong>Telefone:</strong> {{ $biz->phone }}</li>@endif
                @if(!empty($biz->email))     <li><strong>E-mail:</strong> {{ $biz->email }}</li>@endif
                @if(!empty($biz->website))   <li><strong>Site:</strong> <a class="text-blue-600 hover:underline" href="{{ $biz->website }}" target="_blank" rel="noopener">Abrir site</a></li>@endif
                @if(!empty($biz->address_1)) <li><strong>Endereço 1:</strong> {{ $biz->address_1 }}</li>@endif
                @if(!empty($biz->address_2)) <li><strong>Endereço 2:</strong> {{ $biz->address_2 }}</li>@endif
            </ul>
        </div>

        <div class="bg-white rounded shadow p-6">
            <h2 class="font-semibold mb-3">Tags / Meta</h2>
            <ul class="text-sm space-y-1">
                @if(!empty($biz->tags))      <li><strong>Tags:</strong> {{ $biz->tags }}</li>@endif
                @if(!empty($biz->date))      <li><strong>Data:</strong> {{ $biz->date }}</li>@endif
                <li><strong>Ativo:</strong> {{ (int)($biz->active ?? 0) === 1 ? "Sim" : "Não" }}</li>
                @if(!empty($biz->hits))      <li><strong>Visualizações:</strong> {{ $biz->hits }}</li>@endif
            </ul>
        </div>
    </div>

    @if(!empty($biz->latitude) && !empty($biz->longitude))
        <div class="bg-white rounded shadow p-6">
            <h2 class="font-semibold mb-3">Localização</h2>
            <p class="text-sm text-gray-600 mb-3">
                Lat: {{ $biz->latitude }} • Lng: {{ $biz->longitude }}
            </p>
            <a class="text-blue-600 hover:underline" target="_blank" rel="noopener"
               href="https://www.google.com/maps?q={{ urlencode($biz->latitude.','.$biz->longitude) }}">
                Ver no Google Maps
            </a>
        </div>
    @endif

    <div>
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">&larr; Voltar</a>
    </div>
</div>
@endsection
