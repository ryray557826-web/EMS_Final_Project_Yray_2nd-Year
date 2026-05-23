<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-2xl mx-auto bg-[#161616] p-8 rounded-3xl border border-[#262626] shadow-2xl">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-6 text-[#ff2d75]">Process New Payroll</h2>
            
            <form action="{{ route('payroll.store') }}" method="POST" class="space-y-6">
                @csrf
                
              {{-- Employee Selection Dropdown Component Workspace --}}
<div>
    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Select Employee</label>
    <select name="employee_id" id="employee_select" onchange="updateLogReference()" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm focus:border-[#ff2d75] outline-none text-white transition-colors">
        <option value="" disabled selected>-- Select an Employee --</option>
        @foreach($employees as $emp)
            <option value="{{ $emp->employee_id }}" data-hours="{{ $emp->attendanceLogs ? $emp->attendanceLogs->sum('hours') : 0 }}">
                {{ $emp->full_name }} 
                @if(Auth::user()->role_id == 1 && $emp->branch)
                    [{{ $emp->branch->name ?? 'Branch #'.$emp->branch_id }}]
                @endif
            </option>
        @endforeach
    </select>
</div>

                {{-- Hours Input & Log Reference Section --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-[#0a0a0a] p-4 rounded-xl border border-[#262626]">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">Hours to Process</label>
                        <input type="number" 
                               name="hours_worked" 
                               id="hours_input" 
                               step="0.5" 
                               min="0"
                               placeholder="Type or add hours manually..."
                               class="w-full bg-[#161616] border border-[#262626] rounded-lg p-3 text-sm focus:border-[#ff2d75] outline-none text-white font-mono" 
                               required>
                        <span class="text-[9px] text-gray-500 mt-1 block">You can manually input any hour value here.</span>
                    </div>
                    
                    <div class="flex flex-col justify-between">
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-2">System Log Reference</label>
                        <div class="bg-[#161616] border border-[#262626] rounded-lg p-3 flex items-center justify-between h-[46px]">
                            <span class="text-xs text-gray-400 font-mono" id="log_badge">0.00 hrs detected</span>
                            <button type="button" onclick="useLogHours()" class="text-[9px] bg-[#262626] hover:bg-[#333] text-white px-2 py-1 rounded font-black uppercase tracking-wider transition-colors border border-[#333]">
                                Use Logs
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Pay Period Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Pay Period Start</label>
                        <input type="text" name="pay_period_start" id="start_date" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm focus:border-[#ff2d75] outline-none" placeholder="Select Start Date" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Pay Period End</label>
                        <input type="text" name="pay_period_end" id="end_date" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm focus:border-[#ff2d75] outline-none" placeholder="Select End Date" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#ff2d75] py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#e62668] transition-all shadow-md shadow-[#ff2d75]/10">
                    Save & Calculate Gross Pay
                </button>
            </form>
        </div>
    </div>

    <script>
        flatpickr("#start_date", { dateFormat: "Y-m-d", minDate: "2026-01-01", maxDate: "2026-12-31" });
        flatpickr("#end_date", { dateFormat: "Y-m-d", minDate: "2026-01-01", maxDate: "2026-12-31" });

        function updateLogReference() {
            const select = document.getElementById('employee_select');
            if (select.selectedIndex <= 0) return;
            
            const hours = select.options[select.selectedIndex].getAttribute('data-hours') || 0;
            document.getElementById('log_badge').innerText = `${parseFloat(hours).toFixed(2)} hrs detected`;
        }

        function useLogHours() {
            const select = document.getElementById('employee_select');
            if (select.selectedIndex <= 0) {
                alert('Please select an employee first.');
                return;
            }
            const hours = select.options[select.selectedIndex].getAttribute('data-hours') || 0;
            document.getElementById('hours_input').value = parseFloat(hours);
        }
    </script>
</x-app-layout>