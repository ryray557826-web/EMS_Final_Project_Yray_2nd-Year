<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <a href="{{ route('payroll.index') }}" class="text-[10px] text-gray-500 hover:text-[#ff2d75] uppercase tracking-widest font-black transition">
                    ← Back to Ledgers
                </a>
            </div>

            <div class="bg-[#161616] p-10 rounded-3xl">
                <div class="mb-10">
                    <span class="text-[9px] uppercase tracking-widest text-[#ff2d75] font-black font-mono">
                        Transaction: {{ $payroll->reference_number }}
                    </span>
                    <h2 class="text-3xl font-black uppercase tracking-tighter mt-1">Adjust Compensation</h2>
                    <p class="text-xs text-gray-500 mt-2 uppercase tracking-widest">
                        Modifying logs for: <span class="text-white font-bold">{{ $payroll->employee->full_name }}</span>
                    </p>
                </div>

                @if(Auth::user()->role_id == 2)
                    <div class="mb-8 p-6 bg-[#d97706]/5 border border-[#d97706]/20 rounded-2xl text-[10px] text-[#d97706] uppercase tracking-widest font-black">
                        📌 Multi-tier workflow: Edits will be routed to Super Admin for final review.
                    </div>
                @else
                    <div class="mb-8 p-6 bg-green-500/5 border border-green-500/20 rounded-2xl text-[10px] text-green-500 uppercase tracking-widest font-black">
                        ⚡ Super Admin Mode: Direct ledger injection active.
                    </div>
                @endif

                <form action="{{ route('payroll.update', $payroll->transaction_id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="admin_action" value="edit_hours">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Gross Amount</label>
                            <input type="number" step="0.01" name="gross_amount" value="{{ $payroll->gross_amount }}" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Performance Bonus</label>
                            <input type="number" step="0.01" name="bonus_amount" value="{{ $payroll->bonus_amount }}" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Deductions</label>
                            <input type="number" step="0.01" name="deductions" value="{{ $payroll->deductions }}" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition">
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Net Pay (Calculated)</label>
                            <input type="text" value="₱{{ number_format($payroll->net_amount, 2) }}" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm text-gray-600 cursor-not-allowed" readonly>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 mt-6">
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Justification</label>
                        <textarea name="notes" rows="3" placeholder="Provide audit compliance details..." class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition w-full" required></textarea>
                    </div>

                    <div class="mt-10">
                        <button type="submit" class="w-full py-4 bg-[#ff2d75] hover:bg-[#e62668] rounded-xl text-[11px] font-black uppercase tracking-widest transition duration-300">
                            {{ Auth::user()->role_id == 2 ? 'Submit Adjustment Request' : 'Commit Direct Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>