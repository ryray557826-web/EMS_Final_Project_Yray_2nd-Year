<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen flex items-center justify-center">
        <div class="w-full max-w-2xl bg-[#161616] border border-[#262626] p-10 rounded-3xl shadow-2xl text-white">
            <div class="text-center mb-10">
                <h2 class="text-xl font-black uppercase tracking-widest">Update <span class="text-[#ff2d75]">Personnel</span></h2>
                <p class="text-gray-500 text-[10px] mt-1 tracking-widest uppercase">Modifying profile matrix fields</p>
            </div>

            <form action="{{ route('employees.update', $employee->employee_id) }}" method="POST">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name', $employee->full_name) }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                    </div>
                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">System Email</label>
                        <input type="email" name="email" value="{{ old('email', $employee->user->email ?? '') }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                    </div>
                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Position Mapping</label>
                        <select name="position_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                            @foreach($positions as $p)
                                <option value="{{ $p->position_id }}" {{ $employee->position_id == $p->position_id ? 'selected' : '' }}>{{ $p->position_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Branch Node</label>
                        <select name="branch_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                            @foreach($branches as $b)
                                <option value="{{ $b->branch_id }}" {{ $employee->branch_id == $b->branch_id ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Authorization</label>
                        <select name="role_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                            <option value="3" {{ ($employee->user->role_id ?? 3) == 3 ? 'selected' : '' }}>Employee</option>
                            <option value="2" {{ ($employee->user->role_id ?? 3) == 2 ? 'selected' : '' }}>Branch Admin</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Update Password (Blank to keep current)</label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition">
                    </div>
                </div>
                <div class="mt-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] hover:bg-[#e62668] text-white font-black py-4 rounded-xl transition tracking-widest text-[11px] uppercase">Save Updates</button>
                    <a href="{{ route('employees.index') }}" class="flex-1 bg-[#262626] hover:bg-[#363636] text-white font-black py-4 rounded-xl text-center transition tracking-widest text-[11px] uppercase">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>