<x-app-layout>
    <style>
        .terminal-card { background: #1a1a1a; border: 1px solid #333; border-radius: 1.5rem; }
        .btn-clock { 
            width: 100%; 
            padding: 1.5rem; 
            border-radius: 1rem; 
            font-weight: 800; 
            font-size: 1.25rem; 
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .btn-in { background: #ff2d75; color: white; border: none; box-shadow: 0 0 20px rgba(255, 45, 117, 0.3); }
        .btn-in:hover { background: #e62668; transform: scale(1.02); }
        .btn-out { background: transparent; border: 2px solid #ff2d75; color: #ff2d75; }
        .btn-out:hover { background: rgba(255, 45, 117, 0.1); transform: scale(1.02); }
        
        .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 8px; }
        .dot-online { background: #00ff88; box-shadow: 0 0 10px #00ff88; }
        
        .nav-pill { 
            background: #222; 
            color: #888; 
            padding: 8px 16px; 
            border-radius: 8px; 
            font-size: 0.75rem; 
            font-weight: bold; 
            text-decoration: none;
            border: 1px solid #333;
        }
        .nav-pill:hover { border-color: #ff2d75; color: #ff2d75; }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alerts for Feedback --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500 text-green-500 rounded-xl text-xs font-bold uppercase tracking-widest">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-500/10 border border-red-500 text-red-500 rounded-xl text-xs font-bold uppercase tracking-widest">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex justify-between items-center mb-8">
                <div>
                    <h2 class="text-2xl font-bold uppercase tracking-tighter">
                        Attendance <span class="text-[#ff2d75]">Terminal</span>
                    </h2>
                    <p class="text-gray-500 text-xs mt-1">
                        <span class="status-dot dot-online"></span> SYSTEM OPERATIONAL | {{ now()->timezone('Asia/Manila')->format('F d, Y') }}
                    </p>
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('dashboard') }}" class="nav-pill">DASHBOARD</a>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                        <a href="{{ route('employees.index') }}" class="nav-pill">EMPLOYEES</a>
                    @endif
                    @if(Auth::user()->role_id == 1)
                        <a href="{{ route('audit.index') }}" class="nav-pill">AUDIT TRAIL</a>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-1">
                    <div class="terminal-card p-8 text-center">
                        <h3 class="text-gray-400 font-bold text-xs uppercase tracking-widest mb-6">Action Required</h3>
                        
                        <div class="text-4xl font-mono font-bold mb-8 text-white" id="liveClock">
                            00:00:00
                        </div>

                        <form action="{{ route('attendance.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <button name="action" value="clock_in" class="btn-clock btn-in">
                                Clock In
                            </button>
                            <button name="action" value="clock_out" class="btn-clock btn-out">
                                Clock Out
                            </button>
                        </form>

                        <p class="mt-6 text-[10px] text-gray-600 uppercase">
                            Location Verified: {{ Auth::user()->employee->branch->branch_name ?? 'Branch HQ' }}
                        </p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="terminal-card p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-sm uppercase tracking-widest text-gray-400">
                                {{ Auth::user()->role_id == 3 ? 'My Recent Activity' : 'Branch Activity' }}
                            </h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="text-gray-500 border-b border-[#333]">
                                    <tr>
                                        <th class="pb-3 px-2">EMPLOYEE</th>
                                        <th class="pb-3">TIME</th>
                                        <th class="pb-3">TYPE</th>
                                        <th class="pb-3 text-right">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#222]">
                                    @forelse($logs as $log)
                                        <tr>
                                            <td class="py-4 px-2">
                                                <span class="font-bold block text-pink-500">{{ $log->employee->full_name ?? 'System User' }}</span>
                                                <span class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ $log->employee->employee_id_number ?? 'EMP-ID' }}</span>
                                            </td>
                                            <td class="py-4 text-gray-400 font-mono">
                                                {{-- Critical Fix: Converts UTC database time to Asia/Manila --}}
                                                @php
                                                    $displayTime = $log->time_out ?? $log->time_in;
                                                @endphp
                                                {{ \Carbon\Carbon::parse($displayTime)->timezone('Asia/Manila')->format('h:i:A') }}
                                            </td>
                                            <td class="py-4">
                                                @if($log->time_out)
                                                    <span class="text-gray-500 font-bold uppercase text-xs">OUT</span>
                                                @else
                                                    <span class="text-pink-500 font-bold uppercase text-xs">IN</span>
                                                @endif
                                            </td>
                                            <td class="py-4 text-right">
                                                <span class="text-green-500 px-2 py-1 bg-green-500/10 rounded text-[10px] font-bold uppercase border border-green-500/20">
                                                    {{ $log->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-10 text-center text-gray-600 uppercase text-xs tracking-widest">
                                                No activity logs found for today.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            // Formats JS clock to match 12-hour AM/PM format
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
            document.getElementById('liveClock').innerText = now.toLocaleTimeString('en-US', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</x-app-layout>