@extends("layouts.app")

@section("content")
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-6">Contato</h1>

    @if (session("status"))
        <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-2 rounded mb-4">
            {{ session("status") }}
        </div>
    @endif

    <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium">Nome</label>
            <input type="text" name="nome" value="{{ old('nome') }}" class="w-full border rounded px-3 py-2">
            @error('nome')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium">E-mail</label>
            <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2">
            @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Assunto</label>
            <input type="text" name="assunto" value="{{ old('assunto') }}" class="w-full border rounded px-3 py-2">
            @error('assunto')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium">Mensagem</label>
            <textarea name="mensagem" rows="6" class="w-full border rounded px-3 py-2">{{ old('mensagem') }}</textarea>
            @error('mensagem')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Enviar</button>
    </form>
</div>
@endsection
