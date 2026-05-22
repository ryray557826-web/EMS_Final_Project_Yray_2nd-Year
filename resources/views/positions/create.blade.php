<x-app-layout>
    <div class="py-12 bg-[#121212] min-h-screen flex items-center justify-center">
        <div class="w-full max-w-lg bg-[#1a1a1a] border border-[#333] p-10 rounded-3xl shadow-2xl">
            <div class="text-center mb-10">
                <h2 class="text-xl font-bold text-white uppercase tracking-widest">Config <span class="text-[#ff2d75]">Position</span></h2>
                <p class="text-gray-500 text-[10px] mt-1">Set job classification, hourly compensation, and system access</p>
            </div>

            {{-- Works for both Create and Edit routes --}}
            <form action="{{ isset($position) ? route('positions.update', $position->position_id) : route('positions.store') }}" method="POST">
                @csrf
                @if(isset($position)) @method('PUT') @endif

                <div class="space-y-6">
                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Position Title</label>
                        <input type="text" name="position_title" value="{{ old('position_title', $position->position_title ?? '') }}" placeholder="e.g. Senior Agent" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Job Level</label>
                        <select name="job_level" class="w-full bg-[#222] border-[#333] text-white rounded-xl mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]">
                            @foreach(['Entry Level', 'Junior', 'Senior', 'Managerial', 'Executive'] as $level)
                                <option value="{{ $level }}" {{ (old('job_level', $position->job_level ?? '') == $level) ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- TRANSFERRED FROM REGISTER STAFF SECTION --}}
                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Granted System Role Assignment</label>
                        <p class="text-gray-500 text-[9px] mb-1 lowercase italic">Staff hired into this position will inherit these system permissions automatically.</p>
                        <select name="role_id" class="w-full bg-[#222] border-[#333] text-white rounded-xl focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                            <option value="3" {{ (old('role_id', $position->role_id ?? '') == 3) ? 'selected' : '' }}>Employee (Default)</option>
                            <option value="2" {{ (old('role_id', $position->role_id ?? '') == 2) ? 'selected' : '' }}>Branch Admin</option>
                            <option value="1" {{ (old('role_id', $position->role_id ?? '') == 1) ? 'selected' : '' }}>Super Admin</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Hourly Pay Rate (PHP)</label>
                        <div class="relative mt-1">
                            <span class="absolute left-4 top-3 text-gray-500">₱</span>
                            <input type="number" step="0.01" name="hourly_rate" value="{{ old('hourly_rate', $position->hourly_rate ?? '') }}" placeholder="0.00" class="w-full bg-[#222] border-[#333] text-white rounded-xl pl-8 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
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