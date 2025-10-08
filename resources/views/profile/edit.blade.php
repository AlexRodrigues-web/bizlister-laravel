@extends("layouts.app")

@section("content")
<div class="max-w-2xl mx-auto py-8 px-4">
  <h1 class="text-2xl font-semibold mb-6">{{ __("Meu perfil") }}</h1>

  @if(session("status"))
    <div class="mb-4 rounded border border-green-300 bg-green-50 text-green-800 px-3 py-2">
      {{ session("status") }}
    </div>
  @endif

  <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
    @csrf
    @method("PATCH")

    <div>
      <label class="block text-sm font-medium text-gray-700" for="name">{{ __("Nome") }}</label>
      <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
      @error("name")<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700" for="email">{{ __("E-mail") }}</label>
      <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
             class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
      @error("email")<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <fieldset class="border rounded-md p-4">
      <legend class="px-2 text-sm text-gray-600">{{ __("Alterar senha (opcional)") }}</legend>

      <div class="mt-2">
        <label class="block text-sm font-medium text-gray-700" for="current_password">{{ __("Senha atual") }}</label>
        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        @error("current_password")<p class="text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div class="mt-2">
        <label class="block text-sm font-medium text-gray-700" for="password">{{ __("Nova senha") }}</label>
        <input id="password" name="password" type="password" autocomplete="new-password"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
        @error("password")<p class="text-sm text-red-600">{{ $message }}</p>@enderror
      </div>

      <div class="mt-2">
        <label class="block text-sm font-medium text-gray-700" for="password_confirmation">{{ __("Confirmar nova senha") }}</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
      </div>
    </fieldset>

    <div class="flex gap-3">
      <button class="inline-flex rounded-lg bg-indigo-600 px-5 py-2.5 text-white font-medium hover:bg-indigo-700">
        {{ __("Salvar alterações") }}
      </button>
      <a href="{{ url()->previous() }}" class="inline-flex rounded-lg bg-slate-100 px-5 py-2.5 text-slate-700 hover:bg-slate-200">
        {{ __("Cancelar") }}
      </a>
    </div>
  </form>
</div>
@endsection