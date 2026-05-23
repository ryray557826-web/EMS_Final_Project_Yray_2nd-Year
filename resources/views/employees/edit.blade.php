<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen flex items-center justify-center text-white">
        <div class="w-full max-w-2xl bg-[#161616] border border-[#262626] p-10 rounded-3xl shadow-2xl">
            
            <div class="text-center mb-10">
                <h2 class="text-xl font-black uppercase tracking-widest">Edit <span class="text-[#ff2d75]">Staff</span></h2>
                <p class="text-gray-500 text-[10px] mt-1 uppercase tracking-widest">Update personnel details</p>
            </div>

            <form action="{{ route('employees.update', $employee->employee_id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $employee->full_name) }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">System Email</label>
                            <input type="email" name="email" value="{{ old('email', $employee->user->email) }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                        </div>
                        <div>
                            <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Position</label>
                            <select name="position_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                                @foreach($positions as $position)
                                    <option value="{{ $position->position_id }}" {{ $employee->position_id == $position->position_id ? 'selected' : '' }}>
                                        {{ $position->position_title }} ({{ $position->job_level }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Branch</label>
                        <select name="branch_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}" {{ $employee->branch_id == $branch->branch_id ? 'selected' : '' }}>
                                    {{ $branch->branch_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] hover:bg-[#e62668] text-white font-black py-4 rounded-xl transition tracking-widest text-[11px] uppercase">Update Record</button>
                    <a href="{{ route('employees.index') }}" class="flex-1 bg-[#262626] hover:bg-[#363636] text-white font-black py-4 rounded-xl text-center transition tracking-widest text-[11px] uppercase">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>