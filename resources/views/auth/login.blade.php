<x-guest-layout>
    <x-auth-card>
        {{-- Topo com logo e título --}}
        <x-slot name="logo">
            <a href="/" class="inline-flex items-center gap-3">
                <x-application-logo class="w-12 h-12 fill-current text-[#c41210]" />
                <span class="text-lg font-bold tracking-tight text-slate-800">BizLister</span>
            </a>
        </x-slot>

        {{-- Cabeçalho/hero suave --}}
        <div class="mb-6 rounded-xl border border-slate-200 bg-gradient-to-br from-[#c41210]/10 via-white to-white p-4">
            <h1 class="text-xl font-semibold text-slate-900">Entrar</h1>
            <p class="mt-1 text-sm text-slate-600">Acesse sua conta para gerenciar favoritos e perfil.</p>
        </div>

        {{-- Status & Errors --}}
        <x-auth-session-status class="mb-4" :status="session('status')" />
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-red-700">
                {{ session('error') }}
            </div>
        @endif
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        {{-- Form padrão --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            {{-- Email --}}
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

            {{-- Senha --}}
            <div>
                <x-label for="password" :value="__('Senha')" class="text-slate-700 font-medium" />
                <x-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
            </div>

            {{-- Lembrar --}}
            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="rounded border-gray-300 text-[#c41210] shadow-sm focus:border-[#c41210] focus:ring focus:ring-[#c41210]/20 focus:ring-opacity-50"
                        name="remember">
                    <span class="ml-2 text-sm text-gray-600">Lembrar-me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm font-medium text-[#c41210] hover:underline"
                       href="{{ route('password.request') }}">
                        Esqueceu a senha?
                    </a>
                @endif
            </div>

            {{-- CTA entrar + link cadastro --}}
            <div class="flex items-center justify-between pt-2">
                <x-button class="bg-[#c41210] hover:bg-[#aa0f0e] focus:ring-[#c41210]/30">
                    Entrar
                </x-button>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="text-sm text-slate-600 hover:text-slate-800 hover:underline">
                        Criar conta
                    </a>
                @endif
            </div>
        </form>

        {{-- Divisor social --}}
        <div class="mt-8">
            <div class="relative">
                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="bg-white px-3 text-xs font-medium tracking-wide text-slate-500">
                        ou entre com
                    </span>
                </div>
            </div>

            {{-- Botões sociais --}}
            <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                @if (Route::has('social.redirect'))
                    {{-- Google --}}
                    <a href="{{ route('social.redirect', ['provider' => 'google']) }}"
                       class="inline-flex w-full items-center justify-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#c41210]/30 focus:ring-offset-1"
                       rel="noopener">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 533.5 544.3" class="h-5 w-5" aria-hidden="true">
                            <path fill="#EA4335" d="M533.5 278.4c0-17.4-1.6-34.1-4.6-50.2H272.1v95h146.9c-6.3 34-25 62.7-53.4 82v68h86.3c50.5-46.5 81.6-115.1 81.6-194.8z"/>
                            <path fill="#34A853" d="M272.1 544.3c72.9 0 134.1-24.1 178.8-65.1l-86.3-68c-24 16.1-54.6 25.5-92.5 25.5-71 0-131.2-47.9-152.7-112.2h-90v70.6c44.3 88 135.1 149.2 242.7 149.2z"/>
                            <path fill="#4A90E2" d="M119.4 324.5c-10.1-30-10.1-62.3 0-92.3v-70.6h-90c-37.2 74-37.2 159.4 0 233.4l90-70.5z"/>
                            <path fill="#FBBC05" d="M272.1 106.9c39.6-.6 77.6 13.9 106.7 40.9l79.8-79.8C403.2 20.3 340.9-.1 272.1 0 164.6 0 73.8 61.2 29.5 149.2l90 70.6C140.9 167.6 201.1 119.7 272.1 119.7z"/>
                        </svg>
                        <span>Google</span>
                    </a>

                    {{-- Facebook --}}
                    <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}"
                       class="inline-flex w-full items-center justify-center gap-3 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#c41210]/30 focus:ring-offset-1"
                       rel="noopener">
                        <svg class="h-5 w-5 text-[#1877F2]" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M22 12.07C22 6.48 17.52 2 11.93 2 6.35 2 1.87 6.48 1.87 12.07c0 4.93 3.6 9.02 8.32 9.88v-6.99H7.9v-2.89h2.29V9.83c0-2.26 1.35-3.5 3.41-3.5.99 0 2.03.18 2.03.18v2.24h-1.14c-1.12 0-1.47.69-1.47 1.39v1.67h2.5l-.4 2.89h-2.1v6.99c4.72-.86 8.32-4.95 8.32-9.88z"/>
                        </svg>
                        <span>Facebook</span>
                    </a>
                @endif
            </div>

            {{-- Aviso de privacidade/termos opcional --}}
            <p class="mt-4 text-center text-xs text-slate-500">
                Ao entrar, você concorda com nossos <a href="{{ url('/termos') }}" class="underline hover:text-slate-700">Termos de Uso</a> e
                <a href="{{ url('/privacidade') }}" class="underline hover:text-slate-700">Política de Privacidade</a>.
            </p>
        </div>
    </x-auth-card>
</x-guest-layout>
