{{-- resources/views/profile/edit.blade.php --}}
<x-main-layout>
    <div class="claude-container">
        
        {{-- Header Section --}}
        <div class="border-b border-[#3a3a3a] header-section">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex items-center gap-4">
                    <h2 class="claude-title text-2xl text-white">
                        Profil Pengguna
                    </h2>
                </div>
            </div>
        </div>

        {{-- Konten Utama: Form Profil --}}
        <div class="max-w-7xl mx-auto py-8 px-6">
            <div class="space-y-6">
                
                {{-- Box Update Info Profil --}}
                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- Box Update Password --}}
                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- Box Hapus Akun --}}
                <div class="bg-[#2a2a2a]/60 backdrop-blur-md border border-[#3a3a3a] rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</x-main-layout>