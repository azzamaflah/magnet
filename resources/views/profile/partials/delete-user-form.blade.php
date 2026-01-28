{{-- resources/views/profile/partials/delete-user-form.blade.php --}}
<section class="space-y-6">
    <header>
        <h2 class="claude-title text-xl text-white">
            Hapus Akun
        </h2>

        <p class="mt-2 text-sm text-gray-400">
            Setelah akun Anda dihapus, semua data dan sumber dayanya akan dihapus permanen. Sebelum menghapus akun, harap unduh data apa pun yang ingin Anda simpan.
        </p>
    </header>

    {{-- Ganti <x-danger-button> dengan <button> yang di-style --}}
    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="filter-btn bg-red-700 hover:bg-red-600 text-white shadow-lg shadow-red-600/30"
    >Hapus Akun</button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        
        {{-- Style modal agar gelap --}}
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-[#2a2a2a] border border-[#3a3a3a] rounded-lg">
            @csrf
            @method('delete')

            <h2 class="claude-title text-xl text-white">
                Apakah Anda yakin ingin menghapus akun Anda?
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                Setelah akun Anda dihapus, semua data akan dihapus permanen. Masukkan password Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
            </p>

            <div class="mt-6">
                <label for="password_delete" class="filter-label sr-only">Password</label>

                <input
                    id="password_delete"
                    name="password"
                    type="password"
                    class="filter-input mt-1 block w-3/4"
                    placeholder="Password"
                />

                @error('password', 'userDeletion')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                {{-- Ganti <x-secondary-button> dengan <button> yang di-style --}}
                <button type="button" class="filter-btn filter-btn-secondary" x-on:click="$dispatch('close')">
                    Batal
                </button>

                {{-- Ganti <x-danger-button> dengan <button> yang di-style --}}
                <button type="submit" class="filter-btn bg-red-700 hover:bg-red-600 text-white shadow-lg shadow-red-600/30">
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>