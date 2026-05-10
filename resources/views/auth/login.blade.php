<x-guest-layout>
    <style>
        /* Force background and container to match the dark theme */
        .min-h-screen { background-color: #121212 !important; }
        .bg-white { background-color: #1a1a1a !important; border: 1px solid #333; }
    </style>

    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#ff2d75] rounded-lg mb-4 shadow-[0_0_15px_rgba(255,45,117,0.4)] text-white font-bold text-xl">
            S
        </div>
        <h1 class="text-2xl font-bold text-white">SPLACE<span class="text-[#ff2d75]">CONNECTED</span></h1>
        <p class="text-xs text-gray-400 mt-2 uppercase tracking-widest">Portal Authentication</p>
    </div>

    <x-auth-session-status class="mb-4 text-pink-500 text-sm" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('SYSTEM EMAIL')" class="text-gray-400 text-[10px] font-bold tracking-widest" />
            <x-text-input id="email" 
                class="block mt-1 w-full bg-[#222] border-[#333] text-white focus:border-[#ff2d75] focus:ring-[#ff2d75] rounded-lg" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('PASSWORD')" class="text-gray-400 text-[10px] font-bold tracking-widest" />
            <x-text-input id="password" 
                class="block mt-1 w-full bg-[#222] border-[#333] text-white focus:border-[#ff2d75] focus:ring-[#ff2d75] rounded-lg"
                type="password"
                name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded bg-[#222] border-[#333] text-[#ff2d75] shadow-sm focus:ring-[#ff2d75]" name="remember">
                <span class="ms-2 text-xs text-gray-500 uppercase tracking-tight">{{ __('Keep me logged in') }}</span>
            </label>
        </div>

        <div class="flex flex-col space-y-4 mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-[#ff2d75] hover:bg-[#e62668] active:bg-[#ff2d75] focus:ring-[#ff2d75] text-white font-bold rounded-lg border-none">
                {{ __('AUTHORIZE LOGIN') }}
            </x-primary-button>

            <div class="flex justify-between items-center px-1">
                @if (Route::has('password.request'))
                    <a class="text-[10px] text-gray-500 hover:text-[#ff2d75] transition-colors uppercase font-bold" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif

                @if (Route::has('register'))
                    <a class="text-[10px] text-gray-500 hover:text-[#ff2d75] transition-colors uppercase font-bold" href="{{ route('register') }}">
                        {{ __('Create Account') }}
                    </a>
                @endif
            </div>
        </div>
    </form>
</x-guest-layout>