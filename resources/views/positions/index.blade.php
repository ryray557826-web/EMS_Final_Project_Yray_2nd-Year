<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-end mb-10">
                <div>
                    <h2 class="text-4xl font-black uppercase tracking-tighter">
                        Job <span class="text-[#ff2d75]">Structure</span>
                    </h2>
                    <p class="text-gray-400 text-xs mt-2 uppercase tracking-widest">Define Titles and Hourly Salary Rates</p>
                </div>
                <a href="{{ route('positions.create') }}" class="px-6 py-3 bg-[#ff2d75] text-white rounded-xl text-[10px] font-black hover:bg-[#e62668] transition duration-300 uppercase tracking-widest">
                    + New Position
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($positions as $position)
                <div class="bg-[#161616] border border-[#262626] p-6 rounded-3xl flex flex-col justify-between transition-all duration-300 hover:border-[#ff2d75] hover:shadow-2xl">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">
                                {{ $position->job_level }}
                            </span>
                            <a href="{{ route('positions.edit', $position->position_id) }}" class="text-gray-600 hover:text-[#ff2d75] text-[10px] font-bold uppercase tracking-widest transition">
                                Edit
                            </a>
                        </div>
                        <h3 class="text-xl font-black uppercase tracking-tighter">{{ $position->position_title }}</h3>
                        <p class="text-gray-600 text-[10px] mb-6 uppercase tracking-widest">
                            ID: POS-{{ str_pad($position->position_id, 3, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-[#262626] flex justify-between items-center">
                        <span class="text-[10px] text-gray-400 uppercase font-black tracking-widest">Hourly Rate</span>
                       <span class="bg-[#ff2d75]/10 text-[#ff2d75] font-mono font-bold px-3 py-1 rounded-lg text-xs">
                        ₱ {{ number_format($position->hourly_rate, 2) }}
                    </span>
                    </div>
                </div>
                @endforeach
            </div>
            
        </div>
    </div>
</x-app-layout>