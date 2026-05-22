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
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Update your secure access token</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="space-y-6">
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">System Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                            class="w-full bg-[#121212] border-none text-white rounded-xl focus:ring-2 focus:ring-[#ff2d75] transition-all placeholder:text-[#333]"
                            placeholder="username@splacebpo.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">New Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full bg-[#121212] border-none text-white rounded-xl focus:ring-2 focus:ring-[#ff2d75] transition-all placeholder:text-[#333]"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Confirm New Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full bg-[#121212] border-none text-white rounded-xl focus:ring-2 focus:ring-[#ff2d75] transition-all placeholder:text-[#333]"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full bg-[#ff2d75] hover:bg-[#e62668] text-white font-bold py-4 rounded-xl transition duration-300 uppercase tracking-widest text-sm shadow-lg shadow-[#ff2d75]/10">
                        {{ __('Reset Password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>