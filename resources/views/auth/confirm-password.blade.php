<x-guest-layout>
    <style>
        .min-h-screen { background-color: #121212 !important; }
        .bg-white { background-color: #1a1a1a !important; border: 1px solid #333; }
    </style>

    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#ff2d75] rounded-lg mb-4 shadow-[0_0_15px_rgba(255,45,117,0.4)] text-white font-bold text-xl">
            S
        </div>
        <h1 class="text-xl font-bold text-white uppercase tracking-widest">Secure Area</h1>
        <p class="text-[10px] text-gray-500 mt-2 px-4">
            {{ __('This is a secure area of the terminal. Please confirm your password before continuing.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" :value="__('PASSWORD')" class="text-gray-400 text-[10px] font-bold tracking-widest" />
            <x-text-input id="password" 
                class="block mt-1 w-full bg-[#222] border-[#333] text-white focus:border-[#ff2d75] focus:ring-[#ff2d75] rounded-lg"
                type="password"
                name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center mt-8">
            <x-primary-button class="w-full justify-center py-3 bg-[#ff2d75] hover:bg-[#e62668] active:bg-[#ff2d75] focus:ring-[#ff2d75] text-white font-bold rounded-lg border-none">
                {{ __('CONFIRM IDENTITY') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>