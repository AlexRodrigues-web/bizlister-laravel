<x-guest-layout>
    <x-auth-card>
        {{-- Logo / título --}}
        <x-slot name="logo">
            <a href="/" class="inline-flex items-center gap-3">
                <x-application-logo class="w-12 h-12 fill-current text-[#c41210]" />
                <span class="text-lg font-bold tracking-tight text-slate-800">BizLister</span>
            </a>
        </x-slot>

        {{-- Hero suave --}}
        <div class="mb-6 rounded-xl border border-slate-200 bg-gradient-to-br from-[#c41210]/10 via-white to-white p-4">
            <h1 class="text-xl font-semibold text-slate-900">Redefinir senha</h1>
            <p class="mt-1 text-sm text-slate-600">
                Esqueceu sua senha? Sem problemas. Informe seu e-mail e enviaremos um link para redefini-la.
            </p>
        </div>

        {{-- Status / Errors --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            {{-- E-mail --}}
            <div>
                <x-label for="email" :value="__('E-mail')" class="text-slate-700 font-medium" />
                <x-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="voce@email.com"
                />
            </div>

            {{-- CTA + retorno ao login --}}
            <div class="flex items-center justify-between pt-2">
                <x-button class="bg-[#c41210] hover:bg-[#aa0f0e] focus:ring-[#c41210]/30">
                    Enviar link de redefinição
                </x-button>

                @if (Route::has('login'))
                    <a href="{{ route('login') }}"
                       class="text-sm text-slate-600 hover:text-slate-800 hover:underline">
                        Voltar ao login
                    </a>
                @endif
            </div>
        </form>

        {{-- Dica opcional --}}
        <p class="mt-6 text-center text-xs text-slate-500">
            Se o e-mail não chegar em alguns minutos, verifique a pasta de spam/lixo eletrônico.
        </p>
    </x-auth-card>
</x-guest-layout>
