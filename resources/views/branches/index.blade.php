<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-4xl font-black uppercase tracking-tighter">Branch <span class="text-[#ff2d75]">Network</span></h2>
                    <p class="text-gray-500 text-xs mt-2 uppercase tracking-widest">Manage office locations and deployment configurations.</p>
                </div>
                <a href="{{ route('branches.create') }}" class="px-8 py-4 bg-[#ff2d75] hover:bg-[#e62668] rounded-xl text-[11px] font-black uppercase tracking-widest transition">
                    + Register Branch
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($branches as $branch)
                <div class="bg-[#161616] border border-[#262626] rounded-3xl p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="font-black text-lg uppercase tracking-tight">{{ $branch->branch_name }}</h3>
                            <a href="{{ route('branches.edit', $branch->branch_id) }}" class="text-[9px] font-black text-gray-500 hover:text-[#ff2d75] uppercase tracking-widest">Edit</a>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-1">Physical Location</label>
                                <p class="text-sm text-gray-300">{{ $branch->location }}</p>
                            </div>
                            <div>
                                <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest block mb-1">Contact Terminal</label>
                                <p class="text-sm font-mono text-gray-400">{{ $branch->branch_contact }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-[#262626] flex items-center justify-between">
                        <span class="text-[9px] text-gray-500 uppercase font-black tracking-widest">Status</span>
                        <span class="px-3 py-1 bg-[#262626] text-[#ff2d75] text-[9px] font-black rounded-lg uppercase tracking-widest">Active</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>