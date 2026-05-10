<x-app-layout>
    <style>
        .emp-container { background: #1a1a1a; border: 1px solid #333; border-radius: 1rem; overflow: hidden; }
        .emp-row:hover { background: rgba(255, 45, 117, 0.05); }
        .action-link { font-size: 0.75rem; font-weight: bold; color: #888; transition: 0.3s; }
        .action-link:hover { color: #ff2d75; }
        .badge-branch { background: #222; border: 1px solid #444; color: #ff2d75; font-size: 0.7rem; padding: 2px 8px; border-radius: 4px; }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-500/10 border border-green-500 text-green-500 rounded-xl text-xs font-bold uppercase tracking-widest">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold uppercase tracking-tighter">
                        Staff <span class="text-[#ff2d75]">Directory</span>
                    </h2>
                    <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest">
                        {{ Auth::user()->role_id == 1 ? 'Global Personnel Records' : 'Branch Staff List' }}
                    </p>
                </div>
                <a href="{{ route('employees.create') }}" class="px-6 py-2 bg-[#ff2d75] text-white rounded-lg text-xs font-bold hover:bg-[#e62668] transition shadow-[0_0_15px_rgba(255,45,117,0.3)]">
                    + ADD NEW EMPLOYEE
                </a>
            </div>

            <div class="emp-container">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#222] text-gray-400 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Name</th>
                            <th class="py-4 px-6">Position</th>
                            <th class="py-4 px-6">Branch</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#333]">
                        {{-- Real Data Loop --}}
                        @forelse($employees as $employee)
                            <tr class="emp-row transition">
                                <td class="py-4 px-6 font-mono text-[#ff2d75]">
                                    #{{ $employee->employee_id_number }}
                                </td>
                                <td class="py-4 px-6 font-bold">
                                    {{ $employee->full_name }}
                                </td>
                                <td class="py-4 px-6 text-gray-400">
                                    {{ $employee->position->position_name ?? 'N/A' }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="badge-branch">
                                        {{ $employee->branch->branch_name ?? 'Unassigned' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-4">
                                    <a href="{{ route('employees.edit', $employee->employee_id) }}" class="action-link">EDIT</a>
                                    
                                    <form action="{{ route('employees.destroy', $employee->employee_id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this personnel record?');">
                                        @csrf @method('DELETE')
                                        <button class="action-link text-red-900 hover:text-red-500">DELETE</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-600 uppercase text-xs tracking-widest">
                                    No personnel records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination (If applicable) --}}
            @if(method_exists($employees, 'links'))
                <div class="mt-6">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>