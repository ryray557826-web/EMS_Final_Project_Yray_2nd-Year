<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            
            {{-- User Welcome Banner --}}
            <div class="mb-12">
                <h2 class="text-4xl font-black text-white uppercase tracking-tighter">
                    WELCOME, <span class="text-[#ff2d75]"> {{ strtoupper(Auth::user()->name) }}</span>
                </h2>
                <div class="flex items-center gap-3 mt-4">
                    <span class="px-3 py-1 bg-[#161616] border border-[#262626] text-[10px] font-bold uppercase tracking-widest text-white rounded-lg">
                        {{ Auth::user()->role->role_name ?? 'Employee' }}
                    </span>
                    @if(Auth::user()->employee && Auth::user()->employee->branch)
                        <span class="text-gray-400 text-xs italic tracking-wide">
                            {{ Auth::user()->employee->branch->branch_name }}
                        </span>
                    @endif
                </div>
            </div>

            {{-- Main Dashboard Features Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                {{-- Shared Terminal Block (All Users) --}}
                <div class="lg:col-span-2 bg-[#161616] border border-[#262626] p-10 rounded-3xl shadow-2xl border-l-4 border-l-[#ff2d75]">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Attendance Terminal</h3>
                            <p class="text-gray-400 text-xs mt-2 uppercase tracking-widest">Clock-in/out and manage your daily logs.</p>
                        </div>
                        <span class="bg-[#ff2d75]/10 text-[#ff2d75] text-[9px] px-3 py-1 rounded-full font-black uppercase tracking-widest animate-pulse">Live Now</span>
                    </div>
                    <a href="{{ route('attendance.index') }}" class="mt-8 inline-block bg-[#ff2d75] hover:bg-[#e62668] text-white font-bold py-3 px-6 rounded-xl transition duration-300 uppercase tracking-widest text-xs">
                        Open Terminal
                    </a>
                </div>

                {{-- Admin & Branch Manager Modules --}}
                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Staff Directory</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Manage personnel and access.</p>
                        <a href="{{ route('employees.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">View Employees</a>
                    </div>

                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Payroll Engine</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Execute pay runs and sync.</p>
                        <a href="{{ route('payroll.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">Process Payroll</a>
                    </div>

                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">KPI Scorecards</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Review performance metrics.</p>
                        <a href="{{ route('kpi.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">View Metrics</a>
                    </div>
                @endif

                {{-- Super Admin Restricted Infrastructure --}}
                @if(Auth::user()->role_id == 1)
                    {{-- NEW: Salary Profiles Component Box --}}
                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl border-t-2 border-t-[#ff2d75]">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Salary Profiles</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Manage base rates, allowances, and structures.</p>
                        <a href="{{ route('salary-profiles.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">Manage Profiles</a>
                    </div>

                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Job Structure</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Positions and pay scales.</p>
                        <a href="{{ route('positions.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">Manage Positions</a>
                    </div>

                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">Branch Network</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Office location settings.</p>
                        <a href="{{ route('branches.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">Edit Branches</a>
                    </div>

                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">System Audit</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Security and activity logs.</p>
                        <a href="{{ route('audit.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">View Audit Logs</a>
                    </div>
                @endif

                {{-- Standard Employee History Views --}}
                @if(Auth::user()->role_id == 3)
                    <div class="bg-[#161616] border border-[#262626] p-8 rounded-3xl shadow-2xl">
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-2">My History</h3>
                        <p class="text-gray-400 text-[10px] uppercase tracking-widest mb-6">Verified attendance logs.</p>
                        <a href="{{ route('attendance.index') }}" class="text-[#ff2d75] font-bold text-[10px] uppercase tracking-widest underline underline-offset-4">View My Logs</a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>