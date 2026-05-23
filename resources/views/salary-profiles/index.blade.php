<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Header Area Component --}}
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-4xl font-black uppercase tracking-tighter">
                        Salary <span class="text-[#ff2d75]">Profiles</span>
                    </h2>
                    <p class="text-gray-400 text-xs mt-2 uppercase tracking-widest">
                        Review active base pay rates and tracked structural balances assigned across personnel records.
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500 text-green-400 p-4 rounded-xl text-xs uppercase tracking-wider font-bold">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Salary Profiles Information Desk --}}
            <div class="bg-[#161616] p-8 rounded-3xl shadow-2xl border border-[#262626]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter">Active Personnel Compensation Frameworks</h3>
                    <span class="bg-[#262626] text-white text-[9px] font-black px-4 py-2 rounded-full uppercase tracking-widest">
                        System Level Overview
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-500 text-[10px] uppercase tracking-widest">
                                <th class="pb-4 px-2">Employee Name</th>
                                <th class="pb-4 px-2">Assigned Position / Rank</th>
                                <th class="pb-4 px-2 w-[280px]">Base Hourly Rate Configuration</th>
                                <th class="pb-4 px-2">Total Allowance Balance</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse($employees as $emp)
                                <tr class="border-t border-[#262626] hover:bg-[#1c1c1c] transition-colors">
                                    {{-- Employee Profile Info --}}
                                    <td class="py-4 px-2 font-black uppercase tracking-wide text-white">
                                        {{ $emp->full_name ?? $emp->name }}
                                    </td>
                                    
                                    {{-- Assigned Position --}}
                                    <td class="py-4 px-2 text-gray-400 font-medium">
                                        {{ $emp->position->position_title ?? 'Unassigned Tier' }}
                                    </td>

                                    {{-- Base Hourly Rate Inline Form Configurator --}}
                                    <td class="py-4 px-2">
                                        <form action="{{ route('salary-profiles.update', $emp->employee_id) }}" method="POST" class="flex items-center space-x-2">
                                            @csrf
                                            @method('POST')
                                            <div class="relative flex items-center">
                                                <span class="absolute left-3 text-xs font-bold text-gray-500">₱</span>
                                                <input type="number" 
                                                       name="base_hourly_rate" 
                                                       value="{{ $emp->salaryProfile->base_hourly_rate ?? 0.00 }}" 
                                                       step="0.01" 
                                                       min="0"
                                                       class="w-32 bg-[#0a0a0a] border {{ ($emp->salaryProfile->base_hourly_rate ?? 0) == 0 ? 'border-yellow-600/50 text-yellow-500' : 'border-[#262626] text-green-400' }} rounded-lg pl-6 pr-2 py-1.5 text-xs font-mono font-bold focus:border-[#ff2d75] outline-none transition-all">
                                            </div>
                                            
                                            <button type="submit" class="bg-[#262626] hover:bg-[#ff2d75] text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all border border-[#333] hover:border-[#ff2d75]">
                                                Save
                                            </button>

                                            @if(($emp->salaryProfile->base_hourly_rate ?? 0) == 0)
                                                <span class="text-[8px] bg-yellow-500/10 text-yellow-500 font-bold px-2 py-1 rounded border border-yellow-500/20 uppercase tracking-tight">Unconfigured</span>
                                            @endif
                                        </form>
                                    </td>

                                    {{-- Dynamic Live Accumulative Total Allowance Column --}}
                                    <td class="py-4 px-2 font-mono text-[#ff2d75] font-black">
                                        ₱{{ number_format($emp->salaryProfile->total_allowance ?? 0.00, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500 uppercase text-[10px] tracking-widest">
                                        No active employee salary profiles detected in database logs.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>