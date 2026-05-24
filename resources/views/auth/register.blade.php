<x-guest-layout>
    <div class="max-w-md w-full px-6">
        <div class="bg-[#161616] p-10 rounded-3xl shadow-2xl border border-[#262626]">
            
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-[#121212] border border-[#262626] rounded-2xl mb-6 shadow-inner">
                    <span class="text-[#ff2d75] font-black text-2xl">S</span>
                </div>
                <h1 class="text-xl font-black text-white uppercase tracking-tighter">
                    SPLACE<span class="text-[#ff2d75]">CONNECTED</span>
                </h1>
                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-widest">Create Employee Account</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="space-y-6">
                    
                    {{-- Full Name Input Node --}}
                    <div>
                        <label for="name" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                            class="w-full bg-[#121212] border border-[#262626] text-white rounded-xl p-4 text-sm focus:border-[#ff2d75] focus:ring-1 focus:ring-[#ff2d75] outline-none transition-all placeholder:text-[#333]"
                            placeholder="Juan Dela Cruz">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-xs text-red-500 font-bold uppercase tracking-wider" />
                    </div>

                    {{-- System Email Input Node --}}
                    <div>
                        <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">System Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                            class="w-full bg-[#121212] border border-[#262626] text-white rounded-xl p-4 text-sm focus:border-[#ff2d75] focus:ring-1 focus:ring-[#ff2d75] outline-none transition-all placeholder:text-[#333]"
                            placeholder="username@splacebpo.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-500 font-bold uppercase tracking-wider" />
                    </div>

                    {{-- Password Input Node --}}
                    <div>
                        <label for="password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="w-full bg-[#121212] border border-[#262626] text-white rounded-xl p-4 text-sm focus:border-[#ff2d75] focus:ring-1 focus:ring-[#ff2d75] outline-none transition-all placeholder:text-[#333]"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-500 font-bold uppercase tracking-wider" />
                    </div>

                    {{-- Password Confirmation Input Node --}}
                    <div>
                        <label for="password_confirmation" class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="w-full bg-[#121212] border border-[#262626] text-white rounded-xl p-4 text-sm focus:border-[#ff2d75] focus:ring-1 focus:ring-[#ff2d75] outline-none transition-all placeholder:text-[#333]"
                            placeholder="••••••••">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-500 font-bold uppercase tracking-wider" />
                    </div>

                    {{-- Operational Submissions Node --}}
                    <div class="flex flex-col gap-4 pt-2">
                        <button type="submit" class="w-full bg-[#ff2d75] hover:bg-[#e62668] text-white font-black py-4 rounded-xl transition duration-300 uppercase tracking-widest text-[11px] shadow-lg shadow-[#ff2d75]/10">
                            {{ __('Authorize Account') }}
                        </button>
                        <a href="{{ route('login') }}" class="text-center text-[10px] text-gray-500 hover:text-white transition-colors uppercase font-bold tracking-widest">
                            {{ __('Already have an account? Sign In') }}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>