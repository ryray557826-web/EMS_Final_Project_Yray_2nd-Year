<x-app-layout>
    <style>
        .profile-card { background: #1a1a1a; border: 1px solid #333; border-radius: 1.5rem; padding: 2rem; }
        .input-dark { background: #222 !important; border: 1px solid #333 !important; color: white !important; border-radius: 0.75rem !important; }
        .input-dark:focus { border-color: #ff2d75 !important; ring-color: #ff2d75 !important; }
        .section-title { font-size: 0.75rem; font-weight: 800; letter-spacing: 2px; color: #ff2d75; text-transform: uppercase; margin-bottom: 1.5rem; display: block; }
    </style>

    <div class="py-12 bg-[#121212] min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-white uppercase tracking-tighter">
                    Account <span class="text-[#ff2d75]">Settings</span>
                </h2>
                <p class="text-gray-500 text-xs mt-1 uppercase tracking-widest">ID: USER-{{ str_pad(Auth::user()->user_id, 4, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <div class="profile-card">
                    <span class="section-title">Profile Information</span>
                    @include('profile.partials.update-profile-information-form')
                </div>

                <div class="profile-card">
                    <span class="section-title">Security Protocol</span>
                    @include('profile.partials.update-password-form')
                </div>

                <div class="md:col-span-2 profile-card border-red-900/30">
                    <span class="section-title text-red-600">Danger Zone</span>
                    @include('profile.partials.delete-user-form')
                </div>

            </div>
        </div>
    </div>
</x-app-layout>