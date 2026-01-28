{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<section>
    <header>
        <h2 class="claude-title text-xl text-white">
            Perbarui Password
        </h2>

        <p class="mt-2 text-sm text-gray-400">
            Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="filter-label">Password Saat Ini</label>
            <input type="password" name="current_password" id="current_password" 
                   class="filter-input mt-1 block w-full" autocomplete="current-password" />
            @error('current_password', 'updatePassword')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="filter-label">Password Baru</label>
            <input type="password" name="password" id="password" 
                   class="filter-input mt-1 block w-full" autocomplete="new-password" />
            @error('password', 'updatePassword')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="filter-label">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation" 
                   class="filter-input mt-1 block w-full" autocomplete="new-password" />
            @error('password_confirmation', 'updatePassword')
                <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="filter-btn filter-btn-primary">Simpan</button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-400"
                >Tersimpan.</p>
            @endif
        </div>
    </form>
</section>