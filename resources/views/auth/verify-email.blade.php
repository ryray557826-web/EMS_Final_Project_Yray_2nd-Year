<x-guest-layout>
    <div class="max-w-md w-full px-6">
        <div class="bg-[#161616] p-10 rounded-3xl shadow-2xl">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#121212] rounded-2xl mb-6 shadow-inner">
                    <span class="text-[#ff2d75] font-black text-2xl">S</span>
                </div>
                <h1 class="text-xl font-black text-white uppercase tracking-tighter">
                    SPLACE<span class="text-[#ff2d75]">CONNECTED</span>
                </h1>
                <p class="text-[10px] text-gray-500 mt-4 uppercase tracking-widest leading-relaxed px-2">
                    {{ __('Before proceeding, please verify your email address via the link sent to your inbox.') }}
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-6 text-[10px] font-black text-[#ff2d75] text-center uppercase tracking-widest animate-pulse">
                    {{ __('A new verification link has been dispatched.') }}
                </div>
            @endif

            <div class="space-y-6">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="w-full bg-[#ff2d75] hover:bg-[#e62668] text-white font-bold py-4 rounded-xl transition duration-300 uppercase tracking-widest text-sm shadow-lg shadow-[#ff2d75]/10">
                        {{ __('Resend Verification Link') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="text-center">
                    @csrf
                    <button type="submit" class="text-[10px] text-gray-500 hover:text-white transition-colors uppercase font-bold tracking-widest underline decoration-gray-700 underline-offset-4">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>