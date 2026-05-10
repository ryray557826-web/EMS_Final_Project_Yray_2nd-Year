<x-app-layout>
    <style>
        .branch-card { background: #1a1a1a; border: 1px solid #333; border-radius: 1.25rem; overflow: hidden; }
        .branch-header { background: #222; border-bottom: 1px solid #333; padding: 1rem 1.5rem; }
        .contact-info { font-family: 'Courier New', monospace; color: #888; font-size: 0.75rem; }
        .admin-tag { background: rgba(255, 45, 117, 0.1); color: #ff2d75; font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 4px; border: 1px solid rgba(255, 45, 117, 0.2); }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold uppercase tracking-tighter">
                        Branch <span class="text-[#ff2d75]">Network</span>
                    </h2>
                    <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest">Manage Office Locations & Assignments</p>
                </div>
                <a href="{{ route('branches.create') }}" class="px-6 py-2 bg-[#ff2d75] text-white rounded-lg text-xs font-bold hover:bg-[#e62668] transition shadow-[0_0_15px_rgba(255,45,117,0.2)]">
                    + REGISTER BRANCH
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($branches as $branch)
                <div class="branch-card">
                    <div class="branch-header flex justify-between items-center">
                        <h3 class="font-bold text-lg text-white uppercase tracking-tight">{{ $branch->branch_name }}</h3>
                        <a href="{{ route('branches.edit', $branch->branch_id) }}" class="text-[10px] font-bold text-gray-500 hover:text-[#ff2d75] uppercase">Edit Config</a>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <label class="text-[9px] font-bold text-gray-600 uppercase tracking-widest block mb-1">Physical Location</label>
                                <p class="text-sm text-gray-300">{{ $branch->location }}</p>
                            </div>
                            
                            <div class="flex justify-between">
                                <div>
                                    <label class="text-[9px] font-bold text-gray-600 uppercase tracking-widest block mb-1">Contact Terminal</label>
                                    <p class="contact-info">{{ $branch->branch_contact }}</p>
                                </div>
                                <div class="text-right">
                                    <label class="text-[9px] font-bold text-gray-600 uppercase tracking-widest block mb-1">Branch Status</label>
                                    <span class="admin-tag uppercase">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-[#222]/50 px-6 py-3 flex items-center justify-between">
                        <span class="text-[10px] text-gray-500 uppercase font-bold">Assigned Admin:</span>
                        <span class="text-xs font-bold text-white italic">Yray, Dwayne (Sample)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>