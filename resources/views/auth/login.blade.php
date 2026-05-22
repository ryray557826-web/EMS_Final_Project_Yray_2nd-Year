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
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Portal Authentication</p>
            </div>
Z
            <x-auth-session-status class="mb-4 text-[#ff2d75] text-[10px] font-bold text-center uppercase tracking-widest" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">System Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="w-full bg-[#121212] border-none text-white rounded-xl focus:ring-2 focus:ring-[#ff2d75] transition-all placeholder:text-[#333]"
                            placeholder="username@splacebpo.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                            class="w-full bg-[#121212] border-none text-white rounded-xl focus:ring-2 focus:ring-[#ff2d75] transition-all placeholder:text-[#333]"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-none bg-[#121212] text-[#ff2d75] focus:ring-[#ff2d75]" name="remember">
                        <span class="ms-3 text-[10px] text-gray-500 uppercase tracking-widest font-bold">{{ __('Keep me logged in') }}</span>
                    </label>

                    <div class="flex flex-col gap-4 pt-2">
                        <button type="submit" class="w-full bg-[#ff2d75] hover:bg-[#e62668] text-white font-bold py-4 rounded-xl transition duration-300 uppercase tracking-widest text-sm shadow-lg shadow-[#ff2d75]/10">
                            {{ __('Authorize Login') }}
                        </button>

                        <div class="flex justify-between items-center px-1">
                            @if (Route::has('password.request'))
                                <a class="text-[10px] text-gray-500 hover:text-white transition-colors uppercase font-bold tracking-widest" href="{{ route('password.request') }}">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                            @if (Route::has('register'))
                                <a class="text-[10px] text-gray-500 hover:text-white transition-colors uppercase font-bold tracking-widest" href="{{ route('register') }}">
                                    {{ __('Create Account') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>