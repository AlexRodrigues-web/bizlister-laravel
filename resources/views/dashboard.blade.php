<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Atalhos</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ url('/cidades') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Ver Cidades</a>
                    <a href="{{ url('/') }}" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Home</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Resumo</h3>
                @php
                    $totalCidades = \App\Models\City::count();
                    $totalNegocios = \App\Models\Business::count();
                @endphp
                <ul class="list-disc ml-6">
                    <li>Total de cidades: <strong>{{ $totalCidades }}</strong></li>
                    <li>Total de negócios: <strong>{{ $totalNegocios }}</strong></li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
