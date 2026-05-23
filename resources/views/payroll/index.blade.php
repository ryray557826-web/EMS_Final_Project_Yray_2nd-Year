<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- Header Area Component --}}
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-4xl font-black uppercase tracking-tighter">
                        Payroll <span class="text-[#ff2d75]">Ledgers</span>
                    </h2>
                    <p class="text-gray-400 text-xs mt-2 uppercase tracking-widest">
                        Review compensation transactions, manage pay adjustments, and track compliance.
                    </p>
                </div>
                
                <div class="flex items-center gap-4">
                    <a href="{{ route('payroll.create') }}" 
                       class="bg-[#ff2d75] hover:bg-[#e62668] transition-all px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-[0_0_20px_rgba(255,45,117,0.3)]">
                        + Process New Payroll
                    </a>

                    @if(Auth::user()->role_id == 1)
                        <span class="bg-[#262626] text-white text-[9px] font-black px-4 py-2 rounded-full uppercase tracking-widest animate-pulse">
                            Super Admin Controller Active
                        </span>
                    @endif
                </div>
            </div>

            {{-- Branch Admin Modification Requests Desk --}}
            @if(Auth::user()->role_id == 1)
                <div class="bg-[#161616] p-8 rounded-3xl border border-[#262626]">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-sm font-black text-[#d97706] uppercase tracking-widest">
                            ⚠️ Branch Admin Modification Requests
                        </h3>
                        <span class="bg-[#d97706]/10 text-[#d97706] text-[9px] px-3 py-1 rounded-full font-black uppercase tracking-widest">
                            {{ $transactions->where('status', 'Pending Approval')->count() }} PENDING
                        </span>
                    </div>
                    
                    @if($transactions->where('status', 'Pending Approval')->isEmpty())
                        <p class="text-gray-500 text-xs uppercase tracking-widest">No incoming adjustment requests.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-gray-500 text-[10px] uppercase tracking-widest">
                                        <th class="pb-4 px-2">Reference</th>
                                        <th class="pb-4 px-2">Employee</th>
                                        <th class="pb-4 px-2">Proposed Net</th>
                                        <th class="pb-4 px-2">Reason</th>
                                        <th class="pb-4 px-2 text-center">Review Action</th>
                                    </tr>
                                </thead>
                                <tbody class="text-xs">
                                    @foreach($transactions->where('status', 'Pending Approval') as $req)
                                        <tr class="border-t border-[#262626]">
                                            <td class="py-4 px-2 font-mono text-[#ff2d75]">{{ $req->reference_number }}</td>
                                            <td class="py-4 px-2 font-bold">{{ $req->employee->full_name }}</td>
                                            <td class="py-4 px-2 font-bold">₱{{ number_format($req->net_amount, 2) }}</td>
                                            <td class="py-4 px-2 text-gray-400">{{ $req->notes ?? 'Modification requested.' }}</td>
                                            <td class="py-4 px-2 text-center">
                                                @if($req->is_locked || in_array(strtolower($req->status), ['completed', 'rejected']))
                                                    <span class="text-gray-600 text-[10px] font-black uppercase tracking-widest italic select-none">
                                                        Resolved
                                                    </span>
                                                @else
                                                    <a href="{{ route('payroll.manage', $req->transaction_id) }}" 
                                                       class="bg-[#262626] hover:bg-[#363636] text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-widest border border-[#363636] transition-all">
                                                        Manage Request
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Historical Logs Management Desk --}}
            <div class="bg-[#161616] p-8 rounded-3xl shadow-2xl border border-[#262626]">
                <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-6">Historical Logs</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-gray-500 text-[10px] uppercase tracking-widest">
                                <th class="pb-4 px-2">Ref ID</th>
                                <th class="pb-4 px-2">Employee</th>
                                <th class="pb-4 px-2">Gross Pay</th>
                                <th class="pb-4 px-2">Final Gross</th>
                                <th class="pb-4 px-2">Net Pay</th>
                                <th class="pb-4 px-2">Period</th>
                                <th class="pb-4 px-2">Status</th>
                                <th class="pb-4 px-2 text-center">Operations Desk</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs">
                            @forelse($transactions as $payroll)
                                <tr class="border-t border-[#262626] hover:bg-[#1c1c1c] transition-colors">
                                    <td class="py-4 px-2 font-mono text-gray-400">{{ $payroll->reference_number }}</td>
                                    <td class="py-4 px-2 font-bold">{{ $payroll->employee->full_name }}</td>
                                    <td class="py-4 px-2 text-gray-400">₱{{ number_format($payroll->gross_amount, 2) }}</td>
                                    <td class="py-4 px-2 font-mono {{ $payroll->final_gross_pay !== null ? 'text-green-400 font-bold' : 'text-gray-600 italic' }}">
                                        {{ $payroll->final_gross_pay !== null ? '₱'.number_format($payroll->final_gross_pay, 2) : 'Unfinalized' }}
                                    </td>
                                    <td class="py-4 px-2 text-[#ff2d75] font-black">₱{{ number_format($payroll->net_amount, 2) }}</td>
                                    <td class="py-4 px-2 text-gray-400">
                                        {{ \Carbon\Carbon::parse($payroll->pay_period_start)->format('M d') }} - {{ \Carbon\Carbon::parse($payroll->pay_period_end)->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-2">
                                        @php
                                            $normalizedStatus = strtolower($payroll->status);
                                            
                                            $statusClass = $normalizedStatus === 'pending approval' ? 'bg-yellow-500/10 text-yellow-500' : 
                                                          (($normalizedStatus === 'processed' && $payroll->is_locked) || $normalizedStatus === 'completed' ? 'bg-green-500/10 text-green-500' : 
                                                          ($normalizedStatus === 'processed' ? 'bg-blue-500/10 text-blue-400' :
                                                          ($normalizedStatus === 'rolled back' ? 'bg-orange-500/10 text-orange-400' : 'bg-red-500/10 text-red-500')));
                                        @endphp
                                        <span class="{{ $statusClass }} px-2 py-1 rounded-md font-black uppercase tracking-widest text-[9px]">
                                            {{ $payroll->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-2 text-center">
                                        {{-- FIX: Removed 'processed' from the auto-lock condition array check --}}
                                        @if($payroll->is_locked || in_array(strtolower($payroll->status), ['completed', 'rejected']))
                                            <span class="bg-[#1c1c1c] text-gray-600 border border-[#262626] px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-widest cursor-not-allowed select-none inline-block inline-flex items-center gap-1">
                                                🔒 Locked
                                            </span>
                                        @else
                                            <a href="{{ route('payroll.manage', $payroll->transaction_id) }}" 
                                               class="bg-[#ff2d75] hover:bg-[#e62668] text-white px-4 py-2 rounded-lg font-black uppercase text-[10px] tracking-widest transition-all shadow-md inline-block">
                                                Manage
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-gray-500 uppercase text-[10px] tracking-widest">No payroll transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>