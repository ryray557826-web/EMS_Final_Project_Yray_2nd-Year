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
                        System-wide reference terminal for tracked personnel rates, core organizational branches, and accumulative ledger states.
                    </p>
                </div>
            </div>

            {{-- Salary Profiles Information Desk --}}
            <div class="bg-[#161616] p-8 rounded-3xl shadow-2xl border border-[#262626]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter">Personnel Compensation Matrix</h3>
                    <span class="bg-[#262626] text-white text-[9px] font-black px-4 py-2 rounded-full uppercase tracking-widest">
                        Read-Only Master Ledger
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-500 text-[10px] uppercase tracking-widest">
                                <th class="pb-4 px-2">Employee Name</th>
                                <th class="pb-4 px-2">Deployed Branch</th>
                                <th class="pb-4 px-2">Assigned Position / Rank</th>
                                <th class="pb-4 px-2">Base Hourly Rate</th>
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
                                    
                                    {{-- Deployed Branch Assignment (New Column) --}}
                                    <td class="py-4 px-2 text-zinc-400 font-medium uppercase tracking-wider text-[11px]">
                                        {{ $emp->branch->branch_name ?? 'Global / Unassigned' }}
                                    </td>
                                    
                                    {{-- Assigned Position --}}
                                    <td class="py-4 px-2 text-gray-400 font-medium">
                                        {{ $emp->position->position_title ?? 'Unassigned Tier' }}
                                    </td>

                                    {{-- Base Hourly Rate (Pure Text Output) --}}
                                    <td class="py-4 px-2 font-mono text-green-400 font-bold">
                                        ₱{{ number_format($emp->salaryProfile->base_hourly_rate ?? 0.00, 2) }}
                                    </td>

                                    {{-- Dynamic Live Accumulative Total Allowance Column --}}
                                    <td class="py-4 px-2 font-mono text-[#ff2d75] font-black">
                                        ₱{{ number_format($emp->salaryProfile->total_allowance ?? 0.00, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500 uppercase text-[10px] tracking-widest">
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