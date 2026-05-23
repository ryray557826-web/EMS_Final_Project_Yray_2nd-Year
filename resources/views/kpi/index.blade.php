<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-4xl font-black uppercase tracking-tighter">Performance <span class="text-[#ff2d75]">Scorecards</span></h2>
                    <p class="text-gray-500 text-xs mt-2 uppercase tracking-widest">Evaluate staff metrics and manage grading histories.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- New Entry Form Workspace --}}
                <div class="bg-[#161616] p-8 rounded-3xl h-fit">
                    <h3 class="text-sm font-black uppercase tracking-widest text-white mb-6">New Performance Entry</h3>
                    <form action="{{ route('kpi.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Target Staff</label>
                            <select name="employee_id" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                                <option value="" disabled selected>Select personnel...</option>
                                @php
                                    // Security Layer: Filter selection box options by assigned management branch context
                                    $allowedStaff = Auth::user()->role_id == 1 
                                        ? \App\Models\Employee::orderBy('full_name')->get() 
                                        : \App\Models\Employee::where('branch_id', Auth::user()->branch_id)->orderBy('full_name')->get();
                                @endphp
                                @foreach($allowedStaff as $emp)
                                    <option value="{{ $emp->employee_id }}">
                                        {{ $emp->full_name }} @if(Auth::user()->role_id == 1 && $emp->branch) ({{ $emp->branch->name ?? 'Branch #'.$emp->branch_id }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Evaluation Score (0-100)</label>
                            <input type="number" min="0" max="100" name="evaluation_score" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition" placeholder="e.g. 92" required>
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Remarks</label>
                            <textarea name="remarks" rows="4" placeholder="Performance details..." class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition"></textarea>
                        </div>
                        <button type="submit" class="w-full py-4 bg-[#ff2d75] hover:bg-[#e62668] rounded-xl text-[11px] font-black uppercase tracking-widest transition">
                            Log Matrix Entry
                        </button>
                    </form>
                </div>

                {{-- Historical Data Workspace --}}
                <div class="bg-[#161616] p-8 rounded-3xl lg:col-span-2">
                    <h3 class="text-sm font-black uppercase tracking-widest text-white mb-6">Historical Ratings</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] text-gray-500 uppercase tracking-widest">
                                    <th class="pb-4 px-2">Personnel</th>
                                    @if(Auth::user()->role_id == 1)
                                        <th class="pb-4 px-2">Branch</th>
                                    @endif
                                    <th class="pb-4 px-2">KPI Score</th>
                                    <th class="pb-4 px-2">Summary</th>
                                    <th class="pb-4 px-2 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-xs">
                                @foreach($scorecards as $kpi)
                                    {{-- Multi-Branch Filtering Assert Logic Check --}}
                                    @if(Auth::user()->role_id == 1 || (isset($kpi->employee) && $kpi->employee->branch_id == Auth::user()->branch_id))
                                        <tr class="border-t border-[#262626]">
                                            <td class="py-4 px-2 font-bold">{{ $kpi->employee->full_name ?? 'System User' }}</td>
                                            @if(Auth::user()->role_id == 1)
                                                <td class="py-4 px-2 text-blue-400 font-mono text-[10px]">
                                                    {{ $kpi->employee->branch->name ?? 'Branch #'.$kpi->employee->branch_id }}
                                                </td>
                                            @endif
                                            <td class="py-4 px-2 font-mono text-[#ff2d75] font-black">{{ number_format($kpi->evaluation_score, 0) }}%</td>
                                            <td class="py-4 px-2 text-gray-400">{{ $kpi->remarks ?? 'N/A' }}</td>
                                            <td class="py-4 px-2">
                                                <div class="flex justify-center gap-2">
                                                    <a href="{{ route('kpi.edit', $kpi->kpi_id) }}" class="bg-[#262626] hover:bg-[#363636] px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest transition">Edit</a>
                                                    <form action="{{ route('kpi.destroy', $kpi->kpi_id) }}" method="POST" onsubmit="return confirm('Remove entry?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="bg-[#262626] text-red-500 hover:bg-red-600 hover:text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest transition">
                                                            Remove
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>