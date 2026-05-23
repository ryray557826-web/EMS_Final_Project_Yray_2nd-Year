<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-2xl mx-auto bg-[#161616] p-8 rounded-3xl border border-[#262626] shadow-2xl">
            <h2 class="text-2xl font-black uppercase tracking-tighter mb-6 text-[#ff2d75]">Process New Payroll</h2>
            
            <form action="{{ route('payroll.store') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Employee Select Dropdown --}}
                <div>
                    <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Employee</label>
                    <select name="employee_id" id="employee_select" onchange="fillAttendance()" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm focus:border-[#ff2d75] outline-none text-white">
                        <option value="" disabled selected>-- Select an Employee --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->employee_id }}" data-hours="{{ $emp->attendanceLogs->sum('hours') ?? 0 }}">
                                {{ $emp->full_name }} (Logs: {{ $emp->attendanceLogs->sum('hours') ?? 0 }} hrs)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Hours Worked Inputs --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Hours Worked</label>
                        <input type="number" name="hours_worked" id="hours_input" step="0.5" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm focus:border-[#ff2d75] outline-none" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase block mb-2">&nbsp;</label>
                        <button type="button" onclick="fillAttendance()" class="w-full bg-[#262626] hover:bg-[#333] py-3 rounded-lg text-[10px] font-black uppercase tracking-widest transition-colors border border-[#333]">
                            Sync Log Hours
                        </button>
                    </div>
                </div>

                {{-- Pay Period Dates --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Pay Period Start</label>
                        <input type="text" name="pay_period_start" id="start_date" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm" placeholder="Select Start Date" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block mb-2">Pay Period End</label>
                        <input type="text" name="pay_period_end" id="end_date" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-lg p-3 text-sm" placeholder="Select End Date" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-[#ff2d75] py-4 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#e62668] transition-all shadow-md">
                    Save Transaction
                </button>
            </form>
        </div>
    </div>

    <script>
        // Initialize Calendar Engines
        flatpickr("#start_date", { dateFormat: "Y-m-d", minDate: "2026-01-01", maxDate: "2026-12-31" });
        flatpickr("#end_date", { dateFormat: "Y-m-d", minDate: "2026-01-01", maxDate: "2026-12-31" });

        // Auto-populates and updates fields when changing target selections
        function fillAttendance() {
            const select = document.getElementById('employee_select');
            if(select.selectedIndex <= 0) return;
            
            const hours = select.options[select.selectedIndex].getAttribute('data-hours');
            document.getElementById('hours_input').value = hours ? parseFloat(hours) : 0;
        }
    </script>
</x-app-layout>