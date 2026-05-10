<x-app-layout>
    <div class="py-12 bg-[#121212] min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-xl bg-[#1a1a1a] border border-[#333] p-10 rounded-3xl shadow-2xl">
            <div class="text-center mb-10">
                <div class="inline-block p-3 bg-[#ff2d75]/10 rounded-full mb-4">
                    <svg class="w-6 h-6 text-[#ff2d75]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-7h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <h2 class="text-xl font-bold text-white uppercase tracking-widest">Register <span class="text-[#ff2d75]">Branch</span></h2>
                <p class="text-gray-500 text-[10px] mt-1 italic tracking-tight">Deploy a new operational sector to the network</p>
            </div>

            <form action="{{ route('branches.store') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Branch Name</label>
                    <input type="text" name="branch_name" placeholder="e.g. Davao Main Hub" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                </div>

                <div>
                    <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Location Address</label>
                    <input type="text" name="location" placeholder="Street, City, Province" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                </div>

                <div>
                    <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Contact Number</label>
                    <input type="text" name="branch_contact" placeholder="+63 000 000 0000" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] text-white font-bold py-4 rounded-2xl hover:bg-[#e62668] transition uppercase tracking-widest text-xs">Authorize Deployment</button>
                    <a href="{{ route('branches.index') }}" class="flex-1 bg-[#222] border border-[#333] text-gray-400 font-bold py-4 rounded-2xl text-center hover:text-white transition uppercase tracking-widest text-xs">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>