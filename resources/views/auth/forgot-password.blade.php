<x-guest-layout>
    <style>
        .min-h-screen { background-color: #121212 !important; }
        .bg-white { background-color: #1a1a1a !important; border: 1px solid #333; }
    </style>

    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#ff2d75] rounded-lg mb-4 shadow-[0_0_15px_rgba(255,45,117,0.4)] text-white font-bold text-xl">
            S
        </div>
        <h1 class="text-xl font-bold text-white uppercase tracking-widest">Reset Access</h1>
        <p class="text-[10px] text-gray-500 mt-2 px-4">
            {{ __('Forgotten your password? Enter your email and we will send a reset link to your terminal.') }}
        </p>
    </div>

    <x-auth-session-status class="mb-4 text-pink-500 text-xs font-bold text-center" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('SYSTEM EMAIL')" class="text-gray-400 text-[10px] font-bold tracking-widest" />
            <x-text-input id="email" 
                class="block mt-1 w-full bg-[#222] border-[#333] text-white focus:border-[#ff2d75] focus:ring-[#ff2d75] rounded-lg" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col space-y-4 mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-[#ff2d75] hover:bg-[#e62668] active:bg-[#ff2d75] focus:ring-[#ff2d75] text-white font-bold rounded-lg border-none">
                {{ __('EMAIL PASSWORD RESET LINK') }}
            </x-primary-button>
            
            <div class="text-center">
                <a class="text-[10px] text-gray-500 hover:text-[#ff2d75] transition-colors uppercase font-bold" href="{{ route('login') }}">
                    {{ __('Back to Login') }}
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>