<x-app-layout>
    <div class="py-12 bg-[#121212] min-h-screen flex items-center justify-center">
        <div class="w-full max-w-lg bg-[#1a1a1a] border border-[#333] p-10 rounded-3xl shadow-2xl">
            <div class="text-center mb-10">
                <h2 class="text-xl font-bold text-white uppercase tracking-widest">Config <span class="text-[#ff2d75]">Position</span></h2>
                <p class="text-gray-500 text-[10px] mt-1">Set job classification and base compensation</p>
            </div>

            <form action="{{ route('positions.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Position Title</label>
                        <input type="text" name="position_title" placeholder="e.g. Senior Agent" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Job Level</label>
                        <select name="job_level" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]">
                            <option value="Entry">Entry Level</option>
                            <option value="Junior">Junior</option>
                            <option value="Senior">Senior</option>
                            <option value="Managerial">Managerial</option>
                            <option value="Executive">Executive</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Basic Daily Pay (PHP)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3 text-gray-500">₱</span>
                            <input type="number" step="0.01" name="basic_pay" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 pl-8 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                        </div>
                    </div>
                </div>

                <div class="mt-10 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] text-white font-bold py-4 rounded-2xl hover:bg-[#e62668] transition">SAVE POSITION</button>
                    <a href="{{ route('positions.index') }}" class="flex-1 bg-[#222] border border-[#333] text-gray-400 font-bold py-4 rounded-2xl text-center hover:text-white transition">CANCEL</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>