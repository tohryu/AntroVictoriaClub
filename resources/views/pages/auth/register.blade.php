<x-layouts::auth :title="__('Register')">

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
                <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

                <x-auth-session-status class="text-center" :status="session('status')" />

                @if ($teamInvitation)
                    <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Register')" />
                @endif

                <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
                    @csrf

                    <flux:input
                        name="name"
                        :label="__('Name')"
                        :value="old('name')"
                        type="text"
                        required
                        autofocus
                        autocomplete="name"
                        :placeholder="__('Full name')"
                    />

                    <flux:input
                        name="email"
                        :label="__('Email address')"
                        :value="old('email')"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="email@example.com"
                    />

                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Password')"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        viewable
                    />

                    <flux:input
                        name="password_confirmation"
                        :label="__('Confirm password')"
                        type="password"
                        required
                        autocomplete="new-password"
                        :placeholder="__('Confirm password')"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        viewable
                    />

                    <div class="flex items-center justify-end">
                        <flux:button type="submit" variant="primary" class="w-full bg-amber-500 hover:bg-amber-400 text-black font-bold border-none transition-all shadow-lg shadow-amber-500/20" data-test="register-user-button">
                            {{ __('Create account') }}
                        </flux:button>
                    </div>
                </form>

                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
                    <span>{{ __('Already have an account?') }}</span>
                    <flux:link
                        :href="$teamInvitation ? route('login', ['invitation' => $teamInvitation['code']]) : route('login')"
                        data-test="team-invitation-login-link"
                        class="text-amber-400 hover:text-amber-300 font-semibold transition-colors"
                        wire:navigate
                    >
                        {{ __('Log in') }}
                    </flux:link>
                </div>
            </div>
        </div>
    </div>
</x-layouts::auth>