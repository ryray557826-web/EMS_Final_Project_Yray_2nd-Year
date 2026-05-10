<x-app-layout>
    <style>
        .audit-container { background: #1a1a1a; border: 1px solid #333; border-radius: 1rem; overflow: hidden; }
        .audit-row:hover { background: rgba(255, 45, 117, 0.05); }
        .badge-action { 
            font-size: 0.65rem; 
            font-weight: 800; 
            padding: 4px 8px; 
            border-radius: 4px; 
            text-transform: uppercase;
        }
        /* Action Colors */
        .bg-create { background: #00ff88; color: #000; }
        .bg-update { background: #ff2d75; color: #fff; }
        .bg-delete { background: #ff4444; color: #fff; }
        .bg-default { background: #333; color: #eee; }
        
        .json-box { 
            font-family: 'Courier New', monospace; 
            background: #121212; 
            border: 1px solid #222; 
            padding: 10px; 
            border-radius: 8px; 
            font-size: 0.75rem;
            color: #888;
        }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen text-white">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h2 class="text-2xl font-bold uppercase tracking-tighter">
                        System <span class="text-[#ff2d75]">Audit Trails</span>
                    </h2>
                    <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest">Master Security Log | Admin Access Only</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-[#222] border border-[#333] rounded-lg text-[10px] font-bold hover:border-[#ff2d75] transition">BACK TO DASHBOARD</a>
                </div>
            </div>

            <div class="audit-container">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#222] text-gray-400 uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="py-4 px-6">Timestamp</th>
                            <th class="py-4 px-6">User</th>
                            <th class="py-4 px-6">Action</th>
                            <th class="py-4 px-6">Module</th>
                            <th class="py-4 px-6">Description</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#333]">
                        @forelse($logs as $log)
                            <tr class="audit-row transition">
                                <td class="py-4 px-6 text-gray-500 font-mono text-xs">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="font-bold text-white">{{ $log->user->name ?? 'System' }}</span>
                                    <div class="text-[10px] text-gray-500 italic">
                                        {{-- Assuming User model has a role relationship or role_id check --}}
                                        @if($log->user && $log->user->role_id == 1) Super Admin @else Staff @endif
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    {{-- Dynamic Badge Coloring --}}
                                    <span class="badge-action 
                                        @if($log->action == 'CREATE') bg-create 
                                        @elseif($log->action == 'UPDATE') bg-update 
                                        @elseif($log->action == 'DELETE') bg-delete 
                                        @else bg-default @endif">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-400 font-bold text-xs uppercase tracking-tight">
                                    {{ $log->module }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="json-box">
                                        {{ $log->description }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-gray-600 uppercase tracking-widest text-xs">
                                    No records found in the security database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-6">
                {{ $logs->links() }}
            </div>

            <div class="mt-6 text-gray-600 text-[10px] text-center uppercase tracking-widest">
                Showing System Interactions | {{ now()->toDayDateTimeString() }}
            </div>
        </div>
    </div>
</x-app-layout>