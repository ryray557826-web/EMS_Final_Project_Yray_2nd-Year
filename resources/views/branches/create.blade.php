<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen flex items-center justify-center">
        <div class="w-full max-w-xl bg-[#161616] border border-[#262626] p-10 rounded-3xl shadow-2xl text-white">
            <div class="text-center mb-10">
                <h2 class="text-xl font-black uppercase tracking-widest">Register <span class="text-[#ff2d75]">Branch</span></h2>
                <p class="text-gray-500 text-[9px] mt-2 uppercase tracking-widest">Deploy a new operational sector to the network</p>
            </div>

            <form action="{{ route('branches.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Branch Name</label>
                    <input type="text" name="branch_name" placeholder="e.g. Davao Main Hub" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                </div>

                <div>
                    <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Location Address</label>
                    <input type="text" name="location" placeholder="Street, City, Province" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                </div>

                <div>
                    <label class="text-gray-500 text-[9px] font-black tracking-widest uppercase">Contact Number</label>
                    <input type="text" name="branch_contact" placeholder="+63 000 000 0000" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl mt-1 p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] hover:bg-[#e62668] text-white font-black py-4 rounded-xl transition uppercase tracking-widest text-[11px]">Authorize Deployment</button>
                    <a href="{{ route('branches.index') }}" class="flex-1 bg-[#262626] hover:bg-[#363636] text-white font-black py-4 rounded-xl text-center transition uppercase tracking-widest text-[11px]">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>