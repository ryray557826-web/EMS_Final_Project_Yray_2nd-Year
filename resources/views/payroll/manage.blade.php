<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-4xl mx-auto bg-[#161616] p-10 rounded-3xl border border-[#262626] shadow-2xl space-y-8">
            
            {{-- Title Banner --}}
            <div class="flex justify-between items-start border-b border-[#262626] pb-6">
                <div>
                    <h2 class="text-3xl font-black uppercase tracking-tighter">Transaction Management Desk</h2>
                    <p class="text-mono text-xs text-[#ff2d75] mt-1">{{ $payroll->reference_number }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[9px] text-gray-500 block uppercase tracking-widest">Workflow State</span>
                    @php
                        $normalizedStatus = strtolower($payroll->status);
                        $statusClass = $normalizedStatus === 'pending approval' ? 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' : 
                                      (($normalizedStatus === 'processed' && $payroll->is_locked) || $normalizedStatus === 'completed' ? 'bg-green-500/10 text-green-500 border-green-500/20' : 
                                      ($normalizedStatus === 'processed' ? 'bg-blue-500/10 text-blue-400 border-blue-500/20' : 
                                      ($normalizedStatus === 'rolled back' ? 'bg-orange-500/10 text-orange-400 border-orange-500/20' : 'bg-red-500/10 text-red-500 border-red-500/20')));
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border inline-block mt-1 {{ $statusClass }}">
                        {{ $payroll->status }}
                    </span>
                </div>
            </div>

            {{-- Personnel Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-[#0a0a0a] p-4 rounded-xl border border-[#262626]">
                    <span class="text-[9px] text-gray-500 uppercase tracking-widest block mb-1">Target Account Personnel</span>
                    <span class="font-bold text-sm block">{{ $payroll->employee->full_name }}</span>
                </div>
                <div class="bg-[#0a0a0a] p-4 rounded-xl border border-[#262626]">
                    <span class="text-[9px] text-gray-500 uppercase tracking-widest block mb-1">Base Pay Profile Hourly Rate</span>
                    <span class="font-mono text-sm block">₱{{ number_format($payroll->employee->salaryProfile->base_hourly_rate ?? 0, 2) }}</span>
                </div>
                <div class="bg-[#0a0a0a] p-4 rounded-xl border border-[#262626]">
                    <span class="text-[9px] text-gray-500 uppercase tracking-widest block mb-1">Current Balance Allowance</span>
                    <span class="font-mono text-sm text-green-400 block">₱{{ number_format($payroll->employee->salaryProfile->total_allowance ?? 0, 2) }}</span>
                </div>
            </div>

            {{-- Financial Adjustment Compare Sheet --}}
            <div class="bg-[#0a0a0a] p-6 rounded-2xl border border-[#262626] space-y-4">
                <h4 class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Ledger Calculation Values</h4>
                
                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="py-2 border-b border-[#1c1c1c] flex justify-between">
                        <span class="text-gray-500">Gross Calculated Base:</span>
                        <span class="font-mono text-white">₱{{ number_format($payroll->gross_amount, 2) }}</span>
                    </div>
                    <div class="py-2 border-b border-[#1c1c1c] flex justify-between">
                        <span class="text-gray-500">Bonuses / Incentives:</span>
                        <span class="font-mono text-green-400">+₱{{ number_format($payroll->bonus_amount, 2) }}</span>
                    </div>
                    <div class="py-2 border-b border-[#1c1c1c] flex justify-between">
                        <span class="text-gray-500">Total Deductions:</span>
                        <span class="font-mono text-red-400">-₱{{ number_format($payroll->deductions, 2) }}</span>
                    </div>
                    <div class="py-2 border-b border-[#1c1c1c] flex justify-between">
                        <span class="text-gray-500">Net Calculated Payout:</span>
                        <span class="font-mono text-[#ff2d75] font-bold">₱{{ number_format($payroll->net_amount, 2) }}</span>
                    </div>
                </div>

                {{-- Reason / Context Segment --}}
                <div class="pt-4 border-t border-[#1c1c1c]">
                    <span class="text-[9px] text-gray-500 uppercase tracking-widest block mb-1">
                        {{ $payroll->status === 'Pending Approval' ? 'Modification Request Notes Context' : 'Ledger System Notes Context' }}
                    </span>
                    <p class="text-xs text-gray-300 italic bg-[#161616] p-3 rounded-lg border border-[#262626]">
                        {{ $payroll->notes ?? 'No explicit historical adjustments noted for this ledger block.' }}
                    </p>
                </div>
            </div>

            {{-- Terminal Execution Forms Desk --}}
            <div class="border-t border-[#262626] pt-6">
                @if($payroll->is_locked)
                    <div class="bg-green-500/10 border border-green-500/20 text-green-400 p-6 rounded-xl text-center space-y-2">
                        <p class="text-xs font-black uppercase tracking-widest">🔒 Ledger Block Finalized & Read-Only</p>
                        <p class="text-[11px] text-gray-400 normal-case">
                            This transaction was committed and locked. The final gross amount of 
                            <span class="font-mono text-white font-bold">₱{{ number_format($payroll->final_gross_pay, 2) }}</span> 
                            has been permanently added to the employee's total allowance.
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="bg-yellow-500/10 border border-yellow-500/20 text-yellow-500 p-4 rounded-xl text-center text-[10px] font-black uppercase tracking-widest">
                            ⚠️ Warning: Committing will permanently lock this record and transfer the gross amount to the personnel allowance profile.
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            {{-- Commit Action --}}
                            <form action="{{ route('payroll.update', $payroll->transaction_id) }}" method="POST" onsubmit="return confirm('Commit ledger? This will lock the record and update employee allowances.');">
                                @csrf @method('PATCH')
                                <input type="hidden" name="admin_action" value="commit">
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-black uppercase text-[10px] tracking-widest py-3 rounded-xl transition-all shadow-md">
                                    Commit Final
                                </button>
                            </form>

                            {{-- Reject Action --}}
                            <form action="{{ route('payroll.update', $payroll->transaction_id) }}" method="POST" onsubmit="return confirm('Reject this payroll transaction request?');">
                                @csrf @method('PATCH')
                                <input type="hidden" name="admin_action" value="reject">
                                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-black uppercase text-[10px] tracking-widest py-3 rounded-xl transition-all shadow-md">
                                    Reject
                                </button>
                            </form>

                            {{-- Rollback Action --}}
                            <form action="{{ route('payroll.update', $payroll->transaction_id) }}" method="POST" onsubmit="return confirm('Execute system rollback on this tracking row?');">
                                @csrf @method('PATCH')
                                <input type="hidden" name="admin_action" value="rollback">
                                <button type="submit" class="w-full bg-gray-700 hover:bg-gray-600 text-white font-black uppercase text-[10px] tracking-widest py-3 rounded-xl transition-all shadow-md">
                                    Rollback
                                </button>
                            </form>

                            {{-- Edit / Adjustment Route Alternative --}}
                            <a href="{{ route('payroll.edit', $payroll->transaction_id) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black uppercase text-[10px] tracking-widest py-3 rounded-xl transition-all shadow-md text-center flex items-center justify-center">
                                Adjust Further
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Return Navigation Context Footer --}}
            <div class="flex justify-start pt-2">
                <a href="{{ route('payroll.index') }}" class="text-xs text-gray-500 hover:text-white uppercase tracking-widest transition-colors">
                    &larr; Return to Payroll Ledgers
                </a>
            </div>

        </div>
    </div>
</x-app-layout>