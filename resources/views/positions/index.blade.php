<x-app-layout>
    <style>
        .pos-card { background: #1a1a1a; border: 1px solid #333; border-radius: 1.5rem; transition: 0.3s; }
        .pos-card:hover { border-color: #ff2d75; transform: translateY(-5px); }
        .pay-badge { background: rgba(255, 45, 117, 0.1); color: #ff2d75; font-family: 'Courier New', monospace; font-weight: bold; padding: 5px 12px; border-radius: 8px; border: 1px solid rgba(255, 45, 117, 0.3); }
        .level-tag { font-size: 0.65rem; letter-spacing: 1px; color: #888; text-transform: uppercase; font-weight: 800; }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-2xl font-bold uppercase tracking-tighter">
                        Job <span class="text-[#ff2d75]">Structure</span>
                    </h2>
                    <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest">Define Titles and Daily Salary Rates</p>
                </div>
                <a href="{{ route('positions.create') }}" class="px-6 py-2 bg-[#ff2d75] text-white rounded-lg text-xs font-bold hover:bg-[#e62668] transition">
                    + NEW POSITION
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($positions as $position)
                <div class="pos-card p-6 flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="level-tag">{{ $position->job_level }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('positions.edit', $position->position_id) }}" class="text-gray-600 hover:text-[#ff2d75] text-xs font-bold uppercase">Edit</a>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold mb-1">{{ $position->position_title }}</h3>
                        <p class="text-gray-500 text-xs mb-6 uppercase tracking-tighter">ID: POS-{{ str_pad($position->position_id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <div class="pt-4 border-t border-[#222] flex justify-between items-center">
                        <span class="text-xs text-gray-400 uppercase font-bold">Daily Rate</span>
                        <span class="pay-badge">₱ {{ number_format($position->basic_pay, 2) }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>