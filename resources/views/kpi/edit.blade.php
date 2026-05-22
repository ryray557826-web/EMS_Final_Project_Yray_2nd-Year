<x-app-layout>
    <div class="py-12 bg-[#0a0a0a] min-h-screen text-white">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-8">
                <a href="{{ route('kpi.index') }}" class="text-[10px] text-gray-500 hover:text-[#ff2d75] uppercase tracking-widest font-black transition">
                    ← Back to Scorecards
                </a>
            </div>

            <div class="bg-[#161616] p-10 rounded-3xl">
                <div class="mb-10">
                    <span class="text-[9px] uppercase tracking-widest text-[#ff2d75] font-black font-mono">Entry ID: #{{ $kpi->kpi_id }}</span>
                    <h2 class="text-3xl font-black uppercase tracking-tighter mt-1">Modify Performance Matrix</h2>
                    <p class="text-xs text-gray-500 mt-2 uppercase tracking-widest">Updating metrics for: <span class="text-white font-bold">{{ $kpi->employee->full_name }}</span></p>
                </div>

                <form action="{{ route('kpi.update', $kpi->kpi_id) }}" method="POST">
                    @csrf @method('PATCH')

                    <div class="flex flex-col gap-6">
                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Evaluation Score (0 - 100%)</label>
                            <input type="number" min="0" max="100" name="evaluation_score" value="{{ $kpi->evaluation_score }}" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-widest">Justification & Remarks</label>
                            <textarea name="remarks" rows="5" class="bg-[#0a0a0a] border border-[#262626] rounded-xl p-4 text-sm focus:border-[#ff2d75] outline-none transition" required>{{ $kpi->remarks }}</textarea>
                        </div>
                    </div>

                    <div class="mt-10">
                        <button type="submit" class="w-full py-4 bg-[#ff2d75] hover:bg-[#e62668] rounded-xl text-[11px] font-black uppercase tracking-widest transition">
                            Apply Matrix Synchronization
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>