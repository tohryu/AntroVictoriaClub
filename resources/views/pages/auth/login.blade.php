<x-layouts::auth :title="__('Log in')">

    <style>
        @keyframes floatStars {
            0% {
                transform: translateY(0px) scale(0.8);
                opacity: 0.3;
            }
            50% {
                opacity: 1;
                transform: translateY(-40px) scale(1.2);
            }
            100% {
                transform: translateY(-80px) scale(0.8);
                opacity: 0.3;
            }
        }
        .star-particle {
            animation: floatStars var(--duration, 6s) infinite ease-in-out var(--delay, 0s);
        }
    </style>

    <div class="fixed inset-0 min-h-screen w-full flex items-center justify-center p-4 overflow-y-auto bg-black">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">

            <div class="star-particle absolute top-[15%] left-[10%] w-1.5 h-1.5 bg-amber-300 rounded-full shadow-[0_0_8px_#f59e0b]" style="--duration: 5s; --delay: 0s;"></div>
            <div class="star-particle absolute top-[30%] left-[85%] w-2 h-2 bg-white rounded-full shadow-[0_0_10px_#fff]" style="--duration: 7s; --delay: 1s;"></div>
            <div class="star-particle absolute top-[70%] left-[15%] w-1 h-1 bg-amber-200 rounded-full shadow-[0_0_6px_#fde047]" style="--duration: 6s; --delay: 2s;"></div>
            <div class="star-particle absolute top-[85%] left-[75%] w-2 h-2 bg-amber-400 rounded-full shadow-[0_0_12px_#fbbf24]" style="--duration: 8s; --delay: 0.5s;"></div>
            <div class="star-particle absolute top-[40%] left-[90%] w-1 h-1 bg-white rounded-full shadow-[0_0_6px_#fff]" style="--duration: 4s; --delay: 3s;"></div>
            <div class="star-particle absolute top-[20%] left-[50%] w-1.5 h-1.5 bg-amber-300 rounded-full shadow-[0_0_8px_#f59e0b]" style="--duration: 9s; --delay: 1.5s;"></div>

            <div class="star-particle absolute top-[60%] left-[05%] w-2 h-2 bg-white rounded-full shadow-[0_0_10px_#fff]" style="--duration: 6.5s; --delay: 2.5s;"></div>
            <div class="star-particle absolute top-[10%] left-[70%] w-1 h-1 bg-amber-100 rounded-full shadow-[0_0_6px_#fef3c7]" style="--duration: 5.5s; --delay: 0.8s;"></div>
            <div class="star-particle absolute top-[80%] left-[35%] w-1.5 h-1.5 bg-amber-300 rounded-full shadow-[0_0_8px_#f59e0b]" style="--duration: 7.5s; --delay: 1.2s;"></div>
            <div class="star-particle absolute top-[45%] left-[20%] w-1 h-1 bg-white rounded-full shadow-[0_0_6px_#fff]" style="--duration: 4.8s; --delay: 3.5s;"></div>
            <div class="star-particle absolute top-[90%] left-[60%] w-2 h-2 bg-amber-400 rounded-full shadow-[0_0_10px_#fbbf24]" style="--duration: 8.5s; --delay: 0.2s;"></div>
            <div class="star-particle absolute top-[25%] left-[30%] w-1 h-1 bg-white rounded-full shadow-[0_0_6px_#fff]" style="--duration: 5.2s; --delay: 1.8s;"></div>

            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-amber-500/10 rounded-full blur-[140px]"></div>
        </div>

        <div class="relative z-10 w-full max-w-md bg-zinc-900/85 border border-amber-500/30 hover:border-amber-500/50 transition-all rounded-2xl p-6 sm:p-8 backdrop-blur-xl shadow-2xl shadow-amber-950/20 my-auto">

            <div class="text-center mb-6">
                <span class="inline-block bg-amber-950/60 text-amber-300 border border-amber-500/40 text-[10px] uppercase font-bold tracking-widest px-3 py-1 rounded-full mb-3 backdrop-blur-md">
                    ★ Club Nocturno ★
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white uppercase">
                    Victoria <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-yellow-400 to-amber-500">Luxury</span>
                </h1>
                <p class="text-zinc-500 text-xs font-light mt-1">
                    📍 Boulevard Europa #12, Puebla, Mexico
                </p>
            </div>

            <div class="flex flex-col gap-6">
                <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

                <x-auth-session-status class="text-center" :status="session('status')" />

                @if ($teamInvitation)
                    <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Log in')" />
                @endif

                <x-passkey-verify />

                <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center gap-2 bg-zinc-950 hover:bg-zinc-800 text-white font-semibold text-sm py-2.5 px-4 rounded-xl border border-zinc-800 hover:border-amber-500/40 transition-all shadow-sm group">
                    <svg class="w-4 h-4 transition-transform group-hover:scale-110" viewBox="0 0 24 24">
                        <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.6l3.1-3.1C17.3 1.7 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.3l3.7 2.9C6.5 7.2 9 5 12 5z"/>
                        <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.6-.2-2.3H12v4.5h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.8z"/>
                        <path fill="#FBBC05" d="M5.6 14.8c-.2-.7-.4-1.5-.4-2.3s.2-1.6.4-2.3L1.9 7.3C.7 9.7 0 10.8 0 12s.7 2.3 1.9 4.7l3.7-2.9z"/>
                        <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2.2-6.4-5.2L1.9 16C3.7 19.7 7.5 23 12 23z"/>
                    </svg>
                    <span>{{ __('Sign in with Google') }}</span>
                </a>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-zinc-800"></div>
                    <span class="flex-shrink mx-4 text-xs font-semibold uppercase text-zinc-500">
                        {{ __('or continue with email') }}
                    </span>
                    <div class="flex-grow border-t border-zinc-800"></div>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                    @csrf

                    <flux:input
                        name="email"
                        :label="__('Email address')"
                        :value="old('email')"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                    />

                    <div class="relative">
                        <flux:input
                            name="password"
                            :label="__('Password')"
                            type="password"
                            required
                            autocomplete="current-password"
                            :placeholder="__('Password')"
                            viewable
                        />

                        @if (Route::has('password.request'))
                            <flux:link class="absolute top-0 text-sm end-0 text-amber-400 hover:text-amber-300 transition-colors" :href="route('password.request')" wire:navigate>
                                {{ __('Forgot your password?') }}
                            </flux:link>
                        @endif
                    </div>

                    <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />

                    <div class="flex items-center justify-end">
                        <flux:button variant="primary" type="submit" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-bold border-none transition-all shadow-lg shadow-amber-500/20" data-test="login-button">
                            {{ __('Log in') }}
                        </flux:button>
                    </div>
                </form>

                <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-400">
                    <span>{{ __('Don\'t have an account?') }}</span>
                    <flux:link
                        :href="$teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register')"
                        data-test="register-link"
                        class="text-amber-400 hover:text-amber-300 font-semibold transition-colors"
                        wire:navigate
                    >
                        {{ __('Sign up') }}
                    </flux:link>
                </div>
            </div>
        </div>
    </div>
</x-layouts::auth>