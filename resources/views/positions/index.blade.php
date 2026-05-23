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
                <div class="bg-[#161616] border {{ $position->is_active ? 'border-[#262626]' : 'border-red-900/30 base-dimmed-state' }} p-6 rounded-3xl flex flex-col justify-between transition-all duration-300 hover:border-[#ff2d75] hover:shadow-2xl opacity-{{ $position->is_active ? '100' : '60' }}">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <span class="text-[10px] font-black uppercase tracking-widest {{ $position->is_active ? 'text-gray-500' : 'text-red-400' }}">
                                {{ $position->job_level }} {{ !$position->is_active ? '(Inactive)' : '' }}
                            </span>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('positions.edit', $position->position_id) }}" class="text-gray-600 hover:text-[#ff2d75] text-[10px] font-bold uppercase tracking-widest transition">
                                    Edit
                                </a>
                                <form action="{{ route('positions.destroy', $position->position_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently purge this record layout framework?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-600 hover:text-red-500 text-[10px] font-bold uppercase tracking-widest transition">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        <h3 class="text-xl font-black uppercase tracking-tighter text-{{ $position->is_active ? 'white' : 'gray-500' }}">{{ $position->position_title }}</h3>
                        <p class="text-gray-600 text-[10px] mb-6 uppercase tracking-widest">
                            ID: POS-{{ str_pad($position->position_id, 3, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-[#262626] flex justify-between items-center">
                        {{-- Status State Engine Toggle Form Button --}}
                        <form action="{{ route('positions.toggle', $position->position_id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all border {{ $position->is_active ? 'bg-green-500/10 text-green-400 border-green-500/20 hover:bg-green-500/20' : 'bg-zinc-800 text-zinc-500 border-zinc-700/50 hover:bg-zinc-700' }}">
                                {{ $position->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                        
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