<x-app-layout>
    <style>
        .bento-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        .bento-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 1.5rem;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .bento-card:hover {
            border-color: #ff2d75;
            transform: translateY(-5px);
        }
        .text-pink { color: #ff2d75; }
        .bg-pink { background-color: #ff2d75; }
        .btn-bento {
            background: #222;
            color: white;
            border: 1px solid #444;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: 0.3s;
        }
        .btn-bento:hover {
            background: #ff2d75;
            border-color: #ff2d75;
        }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-white">
                    WELCOME, <span class="text-pink">{{ strtoupper(Auth::user()->name) }}</span>
                </h2>
                <p class="text-gray-500 uppercase tracking-widest text-xs mt-2">
                    System Role: {{ Auth::user()->role->role_name ?? 'Employee' }} 
                    @if(Auth::user()->employee && Auth::user()->employee->branch)
                        | Branch: {{ Auth::user()->employee->branch->branch_name }}
                    @endif
                </p>
            </div>

            <div class="bento-grid">
                
                <div class="bento-card md:col-span-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-white mb-2">Attendance Terminal</h3>
                            <p class="text-gray-400 text-sm">Real-time clock-in and clock-out management.</p>
                        </div>
                        <span class="bg-pink text-white text-[10px] px-2 py-1 rounded font-bold">LIVE</span>
                    </div>
                    
                    <div class="mt-6 flex gap-4">
                        <a href="{{ route('attendance.index') }}" class="btn-bento font-bold text-pink border-pink">
                            GO TO CLOCK TERMINAL
                        </a>
                    </div>
                </div>

                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                <div class="bento-card">
                    <h3 class="text-xl font-bold text-white mb-2">Staff Directory</h3>
                    <p class="text-gray-400 text-sm">
                        {{ Auth::user()->role_id == 1 ? 'Manage all company personnel.' : 'Manage branch staff.' }}
                    </p>
                    <a href="{{ route('employees.index') }}" class="btn-bento">View Employees</a>
                </div>
                @endif

                @if(Auth::user()->role_id == 1)
                <div class="bento-card">
                    <h3 class="text-xl font-bold text-white mb-2">Job Structure</h3>
                    <p class="text-gray-400 text-sm">Configure job titles and base salary rates.</p>
                    <a href="{{ route('positions.index') }}" class="btn-bento">Manage Positions</a>
                </div>

                <div class="bento-card">
                    <h3 class="text-xl font-bold text-white mb-2">Branch Network</h3>
                    <p class="text-gray-400 text-sm">Assign Admins and manage office locations.</p>
                    <a href="{{ route('branches.index') }}" class="btn-bento">Edit Branches</a>
                </div>

                <div class="bento-card">
                    <h3 class="text-xl font-bold text-white mb-2">System Audit</h3>
                    <p class="text-gray-400 text-sm">Review logs and security modifications.</p>
                    <a href="{{ route('audit.index') }}" class="btn-bento">View Trails</a>
                </div>
                @endif

                @if(Auth::user()->role_id == 3)
                <div class="bento-card">
                    <h3 class="text-xl font-bold text-white mb-2">My History</h3>
                    <p class="text-gray-400 text-sm">Review your past attendance logs (Read-only).</p>
                    <a href="{{ route('attendance.index') }}" class="btn-bento">View My Logs</a>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>