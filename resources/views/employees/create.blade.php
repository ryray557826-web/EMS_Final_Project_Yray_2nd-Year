<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen flex items-center justify-center text-white">
        <div class="w-full max-w-2xl bg-[#161616] border border-[#262626] p-10 rounded-3xl shadow-2xl">
            
            <div class="text-center mb-10">
                <h2 class="text-xl font-black uppercase tracking-widest">Register <span class="text-[#ff2d75]">Staff</span></h2>
                <p class="text-gray-500 text-[10px] mt-1 uppercase tracking-widest">Add new personnel to the system</p>
            </div>

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Full Name</label>
                        <input type="text" name="full_name" placeholder="e.g. Juan Dela Cruz" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">System Email</label>
                            <input type="email" name="email" placeholder="username@splacebpo.com" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                        </div>
                        <div>
                            <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Position</label>
                            <select name="position_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                                <option value="" disabled selected>Select Position</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->position_id }}">{{ $position->position_title }} ({{ $position->job_level }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Branch</label>
                            <select name="branch_id" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                                <option value="" disabled selected>Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Initial Password</label>
                            <div class="relative mt-1">
                                <input type="password" name="password" id="staff-password" value="Splace2026!" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm pr-16 focus:border-[#ff2d75] outline-none transition" required>
                                <button type="button" onclick="togglePassword()" id="toggle-btn" class="absolute inset-y-0 right-0 px-4 text-[9px] font-black text-gray-500 hover:text-white uppercase tracking-widest transition">Show</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] hover:bg-[#e62668] text-white font-black py-4 rounded-xl transition tracking-widest text-[11px] uppercase">Create Record</button>
                    <a href="{{ route('employees.index') }}" class="flex-1 bg-[#262626] hover:bg-[#363636] text-white font-black py-4 rounded-xl text-center transition tracking-widest text-[11px] uppercase">Cancel</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        function togglePassword() {
            const p = document.getElementById('staff-password');
            const b = document.getElementById('toggle-btn');
            p.type = (p.type === 'password') ? 'text' : 'password';
            b.textContent = (p.type === 'password') ? 'Show' : 'Hide';
        }
    </script>
</x-app-layout>