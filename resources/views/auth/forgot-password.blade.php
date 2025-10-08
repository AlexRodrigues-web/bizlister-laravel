<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Esqueceu sua senha? Sem problemas. Informe seu E-mail e nós enviaremos um link para redefini-la.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- E-mail Address -->
            <div>
                <x-label for="E-mail" :value="__('E-mail')" />

                <x-input id="E-mail" class="block mt-1 w-full" type="E-mail" name="E-mail" :value="old('E-mail')" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Enviar link de redefiniÃƒÂ§ÃƒÂ£o') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
