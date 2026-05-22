<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Header Area Component --}}
            <div class="mb-4">
                <h2 class="text-2xl font-bold uppercase tracking-tighter">
                    Account <span class="text-[#ff2d75]">Settings</span>
                </h2>
                <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest">
                    ID: USER-{{ str_pad(Auth::user()->user_id, 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            {{-- Status Flash Alert Notification Desk --}}
            @if(session('status'))
                <div class="bg-green-500/10 border border-green-500 text-green-400 p-4 rounded-xl text-xs uppercase tracking-wider font-bold">
                    {{ session('status') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500 text-red-400 p-4 rounded-xl text-xs uppercase tracking-wider font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Live Compensation Tracker / Cash Out Engine --}}
                <div class="md:col-span-2 bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl border-l-4 border-l-[#ff2d75] flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <span class="text-[10px] font-black tracking-widest text-[#ff2d75] uppercase block mb-1">
                            Compensation Matrix
                        </span>
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter">Available Balance</h3>
                        <p class="text-gray-400 text-xs mt-1 uppercase tracking-widest">
                            Accumulated dynamic perks ready for immediate withdrawal settlement routing.
                        </p>
                        <div class="text-3xl font-mono text-green-400 font-black mt-3">
                            ₱{{ number_format(Auth::user()->employee->salaryProfile->total_allowance ?? 0.00, 2) }}
                        </div>
                    </div>

                    {{-- Cash out form control interface --}}
                    @if((Auth::user()->employee->salaryProfile->total_allowance ?? 0) > 0)
                        <form action="{{ route('profile.cashout') }}" method="POST" class="w-full md:w-auto">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Confirm processing total allowance cash out? This creates a permanent system audit record.');"
                                    class="w-full md:w-auto bg-[#ff2d75] hover:bg-[#e62668] text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-[0_0_20px_rgba(255,45,117,0.2)] transition-all">
                                💸 Cash Out All Funds
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full md:w-auto bg-[#262626] text-gray-500 border border-[#363636] px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest cursor-not-allowed select-none">
                            No Funds Available
                        </button>
                    @endif
                </div>

                {{-- Profile Information Card --}}
                <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                    <span class="text-[10px] font-black tracking-widest text-[#ff2d75] uppercase block mb-6">Profile Information</span>
                    @include('profile.partials.update-profile-information-form')
                </div>

                {{-- Security Protocol Card --}}
                <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                    <span class="text-[10px] font-black tracking-widest text-[#ff2d75] uppercase block mb-6">Security Protocol</span>
                    @include('profile.partials.update-password-form')
                </div>

                {{-- Danger Zone Card --}}
                <div class="md:col-span-2 bg-[#161616] border border-red-950/40 p-8 rounded-3xl shadow-2xl">
                    <span class="text-[10px] font-black tracking-widest text-red-600 uppercase block mb-6">Danger Zone</span>
                    @include('profile.partials.delete-user-form')
                </div>

            </div>
        </div>
    </div>
</x-app-layout>