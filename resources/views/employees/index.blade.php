<x-app-layout>
    <div class="py-12 bg-[#121212] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- System Notifications --}}
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

            <div class="emp-container overflow-hidden rounded-xl border border-[#333]">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#222] text-gray-400 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="py-4 px-6">ID</th>
                            <th class="py-4 px-6">Name</th>
                            <th class="py-4 px-6">Position</th>
                            <th class="py-4 px-6">Branch</th>
                            <th class="py-4 px-6">Email Verification</th>
                            <th class="py-4 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#333]">
                        @forelse($employees as $employee)
                            <tr class="hover:bg-[#1a1a1a] transition">
                                <td class="py-4 px-6 font-mono text-[#ff2d75]">
                                    #{{ $employee->employee_id_number }}
                                </td>
                                <td class="py-4 px-6 font-bold">
                                    {{ $employee->full_name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ $employee->position->position_title ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-300">
                                    {{ $employee->branch->branch_name ?? 'N/A' }}
                                </td>
                                
                                {{-- EMAIL VERIFICATION STATUS BAR & ACTION BUTTON --}}
                                <td class="px-6 py-4 text-xs">
                                    @if($employee->user && $employee->user->email_verified_at)
                                        <span class="px-2 py-1 bg-green-500/10 border border-green-500 text-green-500 rounded font-bold uppercase tracking-wider">
                                            Verified
                                        </span>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-1 bg-yellow-500/10 border border-yellow-500 text-yellow-500 rounded font-bold uppercase tracking-wider">
                                                Pending
                                            </span>
                                            <form action="{{ route('employees.verify-email', $employee->employee_id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="px-2 py-0.5 bg-[#ff2d75]/10 border border-[#ff2d75] text-[#ff2d75] hover:bg-[#ff2d75] hover:text-white rounded text-[9px] font-bold uppercase tracking-widest transition duration-200">
                                                    Verify
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>

                                <td class="py-4 px-6 text-right space-x-4">
                                    <a href="{{ route('employees.edit', $employee->employee_id) }}" class="text-[10px] font-bold text-gray-400 hover:text-white uppercase">Edit</a>
                                    
                                    <form action="{{ route('employees.destroy', $employee->employee_id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold text-red-700 hover:text-red-500 uppercase">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-600 uppercase text-xs tracking-widest">
                                    No personnel records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($employees, 'links'))
                <div class="mt-6">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>