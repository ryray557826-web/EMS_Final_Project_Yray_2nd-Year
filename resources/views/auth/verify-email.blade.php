<x-guest-layout>
    <style>
        .min-h-screen { background-color: #121212 !important; }
        .bg-white { background-color: #1a1a1a !important; border: 1px solid #333; }
    </style>

    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-[#ff2d75] rounded-lg mb-4 shadow-[0_0_15px_rgba(255,45,117,0.4)] text-white font-bold text-xl">
            S
        </div>
        <h1 class="text-xl font-bold text-white uppercase tracking-widest">Verify Email</h1>
        <p class="text-[10px] text-gray-500 mt-2 px-4 italic">
            {{ __('Thanks for joining SplaceConnectED. Before getting started, please verify your email address by clicking the link we just emailed to you.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-bold text-xs text-[#ff2d75] text-center uppercase tracking-tighter">
            {{ __('A new verification link has been dispatched.') }}
        </div>
    @endif

    <div class="mt-8 flex flex-col space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center py-3 bg-[#ff2d75] hover:bg-[#e62668] active:bg-[#ff2d75] text-white font-bold rounded-lg border-none">
                {{ __('RESEND VERIFICATION') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-[10px] text-gray-500 hover:text-[#ff2d75] transition-colors uppercase font-bold underline">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>