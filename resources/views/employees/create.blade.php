<x-app-layout>
    <style>
        /* Force browser autofill to match the dark theme */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: white !important;
            -webkit-box-shadow: 0 0 0px 1000px #222 inset !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        select option { background: #1a1a1a; color: white; }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen flex items-center justify-center">
        <div class="w-full max-w-2xl bg-[#1a1a1a] border border-[#333] p-8 rounded-3xl shadow-xl">
            <div class="text-center mb-8">
                <h2 class="text-xl font-bold text-white uppercase tracking-widest">Register <span class="text-[#ff2d75]">Staff</span></h2>
                <p class="text-gray-500 text-[10px] mt-1 tracking-widest uppercase">Add new personnel to the system</p>
            </div>

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Full Name</label>
                        <input type="text" name="name" class="w-full bg-[#222] border-[#333] text-white rounded-lg mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">System Email</label>
                        <input type="email" name="email" class="w-full bg-[#222] border-[#333] text-white rounded-lg mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]" required>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Position</label>
                        <select name="position_id" class="w-full bg-[#222] border-[#333] text-white rounded-lg mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]">
                            @foreach($positions as $position)
                                <option value="{{ $position->position_id }}">{{ $position->position_title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Branch</label>
                        <select name="branch_id" class="w-full bg-[#222] border-[#333] text-white rounded-lg mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-gray-400 text-[10px] font-bold tracking-widest uppercase">Initial Password</label>
                        <input type="password" name="password" value="Splace2026!" class="w-full bg-[#222] border-[#333] text-white rounded-lg mt-1 focus:border-[#ff2d75] focus:ring-[#ff2d75]">
                    </div>
                </div>

                <div class="mt-8 flex gap-4">
                    <button type="submit" class="flex-1 bg-[#ff2d75] text-white font-bold py-3 rounded-xl hover:bg-[#e62668] transition">CREATE RECORD</button>
                    <a href="{{ route('employees.index') }}" class="flex-1 bg-[#222] border border-[#333] text-gray-400 font-bold py-3 rounded-xl text-center hover:text-white transition uppercase text-xs flex items-center justify-center">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>