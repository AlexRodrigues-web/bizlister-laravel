@extends("layouts.app")

@section("content")
<div class="max-w-3xl mx-auto p-4">
    @if (session("status"))
        <div class="mb-4 p-3 border rounded bg-green-50">
            {{ session("status") }}
        </div>
    @endif

    <h1 class="text-2xl font-semibold mb-6">Cadastrar Negócio</h1>

    <form method="POST" action="{{ route('business.store') }}" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label class="block mb-1 font-medium">Nome do negócio</label>
            <input type="text" name="business_name" value="{{ old('business_name') }}" class="w-full border rounded p-2">
            @error("business_name") <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Descrição</label>
            <textarea name="description" rows="5" class="w-full border rounded p-2">{{ old('description') }}</textarea>
            @error("description") <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Categoria</label>
            <select name="category_id" class="w-full border rounded p-2">
                <option value="">Selecione...</option>
                @foreach($categories as $c)
                    <option value="{{ $c->cat_id }}" @selected(old('category_id') == $c->cat_id)>{{ $c->label }}</option>
                @endforeach
            </select>
            @error("category_id") <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Cidade</label>
            <select name="city_id" class="w-full border rounded p-2">
                <option value="">Selecione...</option>
                @foreach($cities as $ci)
                    <option value="{{ $ci->city_id }}" @selected(old('city_id') == $ci->city_id)>{{ $ci->city }}</option>
                @endforeach
            </select>
            @error("city_id") <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block mb-1 font-medium">Imagem (opcional)</label>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.gif,.webp" class="w-full">
            @error("image") <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="pt-2">
            <button type="submit" class="px-4 py-2 border rounded bg-black text-white">Salvar anúncio</button>
            <a href="{{ url()->previous() }}" class="ml-3 underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection
