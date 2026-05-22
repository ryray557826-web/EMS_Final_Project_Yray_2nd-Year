<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#161616] border border-[#262626] rounded-3xl p-10">
                <h2 class="text-3xl font-black uppercase tracking-tighter mb-8">Edit <span class="text-[#ff2d75]">Branch</span></h2>

                <form action="{{ route('branches.update', $branch->branch_id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-2">Branch Name</label>
                        <input type="text" name="branch_name" value="{{ $branch->branch_name }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl px-4 py-3 text-sm focus:border-[#ff2d75] focus:ring-0">
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-2">Physical Location</label>
                        <input type="text" name="location" value="{{ $branch->location }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl px-4 py-3 text-sm focus:border-[#ff2d75] focus:ring-0">
                    </div>

                    <div>
                        <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-2">Contact Terminal</label>
                        <input type="text" name="branch_contact" value="{{ $branch->branch_contact }}" class="w-full bg-[#0a0a0a] border border-[#262626] rounded-xl px-4 py-3 text-sm focus:border-[#ff2d75] focus:ring-0">
                    </div>

                    <div class="pt-6 flex justify-end gap-4">
                        <a href="{{ route('branches.index') }}" class="px-8 py-4 bg-[#262626] hover:bg-[#333] rounded-xl text-[11px] font-black uppercase tracking-widest transition">Cancel</a>
                        <button type="submit" class="px-8 py-4 bg-[#ff2d75] hover:bg-[#e62668] rounded-xl text-[11px] font-black uppercase tracking-widest transition">Update Branch</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>